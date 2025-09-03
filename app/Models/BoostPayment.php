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
        'payment_proof',
        'reference',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
