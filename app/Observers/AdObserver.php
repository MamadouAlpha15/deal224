<?php

namespace App\Observers;

use App\Models\Ad;

class AdObserver
{
    public function created(Ad $ad)
    {
        if (config('boost.auto_boost')) {
            $ad->boosted_until = now()->addHours(config('boost.duration_hours'));
            $ad->save();
        }
    }
}
