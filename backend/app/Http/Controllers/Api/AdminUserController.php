<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /**
     * Display a listing of staff / users.
     */
    public function index(Request $request)
    {
        $query = User::with('role');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        $users = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $users->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'phone' => $u->phone,
                'avatar' => $u->avatar,
                'is_active' => (bool) $u->is_active,
                'is_super_admin' => $u->isSuperAdmin(),
                'role' => $u->role ? [
                    'id' => $u->role->id,
                    'name' => $u->role->name,
                    'slug' => $u->role->slug,
                    'color' => $u->role->color,
                ] : null,
                'created_at' => $u->created_at?->format('d/m/Y H:i'),
            ]),
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:50',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
            'custom_permissions' => 'nullable|array',
        ], [
            'name.required' => 'Vui lòng nhập họ tên nhân viên.',
            'email.required' => 'Vui lòng nhập email đăng nhập.',
            'email.unique' => 'Email này đã tồn tại trong hệ thống.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có tối thiểu 6 ký tự.',
            'role_id.required' => 'Vui lòng chọn vai trò (Role) cho nhân viên.',
            'role_id.exists' => 'Vai trò được chọn không hợp lệ.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role_id' => $validated['role_id'],
            'is_active' => $request->boolean('is_active', true),
            'custom_permissions' => $validated['custom_permissions'] ?? null,
        ]);

        $user->load('role');

        return response()->json([
            'success' => true,
            'message' => 'Tạo tài khoản nhân viên thành công!',
            'data' => $user,
        ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        $user = User::with('role.permissions')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => $user->avatar,
                'is_active' => (bool) $user->is_active,
                'role_id' => $user->role_id,
                'role' => $user->role,
                'custom_permissions' => $user->custom_permissions ?? [],
                'all_permissions' => $user->getAllPermissionSlugs(),
            ],
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string|max:50',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
            'custom_permissions' => 'nullable|array',
        ], [
            'name.required' => 'Vui lòng nhập họ tên nhân viên.',
            'email.required' => 'Vui lòng nhập email đăng nhập.',
            'email.unique' => 'Email này đã tồn tại trong hệ thống.',
            'password.min' => 'Mật khẩu phải có tối thiểu 6 ký tự.',
            'role_id.required' => 'Vui lòng chọn vai trò cho nhân viên.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->role_id = $validated['role_id'];

        // If editing current user, do not allow deactivating self
        if ($user->id === $request->user()->id) {
            $user->is_active = true;
        } else {
            $user->is_active = $request->boolean('is_active', true);
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if (array_key_exists('custom_permissions', $validated)) {
            $user->custom_permissions = $validated['custom_permissions'];
        }

        $user->save();
        $user->load('role');

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật tài khoản nhân viên thành công!',
            'data' => $user,
        ]);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể tự xóa tài khoản của chính mình.',
            ], 422);
        }

        if ($user->id === 1 || $user->email === 'admin@innhatnguyen.vn') {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa tài khoản Quản Trị Viên Gốc của xưởng.',
            ], 422);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa tài khoản nhân viên thành công.',
        ]);
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể tự khóa tài khoản của chính mình.',
            ], 422);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        if (!$user->is_active) {
            $user->tokens()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => $user->is_active ? 'Đã kích hoạt tài khoản nhân viên.' : 'Đã tạm khóa tài khoản nhân viên.',
            'is_active' => (bool) $user->is_active,
        ]);
    }
}

