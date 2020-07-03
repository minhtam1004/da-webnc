<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $fillable = ['sendId','sendBank','receivedId','receivedBank','amount','reason','OTPCode', 'expiresAt', 'payer'];
    protected $casts = [
        'expiresAt' => 'datetime',
    ];
    public function sender()
    {
        return $this->attributes['sender'] = $this->belongsTo('App\Account', 'sendId', 'accountNumber');
    }
    public function receiver()
    {
        return $this->attributes['receiver'] = $this->belongsTo('App\Account', 'receivedId', 'accountNumber');
    }
    public function Bank()
    {
        return $this->belongsTo('App\Bank', 'receivedBank', 'id');
    }
}
