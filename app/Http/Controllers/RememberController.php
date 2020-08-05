<?php

namespace App\Http\Controllers;

use App\Account;
use App\Bank;
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
            'bankId' => 'max:255'
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error' => 'Parameter error'], 422);
        }
        if($request->bankId)
        {
            $bank = Bank::find($request->bankId);
            if(!$bank)
            {
                return response()->json(['error' => 'Bank not exist'], 422);
            }
            $req = new Request(['id'=> $request->accountId, 'bankId'=>$request->bankId]);
            $res = app('App\Http\Controllers\BankController')->viewuser($req);
            if($res->status() !== 200) return response()->json(['error' => 'user not exist'], 404);
            $name =  $res->getData()->result->name;
        }
        else {
            $acc = Account::where('accountNumber', $request->accountId)->first();
            if(!$acc)
            {
                return response()->json(['error' => 'account do not exist'], 404);
            }
            $name = $acc->user->name;
        }
        if(!$request->name) 
        {
            $request->merge(['name'=> $name]);
        }
        $request->merge(['ownerId' => auth('api')->user()->id]);
        $r = RememberList::updateOrCreate(['ownerId'=>$request->ownerId,'accountId'=>$request->accountId,'bankId'=>$request->bankId],$request->all());
        return response()->json($r);
    }
}
