<?php

namespace App\Http\Controllers;

use App\Account;
use App\DebtList;
use App\Mail\OTPMail;
use App\Notifications\DebtNotification;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

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
    public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'otherId' => 'required|max:255',
            'debt' => 'required|numeric|min:10000|max:1000000000',
            'note' => 'max:200'
        ]);
        $acc = Account::where('accountNumber',$request->otherId)->first();
        if(!$acc)
        {
            return response()->json(['error'=>'account not exist'],404);
        }
        $acc1 = auth('api')->user()->account;
        if(!$acc1)
        {
            return response()->json(['error'=>'do not have account']);
        }
        $request->merge(['ownerId'=>$acc1->accountNumber]);
        $user = $acc->user;
        $user->notify(new DebtNotification(['owner'=>$acc1->user,'note'=>$request->note,]));

        // Mail::to($acc->user->email)->send(new OTPMail('121212'));
        $debt = DebtList::create($request->all());
        return $debt;
    }
}
