<?php

namespace App\Http\Traits;

use App\Models\User;

trait UserTrait
{
    public function getUser($user_id)
    {
        return User::where('id', $user_id)->first();
    }
}
