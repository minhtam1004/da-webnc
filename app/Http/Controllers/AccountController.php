<?php

namespace App\Http\Controllers;

use App\Account;
use App\Transfer;
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
    public function store(Request $request)
    {
        //
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
        if(!$account) return response()->json(['error' => 'account doest exist'],404);
        $user = $account->user;
        return  response()->json(['name'=> $user->name, 'email' => $user->email],200);
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
        return response()->json(['message'=>'recharge has been added'],201);
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
