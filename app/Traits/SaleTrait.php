<?php

namespace App\Traits;

use App\Models\CashMovement;

trait SaleTrait
{
    public function addCashMovement($day_id, $type, $concept, $amount, $payment_method, $user_id)
    {
        return CashMovement::create([
            'day_id' => $day_id,
            'type' => $type,
            'concept' => $concept,
            'amount' => $amount,
            'payment_method' => $payment_method,
            'user_id' => $user_id,
        ]);
    }
}
