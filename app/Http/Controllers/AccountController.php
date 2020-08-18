<?php

namespace App\Http\Controllers;

use App\Account;
use App\Transfer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($id ,Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:50000',
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error' => 'Parameter error'], 422);
        }
        $user = User::find($id);
        $authUser = auth('api')->user();
        if(!$user)
        {
            return response()->json(['error'=>'user does not exist'],404);
        }
        if($authUser->roleId > 2 || $authUser->roleId > $user->roleId )
        {
            return response()->json(['error'>'do not have permission'],403);
        }
        $rand = $id + 1134567890;
        while (Account::where('accountNumber', $rand)->first()) {
            $rand = ($rand + 200) % 10000000000;
        }
        $data = ['userId' => $id, 'accountNumber' => $rand, 'excess' => $request->amount];
        $acc = Account::create($data);
        return response()->json($acc);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $account = Account::where('accountNumber',$id)->first();
        if(!$account) return response()->json(['error' => 'account doest exist'],204);
        $user = $account->user;
        if(!auth('api')->check())
        {
            return response()->json(['name'=> $user->name, 'email' => $user->email],200);
        }
        return response()->json($account);
    }
    public function recharge($id, Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:50000',
            'reason' => 'required|max:255'
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error' => 'Parameter error'], 422);
        }
        $acc = Account::where('accountNumber', $id)->first();
        if(!$acc)
        {
            return response()->json(['error' => 'account does not exist'], 404);
        }
        $acc->excess += $request->amount;
        $acc->save();
        $data = ['sendId'=>auth('api')->user()->id,'receivedId'=>$acc->accountNumber,'amount'=>$request->amount,'reason'=>$request->reason];
        $transfer = Transfer::create($data);
        $transfer->isConfirm = true;
        $transfer->save();
        return response()->json(['message'=>'recharge has been added'],200);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function edit(Account $account)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account)
    {
        //
    }
}
