<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RememberList extends Model
{
    protected $primaryKey = 'id';
    public function owner()
    {
        return $this->belongsTo('App\User','ownerId','id');
    }
    public function other()
    {
        return $this->belongsTo('App\User','otherId','id');
    }
}
