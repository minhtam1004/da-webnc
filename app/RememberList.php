<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RememberList extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = ['ownerId','accountId','name', 'bankId'];
    public function owner()
    {
        return $this->belongsTo('App\User','ownerId','id');
    }
    public function other()
    {
        return $this->belongsTo('App\User','accountId','id');
    }
    public function Bank()
    {
        return $this->belongsTo('App\Bank','bankId','id');
    }
}
