<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $primaryKey = 'id';
    protected $hidden = [
        'bank_code', 'key','api_trans','api_user','secret_key','rsa', 'created_at', 'updated_at'
    ];
    public function sendTransfer()
    {
        return $this->attributes['sendTransfer'] = $this->hasMany('App\Transfer', 'sendBank', 'id');
    }
    public function receivedTransfer()
    {
        return $this->attributes['receivedTransfer'] = $this->hasMany('App\Transfer', 'receivedBank', 'id');
    }
    
}
