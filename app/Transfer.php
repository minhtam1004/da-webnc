<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $fillable = ['sendId','sendBank','receivedId','receivedBank','amount','reason','OTPCode', 'expiresAt', 'payer', 'creator','isDebt'];
    protected $hidden = [
        'OTPCode', 'expiresAt', 'isConfirm'
    ];

    protected $casts = [
        'expiresAt' => 'datetime',
    ];
    public function sender()
    {
        return $this->attributes['sender'] = $this->belongsTo('App\Account', 'sendId', 'accountNumber');
    }
    public function senderUser()
    {
        return $this->attributes['senderUser'] = $this->belongsTo('App\User');
    }
    public function receiverUser()
    {
        return $this->attributes['receiverUser'] = $this->belongsTo('App\User');
    }
    public function receiver()
    {
        return $this->attributes['receiver'] = $this->belongsTo('App\Account', 'receivedId', 'accountNumber');
    }
    public function SendBank()
    {
        return $this->attributes['SendBank'] = $this->belongsTo('App\Bank', 'sendBank', 'id');
    }
    public function ReceivedBank()
    {
        return $this->attributes['ReceivedBank'] = $this->belongsTo('App\Bank', 'receivedBank', 'id');
    }

}
