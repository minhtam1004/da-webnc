<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $primaryKey = 'id';
    public function transfer()
    {
        return $this->attributes['transfer'] = $this->hasMany('App\Transfer', 'receivedBank', 'id');
    }
}
