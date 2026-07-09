<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /** Roles that must not be deleted or stripped of access. */
    private const PROTECTED_ROLES = ['Admin'];

    public function index(Request $request)
    {
        abort_unless($request->user()->can('view roles'), 403);

        $roles = Role::withCount(['users', 'permissions'])->orderBy('name')->get();

        return view('roles.index', compact('roles'));
    }

    public function create(Request $request)
    {
        abort_unless($request->user()->can('create roles'), 403);

        return view('roles.create', [
            'role'             => new Role(),
            'groupedPermissions' => $this->groupedPermissions(),
            'rolePermissions'  => [],
        ]);
    }

    public function store(StoreRoleRequest $request)
    {
        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('roles.index')->with('success', 'Role created.');
    }

    public function edit(Request $request, Role $role)
    {
        abort_unless($request->user()->can('update roles'), 403);

        return view('roles.edit', [
            'role'               => $role,
            'groupedPermissions' => $this->groupedPermissions(),
            'rolePermissions'    => $role->permissions->pluck('name')->all(),
            'isProtected'        => in_array($role->name, self::PROTECTED_ROLES, true),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        // Protected roles (Admin) always keep every permission — prevents lockout.
        if (in_array($role->name, self::PROTECTED_ROLES, true)) {
            $role->syncPermissions(Permission::all());

            return redirect()->route('roles.index')->with('success', 'Protected role always has full access.');
        }

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('roles.index')->with('success', 'Role updated.');
    }

    public function destroy(Request $request, Role $role)
    {
        abort_unless($request->user()->can('delete roles'), 403);

        if (in_array($role->name, self::PROTECTED_ROLES, true)) {
            return back()->with('error', 'This role is protected and cannot be deleted.');
        }

        if ($role->users()->exists()) {
            return back()->with('error', 'Cannot delete a role that still has users. Reassign them first.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted.');
    }

    /** All permissions grouped by their subject (e.g. "Assets", "Employees"). */
    private function groupedPermissions(): Collection
    {
        $verbs = ['view ', 'create ', 'update ', 'delete ', 'transfer ', 'upload ', 'export ', 'restore ', 'force delete '];

        return Permission::orderBy('name')->get()->groupBy(function (Permission $perm) use ($verbs) {
            foreach ($verbs as $verb) {
                if (str_starts_with($perm->name, $verb)) {
                    return ucwords(substr($perm->name, strlen($verb)));
                }
            }

            return ucwords($perm->name);
        });
    }
}
