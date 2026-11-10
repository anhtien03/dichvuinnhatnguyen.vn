<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminRoleController extends Controller
{
    /**
     * Display a listing of roles with user counts and permissions.
     */
    public function index()
    {
        $roles = Role::with('permissions:id,name,slug,group,group_label')
            ->withCount('users')
            ->orderBy('is_system', 'desc')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $roles,
        ]);
    }

    /**
     * Get all available permissions grouped by category.
     */
    public function permissions()
    {
        $permissions = Permission::orderBy('id', 'asc')->get();

        $grouped = [];
        foreach ($permissions as $p) {
            $grouped[$p->group]['label'] = $p->group_label;
            $grouped[$p->group]['items'][] = [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'description' => $p->description,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'list' => $permissions,
                'grouped' => $grouped,
            ],
        ]);
    }

    /**
     * Update permissions for a specific role.
     */
    public function updatePermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        if ($role->slug === 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Vai trò Super Admin mặc định luôn có 100% tất cả các quyền, không thể giới hạn.',
            ], 422);
        }

        $request->validate([
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($request->permission_ids);

        return response()->json([
            'success' => true,
            'message' => "Đã cập nhật danh sách quyền cho vai trò [{$role->name}] thành công!",
            'data' => $role->load('permissions'),
        ]);
    }

    /**
     * Store a newly created custom role.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|unique:roles,slug',
            'color' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ], [
            'name.required' => 'Vui lòng nhập tên vai trò mới.',
            'slug.unique' => 'Mã vai trò này đã tồn tại.',
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['name'], '_');

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'color' => $validated['color'] ?? 'blue',
            'description' => $validated['description'] ?? null,
            'is_system' => false,
        ]);

        if (!empty($validated['permission_ids'])) {
            $role->permissions()->sync($validated['permission_ids']);
        }

        return response()->json([
            'success' => true,
            'message' => "Tạo vai trò [{$role->name}] thành công!",
            'data' => $role->load('permissions'),
        ], 201);
    }

    /**
     * Update role details.
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $role->name = $validated['name'];
        if (isset($validated['color'])) $role->color = $validated['color'];
        if (isset($validated['description'])) $role->description = $validated['description'];
        $role->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin vai trò thành công!',
            'data' => $role,
        ]);
    }

    /**
     * Delete a custom role.
     */
    public function destroy($id)
    {
        $role = Role::withCount('users')->findOrFail($id);

        if ($role->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa vai trò hệ thống mặc định.',
            ], 422);
        }

        if ($role->users_count > 0) {
            return response()->json([
                'success' => false,
                'message' => "Không thể xóa vai trò này vì đang có {$role->users_count} nhân viên được gán vai trò này. Vui lòng chuyển vai trò cho nhân viên trước.",
            ], 422);
        }

        $role->permissions()->detach();
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa vai trò thành công.',
        ]);
    }
}

