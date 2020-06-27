<?php

namespace App\Http\Controllers;

use App\DebtList;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    public function show($id)
    {
        $debt = DebtList::find($id);
        if(!$debt)
        {
            return response()->json(['error' => 'debt is not exist'],404);
        }
        return response()->json($debt,200);
    }
    public function index()
    {
        $debt = auth('api')->user()->debtList;
        return response()->json($debt,200);
    }
}
