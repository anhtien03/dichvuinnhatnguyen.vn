<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * Get list of folders and uploaded media files in storage
     */
    public function index(Request $request)
    {
        $disk = Storage::disk('public');
        
        // Ensure root products directory exists
        if (!$disk->exists('products')) {
            $disk->makeDirectory('products');
        }

        // Determine current working folder (sanitized)
        $rawFolder = $request->query('folder', 'products');
        $currentFolder = trim(str_replace(['..', '\\'], ['', '/'], $rawFolder), '/');
        
        if (empty($currentFolder)) {
            $currentFolder = 'products';
        }

        if (!$disk->exists($currentFolder)) {
            $disk->makeDirectory($currentFolder);
        }

        // 1. Get Subdirectories in this folder
        $subDirs = $disk->directories($currentFolder);
        $foldersList = [];
        foreach ($subDirs as $dir) {
            $dirName = basename($dir);
            $imageCount = count(array_filter($disk->files($dir), function ($file) use ($disk) {
                return str_starts_with($disk->mimeType($file) ?: '', 'image/');
            }));

            $foldersList[] = [
                'name' => $dirName,
                'path' => $dir,
                'items_count' => $imageCount,
            ];
        }

        // Sort folders alphabetically
        usort($foldersList, fn($a, $b) => strcasecmp($a['name'], $b['name']));

        // 2. Get Files in this folder
        $files = $disk->files($currentFolder);
        $mediaList = [];

        foreach ($files as $filePath) {
            $mime = $disk->mimeType($filePath) ?: 'image/jpeg';
            if (str_starts_with($mime, 'image/')) {
                $filename = basename($filePath);
                $width = null;
                $height = null;
                try {
                    $fullPath = $disk->path($filePath);
                    if (file_exists($fullPath)) {
                        $info = @getimagesize($fullPath);
                        if ($info && !empty($info[0]) && !empty($info[1])) {
                            $width = (int) $info[0];
                            $height = (int) $info[1];
                        }
                    }
                } catch (\Throwable $e) {}

                $mediaList[] = [
                    'id' => md5($filePath),
                    'name' => $filename,
                    'url' => '/storage/' . $filePath,
                    'path' => $filePath,
                    'folder' => $currentFolder,
                    'size' => $disk->size($filePath),
                    'mime' => $mime,
                    'width' => $width,
                    'height' => $height,
                    'updated_at' => $disk->lastModified($filePath),
                ];
            }
        }

        // Sort newest first
        usort($mediaList, fn($a, $b) => $b['updated_at'] <=> $a['updated_at']);

        // Stock sample images only shown in root 'products' if no uploaded images yet
        if ($currentFolder === 'products' && count($mediaList) === 0) {
            $stockImages = [
                [
                    'id' => 'sample-1',
                    'name' => 'in-hop-giay-kraft-cao-cap.jpg',
                    'url' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=800&q=80',
                    'size' => 145000,
                    'mime' => 'image/jpeg',
                    'width' => 1200,
                    'height' => 800,
                    'is_stock' => true
                ],
                [
                    'id' => 'sample-2',
                    'name' => 'in-tui-giay-quai-du-sang-trong.jpg',
                    'url' => 'https://images.unsplash.com/photo-1589365278144-c9e705f843ba?auto=format&fit=crop&w=800&q=80',
                    'size' => 182000,
                    'mime' => 'image/jpeg',
                    'width' => 1200,
                    'height' => 800,
                    'is_stock' => true
                ],
                [
                    'id' => 'sample-3',
                    'name' => 'in-tem-nhan-decal-cuon.jpg',
                    'url' => 'https://images.unsplash.com/photo-1589939705384-5185137a7f0f?auto=format&fit=crop&w=800&q=80',
                    'size' => 165000,
                    'mime' => 'image/jpeg',
                    'width' => 1000,
                    'height' => 1000,
                    'is_stock' => true
                ],
                [
                    'id' => 'sample-4',
                    'name' => 'in-catalogue-offset-dep.jpg',
                    'url' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=800&q=80',
                    'size' => 195000,
                    'mime' => 'image/jpeg',
                    'width' => 1200,
                    'height' => 800,
                    'is_stock' => true
                ],
                [
                    'id' => 'sample-5',
                    'name' => 'logo-nhat-nguyen-icon.png',
                    'url' => '/logo-icon.png',
                    'size' => 56000,
                    'mime' => 'image/png',
                    'width' => 512,
                    'height' => 512,
                    'is_stock' => true
                ]
            ];
            $mediaList = $stockImages;
        }

        return response()->json([
            'success' => true,
            'current_folder' => $currentFolder,
            'is_root' => $currentFolder === 'products',
            'parent_folder' => $currentFolder !== 'products' ? dirname($currentFolder) : null,
            'folders' => $foldersList,
            'data' => $mediaList,
            'total_files' => count($mediaList),
        ]);
    }

    /**
     * Create a new folder
     */
    public function createFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'parent_folder' => 'nullable|string',
        ]);

        $disk = Storage::disk('public');
        
        $rawParent = $request->input('parent_folder', 'products');
        $parent = trim(str_replace(['..', '\\'], ['', '/'], $rawParent), '/');
        if (empty($parent)) {
            $parent = 'products';
        }

        // Clean folder name to slug
        $folderName = Str::slug($request->name);
        if (empty($folderName)) {
            return response()->json([
                'success' => false,
                'message' => 'Tên thư mục không hợp lệ',
            ], 422);
        }

        $newFolderPath = $parent . '/' . $folderName;

        if ($disk->exists($newFolderPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Thư mục này đã tồn tại',
            ], 422);
        }

        $disk->makeDirectory($newFolderPath);

        return response()->json([
            'success' => true,
            'message' => 'Đã tạo thư mục thành công',
            'data' => [
                'name' => $folderName,
                'path' => $newFolderPath,
                'items_count' => 0,
            ],
        ], 201);
    }

    /**
     * Delete a folder and its contents
     */
    public function deleteFolder(Request $request)
    {
        $request->validate([
            'folder' => 'required|string',
        ]);

        $disk = Storage::disk('public');
        $rawFolder = trim(str_replace(['..', '\\'], ['', '/'], $request->folder), '/');

        // Cannot delete root
        if ($rawFolder === 'products' || !str_starts_with($rawFolder, 'products/')) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa thư mục gốc của hệ thống',
            ], 403);
        }

        if ($disk->exists($rawFolder)) {
            $disk->deleteDirectory($rawFolder);
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa thư mục và tất cả nội dung bên trong',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy thư mục',
        ], 404);
    }

    /**
     * Upload an image to a specific folder
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|image|mimes:jpeg,png,jpg,webp,svg,gif,avif,jfif|max:20480', // Max 20MB
            'folder' => 'nullable|string',
        ]);

        $rawFolder = $request->input('folder', 'products');
        $targetFolder = trim(str_replace(['..', '\\'], ['', '/'], $rawFolder), '/');
        if (empty($targetFolder)) {
            $targetFolder = 'products';
        }

        $disk = Storage::disk('public');
        if (!$disk->exists($targetFolder)) {
            $disk->makeDirectory($targetFolder);
        }

        $file = $request->file('file');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension() ?: 'webp';
        
        $cleanSlug = Str::slug($originalName);
        $uniqueFilename = $cleanSlug . '-' . time() . '.' . $extension;

        // Store to specified folder
        $path = $file->storeAs($targetFolder, $uniqueFilename, 'public');
        $url = '/storage/' . $path;

        $width = null;
        $height = null;
        try {
            $info = @getimagesize($file->getRealPath());
            if ($info && !empty($info[0]) && !empty($info[1])) {
                $width = (int) $info[0];
                $height = (int) $info[1];
            }
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'message' => 'Tải ảnh lên thành công',
            'data' => [
                'id' => md5($path),
                'name' => $uniqueFilename,
                'url' => $url,
                'path' => $path,
                'folder' => $targetFolder,
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'width' => $width,
                'height' => $height,
                'updated_at' => time(),
            ],
        ], 201);
    }

    /**
     * Delete an uploaded image
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $disk = Storage::disk('public');
        $rawPath = $request->path;

        $cleanPath = ltrim(str_replace('/storage/', '', $rawPath), '/');

        if (str_contains($cleanPath, '..') || empty($cleanPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Đường dẫn tệp không hợp lệ',
            ], 400);
        }

        if ($disk->exists($cleanPath)) {
            $disk->delete($cleanPath);
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa hình ảnh khỏi thư viện',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy tệp để xóa',
        ], 404);
    }
}
