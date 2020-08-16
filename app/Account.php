<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = [
        'userId', 'accountNumber', 'excess', 'type'
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
    public function owndebts()
    {
        return $this->attributes['owndebts'] = $this->hasMany('App\DebtList', 'ownerId', 'accountNumber');
    }
    public function otherdebts()
    {
        return $this->attributes['otherdebts'] = $this->hasMany('App\DebtList', 'otherId', 'accountNumber');
    }

}
