<?php

namespace App\Http\Traits;

use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\Rol;
use App\Models\RolHasPermission;

trait RolTrait
{
    public function listRol($type)
    {
        if ($type == 1) {
            $roles = Rol::where([
                ['rolG_id', 1],
                ['state', 1]
            ])->whereIn('id', [1, 2])->get();
            return $roles;
        }

        if ($type == 2) {
            $roles = Rol::where([
                ['rolG_id', 2],
                ['state', 1]
            ])->whereIn('id', [1, 3])->get();
            return $roles;
        }
    }
    public function showRol($rol_id)
    {
        return Rol::where('id', $rol_id)->whereNotIn('id', [1, 3])->first();
    }
    public function createRol($name, $rolG_id)
    {
        return Rol::create([
            'name' => $name,
            'state' => 1,
            'rolG_id' => $rolG_id
        ]);
    }

    public function permissionRol($rol_id)
    {
        return RolHasPermission::where('rol_id', $rol_id)->pluck('permission_id')->all();
    }
    public function permissionGroupRolG($rolG_id)
    {
        return PermissionGroup::where('rolG_id', $rolG_id)->get()->toArray();
    }
    public function permissionsRolGroup($permissionG_id)
    {
        return Permission::where('state', 1)->whereIn('permissionG_id', $permissionG_id)->get();
    }
}
