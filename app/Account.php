<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = [
        'userId', 'accountNumber', 'excess',
    ];
    protected $hidden = [
        'created_at','updated_at'
    ];

    public function user()
    {
        return $this->attributes['user'] = $this->belongsTo('App\User','userId');
    }
    public function receivedTransfer()
    {
        return $this->attributes['receivedTransfer'] = $this->hasMany('App\Transfer', 'receivedId', 'accountNumber');
    }

    public function sendTransfer()
    {
        return $this->attributes['sendTransfer'] = $this->hasMany('App\Transfer', 'sendId', 'accountNumber');
    }
}
