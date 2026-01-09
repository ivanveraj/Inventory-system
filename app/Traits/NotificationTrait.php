<?php

namespace App\Traits;

use Filament\Notifications\Notification;

trait NotificationTrait
{
    public static function customNotification($type = 'success', $title = null, $message = null, $user = null)
    {
        $notification = Notification::make()->title($title)->body($message);

        switch ($type) {
            case 'success':
                $notification->success();
                $title = $title ?? 'Action Completed';
                $message = $message ?? 'The action was completed successfully.';
                break;
            case 'error':
                $notification->danger();
                $title = $title ?? 'Action Failed';
                $message = $message ?? 'An error occurred while processing the request.';
                break;
            case 'warning':
                $notification->warning();
                $title = $title ?? 'Warning';
                $message = $message ?? 'There might be an issue that needs your attention.';
                break;
            case 'info':
                $notification->info();
                $title = $title ?? 'Information';
                $message = $message ?? 'Here is some important information for you.';
                break;
            default:
                $notification->info();
                $title = $title ?? 'Notification';
                $message = $message ?? 'You have received a new notification.';
                break;
        }

        if ($user) {
            $notification->sendToDatabase($user);
        } else {
            $notification->send();
        }
    }
    
    public function modalAction($action, $id)
    {
        $this->dispatch($action, id: $id);
    }
}
