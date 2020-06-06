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
        return $this->belongsTo('App\User','userId','id');
    }
}
