<?php

namespace App\Http\Controllers\Rol;

use App\Http\Controllers\Controller;
use App\Http\Traits\RolTrait;
use App\Models\Permission;
use App\Models\Rol;
use App\Models\RolHasPermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class RolController extends Controller
{
    use RolTrait;
    public function index()
    {
        return view('rols.index_rol');
    }

    public function datatable(Request $rq)
    {
        $roles = Rol::whereNot('id', 1)->orderBy('created_at', 'DESC')->get();
        return DataTables::of($roles)
            ->addColumn('name', function ($rol) {
                return $rol->name;
            })
            ->addColumn('users_associated', function ($rol) {
                return '<span class="badge bg-primary">' . TotalUserAssociated($rol->id) . '</span>';
            })
            ->addColumn('state', function ($user) {
                $state = $user->state == 1 ? '  <span
                class="badge rounded-pill bg-success">Activo</span>' : ' <span
                class="badge rounded-pill bg-danger">Inactivo</span>';
                return $state;
            })
            ->addColumn('permissions', function ($rol) {
                $associatedP = '<button onclick="permissionsRol(' . $rol->id . ')" type="button" class="btn btn-primary">
                <i class="fa fa-address-book"></i> Permisos</button>';
                return $associatedP;
            })
            ->addColumn('actions', function ($rol) {
                $Edit =  '<button onclick="editRol(' . $rol->id . ')" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="Editar">
                <i class="fas fa-edit"></i></button>';
                $msj = $rol->state == 1 ? 'Archivar' : 'Activar';
                $icon = $rol->state == 1 ? '<i class="fas fa-trash"></i>' : '<i class="fas fa-sync-alt"></i>';
                $Archive =  '<button onclick="archiveRol(' . $rol->id . ',' . $rol->state . ')" class="btn btn-primary btn-sm ml-2" data-toggle="tooltip" data-placement="top" title="' . $msj . '">' . $icon . '</button>';

                return "<center>$Edit $Archive</center>";
            })
            ->rawColumns(['name', 'users_associated', 'permissions', 'state', 'actions'])->make(true);
    }
    public function create(Request $rq)
    {
        return view('rols.create_rol');
    }

    public function store(Request $rq)
    {
        $rq->validate([
            'name' => 'required|string',
        ]);

        $this->createRol($rq->name, 1);
        return AccionCorrecta('', '');
    }

    public function show($rol_id)
    {
        $rol = $this->showRol($rol_id);
        if (is_null($rol)) {
            return AccionIncorrecta('', '');
        }

        return view('rols.edit_rol', compact('rol'));
    }

    public function update(Request $rq)
    {
        $rq->validate([
            'id' => 'required',
            'name' => 'required|string', Rule::unique('rols', 'name')->ignore($rq->id)
        ]);

        $rol = $this->showRol($rq->id);
        if (is_null($rol)) {
            return AccionIncorrecta('', '');
        }

        $rol->name = $rq->name;
        $rol->save();
        return AccionCorrecta('', '');
    }

    public function archive(Request $rq)
    {
        $rol = $this->showRol($rq->rol_id);
        if (is_null($rol)) {
            return AccionIncorrecta('', 'No se puede eliminar este rol');
        }

        $rol->state = $rol->state == 0 ? 1 : 0;
        $rol->save();

        User::where('rol_id', $rol->id)->update([
            'rol_id' => 3
        ]);

        return AccionCorrecta('', '');
    }

    public function managePermissionsRol(Request $rq)
    {
        $rol = $this->showRol($rq->rol_id);

        if (is_null($rol)) {
            return AccionIncorrecta('', '');
        }

        $rolPerms =  $this->permissionRol($rol->id);
        $array = [];
        $permissionGroups = $this->permissionGroupRolG($rol->rolG_id);
        for ($i = 0; $i < count($permissionGroups); $i++) {
            $permissions = Permission::where('permissionG_id', $permissionGroups[$i]['id'])->get()->toArray();
            $array[] = [
                'permissionG' => $permissionGroups[$i]['id'],
                'namePermissionG' => $permissionGroups[$i]['name'],
                'permissions' => $permissions
            ];
        }
        return view('rols.manage_rol', compact('rol', 'rolPerms', 'array'));
    }

    public function updatePermissionRol(Request $rq)
    {
        $rol = $this->showRol($rq->rol_id);

        if (is_null($rol)) {
            return AccionIncorrecta('', '');
        }

        $permissions = Permission::whereIn('permissionG_id', function ($query) use ($rol) {
            $query->select('id')->from('permission_groups')->where('rolG_id', $rol->rolG_id);
        })->get();

        RolHasPermission::where('rol_id', $rol->id)->delete();
        foreach ($permissions as $permission) {
            if ($rq->has('check_' . $permission->id)) {
                RolHasPermission::create([
                    'rol_id' => $rol->id,
                    'permission_id' => $permission->id
                ]);
            }
        }

        return AccionCorrecta('', '');
    }
}
