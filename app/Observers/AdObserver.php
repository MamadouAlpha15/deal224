<?php

namespace App\Observers;

use App\Models\Ad;

class AdObserver
{
    public function created(Ad $ad)
    {
        // Convertit correctement la valeur de .env en booléen
        $autoBoost = filter_var(config('boost.auto_boost'), FILTER_VALIDATE_BOOLEAN);

        if ($autoBoost) {
            $ad->boosted_until = now()->addHours(config('boost.duration_hours'));
            $ad->save();
        }
    }
}
