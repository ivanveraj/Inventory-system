<?php

namespace App\Http\Traits;

use App\Models\Permission;

trait GeneralTrait
{
    public function createPermission($id, $name, $permissionG_id)
    {
        return Permission::create([
            'id' => $id,
            'name' => $name,
            'state' => 1,
            'permissionG_id' => $permissionG_id
        ]);
    }
}
