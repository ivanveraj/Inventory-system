<?php

namespace App\Http\Traits;

use Filament\Notifications\Notification;

trait NotificationTrait
{
    public static function customNotification($tipo = 'success', $titulo = null, $mensaje = null)
    {
        if ($tipo == 'success') {
            $titulo = $titulo ?? 'Acción completada';
            $mensaje = $mensaje ?? 'La acción se completó exitosamente.';
        } else {
            $titulo = $titulo ?? 'Acción no completada';
            $mensaje = $mensaje ?? 'La acción no se completó exitosamente.';
        }

        Notification::make()
            ->color($tipo)
            ->title($titulo)
            ->body($mensaje)
            ->send();
    }

    public function modalAction($action, $id)
    {
        $this->dispatch($action, id: $id);
    }
}
