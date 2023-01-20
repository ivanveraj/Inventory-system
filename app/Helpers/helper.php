<?php

use App\Models\Day;
use App\Models\HistoryTable;
use App\Models\Rol;
use App\Models\RolHasPermission;
use App\Models\User;
use App\Http\Traits\TableTrait;
use App\Models\Table;

function validatePermission(User $user, $id_permission)
{
    if ($user->rol_id == 1) {
        return true;
    }
    return session()->has('permissions') ? in_array($id_permission, session()->get('permissions')) : false;
}

function VerifyPermissions($LogUser, $Permissions)
{
    $array = [];
    foreach ($Permissions as $perm) {
        $array[$perm] = validatePermission($LogUser, $perm);
    }
    return $array;
}

function PermissionsRol($rol)
{
    return RolHasPermission::where('rol_id', $rol)->pluck('permission_id')->toArray();
}

function AccionCorrecta($title, $message, $retorno = 1, $data = [])
{
    $type = 'success';
    $title = $title == '' ? 'Accion realizada' : $title;
    $message = $message == '' ? 'La accion se realizo correctamente' : $message;
    if ($retorno == 2) {
        return ['status' => 1, 'type' => $type, 'title' => $title, 'message' => $message, 'data' => $data];
    }
    return response()->json(['status' => 1, 'type' => $type, 'title' => $title, 'message' => $message, 'data' => $data]);
}

function AccionIncorrecta($title, $message, $retorno = 1, $data = [])
{
    $type = 'error';
    $title = $title == '' ? 'Accion no realizada' : $title;
    $message = $message == '' ? 'La accion no se realizo, hubo un error' : $message;
    if ($retorno == 2) {
        return ['status' => 0, 'type' => $type, 'title' => $title, 'message' => $message, 'data' => $data];
    }
    return response()->json(['status' => 0, 'type' => $type, 'title' => $title, 'message' => $message, 'data' => $data]);
}

function TotalUserAssociated($rol)
{
    $role = Rol::where('id', $rol)->where('state', 1)->first();
    return is_null($role) ? 0 : User::where('rol_id', $role->id)->count();
}

function completeNameUser($user)
{
    return $user->name . ' ' . $user->surname;
}

function formatMoney($num)
{
    $num = doubleval($num);
    return number_format($num);
}

function DateDifference($date1, $date2)
{
    $minutes = (strtotime($date1) - strtotime($date2)) / 60;
    return floor($minutes);
}
function DateDifferenceSeconds($date1, $date2)
{
    $minutes = (strtotime($date1) - strtotime($date2)) / 60;
    $seconds = $minutes * 60;
    return $seconds;
}

function getDay()
{
    $day = Day::whereNull('finish_day')->orderBy('created_at', 'DESC')->first();
    if (is_null($day)) {
        $day = Day::create(['total' => 0]);
        $tables = Table::where('state', 1)->orderBy('id', 'ASC')->get();
        foreach ($tables as $table) {
            HistoryTable::create(['day_id' => $day->id, 'table_id' => $table->id, 'time' => 0]);
        }
    }
    return $day;
}
function getExistDay()
{
    $day = Day::whereNull('finish_day')->orderBy('created_at', 'DESC')->first();
    if (is_null($day)) {
        return false;
    }
    return true;
}

function getDayCurrent()
{
    return Day::whereNull('finish_day')->orderBy('created_at', 'DESC')->first();
}
function getLastDay()
{
    return Day::whereNull('created_at', date('Y-m-d'))->first();
}
