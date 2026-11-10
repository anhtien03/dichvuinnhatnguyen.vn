<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QuoteRequestController extends Controller
{
    /**
     * Submit a quote request (Public)
     */
    public function store(Request $request)
    {
        // 1. Honeypot Anti-Spam Check: If invisible honeypot field is filled, bot detected!
        if ($request->filled('company_fax') || $request->filled('website_verify')) {
            // Return fake success so bot stops retrying without writing any file or DB record
            return response()->json([
                'success' => true,
                'message' => 'Yêu cầu báo giá của bạn đã được tiếp nhận thành công!',
                'data' => [
                    'code' => 'NN-' . date('Ym') . '-BOT',
                    'customer_name' => 'Protected'
                ]
            ], 200);
        }

        // 2. Strict Input & File Whitelist Validation
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'product_id' => 'nullable|exists:products,id',
            'product_name' => 'nullable|string|max:255',
            'quantity' => 'nullable|string|max:100',
            'specs_description' => 'nullable|string',
            'file' => 'nullable|file|max:51200|mimes:pdf,ai,psd,cdr,zip,rar,7z,jpg,jpeg,png,tiff',
        ], [
            'file.mimes' => 'Tệp tin không đúng định dạng in ấn hợp lệ. Chỉ chấp nhận: PDF, AI, PSD, CDR, ZIP, RAR, 7Z, JPG, PNG, TIFF.',
            'file.max' => 'Dung lượng file vượt quá giới hạn 50MB.',
        ]);

        // 3. Security Check: Explicitly reject any executable or scripting extensions
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $ext = strtolower($file->getClientOriginalExtension());
            $banned = ['php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phps', 'cgi', 'pl', 'exe', 'sh', 'bat', 'com', 'jar', 'py', 'vbs', 'scr', 'js', 'html', 'htm'];
            if (in_array($ext, $banned)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tệp tin bị từ chối vì lý do bảo mật máy chủ.'
                ], 422);
            }
        }

        // Generate Code: NN-YYYYMM-XXXX
        $prefix = 'NN-' . date('Ym') . '-';
        $lastQuote = QuoteRequest::where('code', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastQuote) {
            $parts = explode('-', $lastQuote->code);
            $lastNum = (int) end($parts);
            $nextNumber = $lastNum + 1;
        }
        $code = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $filePath = null;
        $fileName = null;
        $fileSize = null;
        $fileType = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileType = $file->getClientOriginalExtension();
            $storedPath = $file->store('quotes', 'public');
            $filePath = '/storage/' . $storedPath;
        }

        $quote = QuoteRequest::create([
            'code' => $code,
            'customer_name' => $validated['customer_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'company' => $validated['company'] ?? null,
            'product_id' => $validated['product_id'] ?? null,
            'product_name' => $validated['product_name'] ?? null,
            'quantity' => $validated['quantity'] ?? null,
            'specs_description' => $validated['specs_description'] ?? null,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'file_type' => $fileType,
            'status' => 'pending',
        ]);

        // Send email notifications safely (Admin alert + Customer confirmation)
        try {
            $adminEmail = \App\Models\SiteSetting::get('email', 'nhatnguyenprinting@gmail.com');
            if (!empty($adminEmail)) {
                \Illuminate\Support\Facades\Mail::to($adminEmail)->send(new \App\Mail\NewQuoteRequestAdminMail($quote));
            }
            if (!empty($quote->email)) {
                \Illuminate\Support\Facades\Mail::to($quote->email)->send(new \App\Mail\QuoteConfirmationCustomerMail($quote));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Quote notification email error: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Gửi yêu cầu báo giá thành công! Xưởng in Nhật Nguyên sẽ liên hệ bạn ngay.',
            'data' => [
                'id' => $quote->id,
                'code' => $quote->code,
                'customer_name' => $quote->customer_name,
                'phone' => $quote->phone,
                'created_at' => $quote->created_at,
            ],
        ], 201);
    }

    /**
     * List quotes for admin
     */
    public function index(Request $request)
    {
        $query = QuoteRequest::with('product')->orderBy('id', 'desc');

        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 15);
        $quotes = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $quotes,
        ]);
    }

    /**
     * View single quote
     */
    public function show($id)
    {
        $quote = QuoteRequest::with('product')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $quote,
        ]);
    }

    /**
     * Update quote status & admin notes
     */
    public function update(Request $request, $id)
    {
        $quote = QuoteRequest::findOrFail($id);

        $validated = $request->validate([
            'status' => 'sometimes|required|in:pending,contacted,approved,completed,cancelled',
            'quoted_price' => 'nullable|numeric|min:0',
            'admin_notes' => 'nullable|string',
        ]);

        $quote->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái đơn báo giá thành công',
            'data' => $quote,
        ]);
    }

    /**
     * Delete quote
     */
    public function destroy($id)
    {
        $quote = QuoteRequest::findOrFail($id);

        if ($quote->file_path) {
            $relative = str_replace('/storage/', '', $quote->file_path);
            Storage::disk('public')->delete($relative);
        }

        $quote->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa yêu cầu báo giá',
        ]);
    }
}

