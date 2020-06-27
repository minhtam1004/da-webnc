<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $fillable = ['sendId','sendBank','receivedId','receivedBank','amount','reason','OTPCode', 'expiresAt'];
    protected $casts = [
        'expiresAt' => 'datetime',
    ];
    public function sender()
    {
        return $this->belongsTo('App\Account', 'sendId', 'id');
    }
    public function receiver()
    {
        return $this->belongsTo('App\Account', 'receivedId', 'id');
    }
    public function Bank()
    {
        return $this->belongsTo('App\Bank', 'receivedBank', 'id');
    }
}
