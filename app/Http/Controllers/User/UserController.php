<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Traits\UserTrait;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    use UserTrait;
    public function index()
    {
        return view('users.index_users');
    }

    public function list(Request $rq)
    {
        $users = User::whereNotIn('rol_id', [1])->get();

        return DataTables::of($users)
            ->addColumn('name', function ($user) {
                return '<div class="flex justify-center">
                    <div class="mr-3">
                        <img class="h-8 w-8 rounded-full object-cover" src="' . $user->profile_photo_url . '" >
                    </div>
                    <div class="flex flex-col justify-center">
                        <h6 class="mb-0 leading-normal text-sm">' . $user->name . '</h6>
                        <p class="mb-0 leading-tight text-xs text-slate-400">' . $user->email . '</p>
                    </div>
                </div>';
            })
            ->addColumn('state', function ($user) {
                $state = $user->state == 1 ? '  <span
                class="badge rounded-pill bg-success">Activo</span>' : ' <span
                class="badge rounded-pill bg-danger">Inactivo</span>';
                return $state;
            })
            ->addColumn('rol', function ($user) {
                $rol = $user->Rol;
                return is_null($rol) ? 'Sin rol' : $rol->name;
            })
            ->addColumn('actions', function ($user) {
                $Edit = '<button onclick="edit(' . $user->id . ')" class="dropdown-item">Editar</button>';
                $msj = $user->state == 1 ? 'Archivar' : 'Activar';
                $Archive =  '<button onclick="archive(' . $user->id . ',' . $user->state . ')" class="dropdown-item">' . $msj . '</button>';
                $AssignRol = '<button onclick="assignRol(' . $user->id . ')" class="dropdown-item">Asignar rol</button>';
                $dropdown = '
                    <div class="btn-group">
                        <div class="btn-group dropstart" role="group">
                            <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="visually-hidden">Toggle Dropstart</span>
                            </button>
                            <ul class="dropdown-menu">
                            ' . $Edit . '
                            ' . $Archive . '
                            ' . $AssignRol . '
                            </ul>
                        </div>
                    </div>';
                return '<center>' . $dropdown . '</center>';
            })
            ->rawColumns(['name', 'state', 'rol', 'actions'])->make(true);
    }

    public function create()
    {
        return view('users.create_user');
    }

    public function store(Request $rq)
    {
        $rq->validate([
            'name' => 'required',
            'user' => 'required|unique:users,user',
            'password' => 'required',
            'passwordC' => 'required|same:password'
        ]);

        $user = User::create([
            'name' => $rq->name,
            'rol_id' => 3,
            'state' => 1,
            'user' => $rq->user,
            'password' => Hash::make($rq->password),
            'remember_token' => time() . $rq->user,
        ]);

        return AccionCorrecta('', '');
    }

    public function show($id)
    {
        $user = $this->getUser($id);
        if (is_null($user)) {
            return AccionIncorrecta('', '');
        }
        return view('users.edit_user', compact('user'));
    }

    public function update(Request $rq)
    {
        $rq->validate([
            'id' => 'required',
            'name' => 'required',
            'user' => 'required'
        ]);

        $user = $this->getUser($rq->id);
        if (is_null($user)) {
            return AccionIncorrecta('', '');
        }

        $user->update([
            'name' => $rq->name,
            'user' => $rq->user
        ]);

        return AccionCorrecta('', '');
    }

    public function archive(Request $rq)
    {
        $user = $this->getUser($rq->id);
        if (is_null($user)) {
            return AccionIncorrecta('', '');
        }

        $user->state = $user->state == 1 ? 0 : 1;
        $user->save();

        return AccionCorrecta('', '');
    }

    public function assignRol($id_user)
    {
        $user = $this->getUser($id_user);
        if (is_null($user)) {
            return AccionIncorrecta('', '');
        }

        $rols = Rol::where('state', 1)->where('rolG_id', 1)->where('id', '!=', 1)->get();

        return view('users.assign_rol', compact('rols', 'user'));
    }

    public function changeRol(Request $rq)
    {
        $rq->validate([
            'id' => 'required|exists:users,id',
            'rol_id' => 'required|exists:rols,id'
        ]);

        if ($rq->rol_id == 1) {
            return AccionIncorrecta('', '');
        }

        $user = $this->getUser($rq->id);
        if (is_null($user)) {
            return AccionIncorrecta('', '');
        }

        $user->rol_id = $rq->rol_id;
        $user->save();

        return AccionCorrecta('', '');
    }
}
