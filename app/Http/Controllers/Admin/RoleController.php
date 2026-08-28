<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(private readonly ActivityLogger $logger) {}

    public function index(): Response
    {
        $this->requirePermission('roles.view');

        return Inertia::render('Admin/Roles/Index', [
            'roles' => Role::query()->with('permissions')->withCount('users')->get(),
            'permissionGroups' => config('permissions.groups'),
        ]);
    }

    public function store(RoleRequest $request): RedirectResponse
    {
        $this->requirePermission('roles.create');

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
        $role->syncPermissions($request->input('permissions', []));
        $this->logger->log('role.created', 'تم إنشاء دور: '.$role->name, $role);

        return back()->with('success', 'تم إنشاء الدور بنجاح.');
    }

    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        $this->requirePermission('roles.update');

        if ($role->name === 'المدير العام') {
            $role->syncPermissions(Permission::all());
        } else {
            $role->update(['name' => $request->name]);
            $role->syncPermissions($request->input('permissions', []));
        }

        $this->logger->log('role.updated', 'تم تعديل الدور والصلاحيات: '.$role->name, $role);

        return back()->with('success', 'تم تحديث الدور والصلاحيات بنجاح.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->requirePermission('roles.delete');

        if ($role->name === 'المدير العام') {
            return back()->with('error', 'لا يمكن حذف دور المدير العام.');
        }

        if ($role->users()->exists()) {
            return back()->with('error', 'لا يمكن حذف دور مرتبط بمستخدمين.');
        }

        $role->delete();
        $this->logger->log('role.deleted', 'تم حذف الدور: '.$role->name, $role);

        return back()->with('success', 'تم حذف الدور بنجاح.');
    }
}
