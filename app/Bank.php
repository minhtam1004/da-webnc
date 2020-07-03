<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $primaryKey = 'id';
    protected $hidden = [
        'bank_code', 'key','api_trans','api_user','secret_key','rsa', 'created_at', 'updated_at'
    ];
    public function transfer()
    {
        return $this->attributes['transfer'] = $this->hasMany('App\Transfer', 'receivedBank', 'id');
    }
    
}
