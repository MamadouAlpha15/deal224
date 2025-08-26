<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoostPayment extends Model
{
    protected $fillable = [
        'user_id',
        'ads_count',
        'amount',
        'start_date',
        'end_date',
        'status',
        'payement_proof',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
