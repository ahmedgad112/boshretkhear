<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(private readonly ActivityLogger $logger) {}

    public function index(Request $request): Response
    {
        $this->requirePermission('users.view');

        $users = User::query()
            ->with('roles')
            ->when($request->q, fn ($q, $term) => $q->where(function ($inner) use ($term) {
                $inner->where('name', 'like', '%'.$term.'%')->orWhere('email', 'like', '%'.$term.'%');
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'roles' => Role::query()->orderBy('name')->get(['id', 'name']),
            'filters' => $request->only('q'),
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $this->requirePermission('users.create');

        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $user->syncRoles([$request->role]);
        $this->logger->log('user.created', 'تم إضافة مستخدم: '.$user->name, $user);

        return back()->with('success', 'تم إنشاء المستخدم بنجاح.');
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $this->requirePermission('users.update');

        $payload = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->filled('password')) {
            $payload['password'] = $request->password;
        }

        $user->update($payload);
        $user->syncRoles([$request->role]);
        $this->logger->log('user.updated', 'تم تعديل المستخدم: '.$user->name, $user);

        return back()->with('success', 'تم تحديث المستخدم بنجاح.');
    }

    public function toggle(User $user): RedirectResponse
    {
        $this->requirePermission('users.update');

        if ($user->id === auth()->id()) {
            return back()->with('error', 'لا يمكنك تعطيل حسابك الحالي.');
        }

        $user->update(['is_active' => ! $user->is_active]);
        $this->logger->log('user.toggled', 'تم تغيير حالة المستخدم: '.$user->name, $user);

        return back()->with('success', $user->is_active ? 'تم تفعيل المستخدم.' : 'تم تعطيل المستخدم.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->requirePermission('users.delete');

        if ($user->id === auth()->id()) {
            return back()->with('error', 'لا يمكنك حذف حسابك الحالي.');
        }

        $user->delete();
        $this->logger->log('user.deleted', 'تم حذف المستخدم: '.$user->name, $user);

        return back()->with('success', 'تم حذف المستخدم بنجاح.');
    }
}
