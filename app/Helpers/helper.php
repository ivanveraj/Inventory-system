<?php

use App\Models\Day;
use App\Models\HistoryTable;
use App\Models\Rol;
use App\Models\RolHasPermission;
use App\Models\Table;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

function validatePermission(User $user, $id_permission)
{
    if (in_array($user->rol_id, [1, 2])) {
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

function getDay($openingBalance = 0)
{
    $day = Day::where('status', 'open')->orderBy('created_at', 'DESC')->first();
    if (is_null($day)) {
        $day = Day::create([
            'total' => 0,
            'profit' => 0,
            'opening_balance' => $openingBalance,
            'opened_at' => now(),
            'opened_by' => Auth::id(),
            'status' => 'open',
            'cash_sales' => 0,
            'card_sales' => 0,
            'transfer_sales' => 0,
            'total_sales' => 0,
            'tables_total' => 0,
            'products_total' => 0,
            'expenses' => 0,
            'withdrawals' => 0,
            'cash_left_for_next_day' => $openingBalance,
            'final_balance' => 0,
        ]);
        $tables = Table::where('state', 1)->orderBy('id', 'ASC')->get();
        foreach ($tables as $table) {
            HistoryTable::create(['day_id' => $day->id, 'table_id' => $table->id, 'time' => 0, 'total' => 0]);
        }
    }
    return $day;
}

function getLastDay2()
{
    return Day::where('status', 'closed')->orderBy('created_at', 'desc')->first();
}

function getExistDay()
{
    $day = Day::where('status', 'open')->orderBy('created_at', 'DESC')->first();
    if (is_null($day)) {
        return false;
    }
    return true;
}

function getDayCurrent()
{
    return Day::where('status', 'open')->orderBy('created_at', 'DESC')->first();
}

function getLastDay($currentDay)
{
    return Day::where('id', '!=', $currentDay->id)
        ->whereNotNull('created_at')
        ->orderBy('created_at', 'desc')
        ->first();
}

function formatMoney($num)
{
    return '$' . number_format($num);
}

function formatDate($date, $format = 'd/m/Y')
{
    return Carbon::parse($date)->translatedFormat($format);
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

if (!function_exists('getAdministrativeRoles')) {
    function getAdministrativeRoles(): array
    {
        return ['SuperAdmin', 'Administrador', 'Cajero', 'Colaborador'];
    }
}

if (!function_exists('hint_info_tooltip')) {
    /**
     * Crea un hintInfoTooltip con tooltip para componentes de Filament
     *
     * @param string $tooltip
     * @return array
     */
    function hint_info_tooltip(string $tooltip): array
    {
        return [
            'icon' => 'heroicon-o-information-circle',
            'tooltip' => $tooltip,
        ];
    }
}

// if (!function_exists('getDailyClosing')) {
//     function getDay()
//     {
//         $dailyClosing = DailyClosing::where('date', today())->first();
//         if (is_null($dailyClosing)) {
//             return DailyClosing::create(['date' => today()]);
//         }

//         return $dailyClosing;
//     }
// }

// if (!function_exists('getCategories')) {
//     function getCategories()
//     {
//         return ServicesCategory::where('status', 1)->get();
//     }
// }
