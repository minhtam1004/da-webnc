<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DebtList extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = ['ownerId','otherId','debt','ispaid','note'];
    public function owner()
    {
        return $this->belongsTo('App\Account','ownerId','accountNumber');
    }
    public function other()
    {
        return $this->belongsTo('App\Account','otherId','accountNumber');
    }
}
