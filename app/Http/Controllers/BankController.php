<?php

namespace App\Http\Controllers;

use App\Bank;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;


class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendMoney($request)
    {
        $validatedData = Validator::make($request->all(),[
            'sendId' => 'required|max:255',
            'sendBank' => 'max:255',
            'receivedId' => 'required|max:255',
            'receivedBank' => 'required|max:255',
            'amount' => 'required|numeric|min:10000|max:1000000000',
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error'=>'Parameter error'],422);
        }
        $bank = Bank::find($request->receivedBank);
        if(!$bank) return response()->json(['error'=>'bank not connected'],422);
        $user = User::find($request->sendId);
        $time = Carbon::now()->timestamp;
        //$response = Http::withHeaders(['bank-code' => $bank->bank_code,'time' => $time,'sig'=> hash('sha256',$time.$bank->secret_key),'signature-pgp' => ])
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
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function show(Bank $bank)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function edit(Bank $bank)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bank $bank)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bank $bank)
    {
        //
    }
}
