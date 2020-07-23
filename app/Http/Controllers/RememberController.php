<?php

namespace App\Http\Controllers;

use App\Account;
use App\RememberList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RememberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function show(Request $request)
    {
        $request->name = $request->name ? $request->name : '';
        $request->limit = $request->limit && $request->limit > 0 ? $request->limit : 10;
        $request->page = $request->page && $request->page > 0 ? $request->page : 1;

        $account = RememberList::where('ownerId', auth('api')->user()->id)->where('name', 'LIKE', "%{$request->name}%")->paginate($request->limit);
        return  response()->json($account, 200);
    }
    public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'max:255',
            'accountId' => 'required|max:255',
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error' => 'Parameter error'], 422);
        }
        $acc = Account::where('accountNumber', $request->accountId)->first();
        if(!$acc)
        {
            return response()->json(['error' => 'account do not exist'], 404);
        }
        if(!$request->name) 
        {
            $request->merge(['name'=> $acc->user()->name]);
        }
        $request->merge(['ownerId', auth('api')->user()->id]);
        $r = RememberList::updateOrCreate(['ownerId'=>$request->ownerId,'accountId'=>$request->accountId],$request->all());
        return response()->json($r);
    }
}
