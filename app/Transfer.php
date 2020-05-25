<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $fillable = ['sendId','sendBank','receivedId','receivedBank','amount','reason'];
}
