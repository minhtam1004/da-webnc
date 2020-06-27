<?php

namespace App\Http\Controllers;

use App\Account;
use App\Bank;
use App\Mail\OTPMail;
use App\Transfer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TransferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        auth('api')->user()->roleId === 1;
        return Transfer::where('isConfirm', true);
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
        $validatedData = Validator::make($request->all(), [
            'sendId' => 'required|max:255',
            'sendBank' => 'max:255',
            'receivedId' => 'required|max:255',
            'receivedBank' => 'max:255',
            'amount' => 'required|numeric|min:10000|max:1000000000',
            'reason' => 'required|max:200'
        ]);
        if ($validatedData->fails()) {
            return response()->json('Parameter error', 422);
        }
        $acc = null;
        $OTPCode = rand(0, 999999);
        $OTPString = str_repeat(0, 5 - floor(log10($OTPCode))) . strval($OTPCode);
        if (!$request->receivedBank) {
            $acc = Account::where('accountNumber', $request->receivedId)->first();
            if (!$acc) return response()->json(['error' => 'account doesnt exist'], 204);
            $acc->excess += $request->amount;
            $acc->save();
        }
        if (!$request->sendBank) {
            $acc = Account::where('accountNumber', $request->sendId)->first();
            if (!$acc) {
                return response()->json(['error' => 'account doesnt exist'], 204);
            }
            Mail::to($acc->user->email)->send(new OTPMail($OTPString));
            $transfer = Transfer::where('sendId', $request->sendId)->where('isConfirm', false)->first();
            if ($transfer) {
                $transfer->sendBank = $request->sendBank;
                $transfer->receivedId = $request->receivedId;
                $transfer->receivedBank = $request->receivedBank;
                $transfer->amount = $request->amount;
                $transfer->reason = $request->reason;
                $transfer->OTPCode = $OTPString;
                $transfer->expiresAt = time() + 60;
                $transfer->save();
                return response()->json(['message' => 'Transfer has been added', 'trasferId' => $transfer->id], 200);
            }
            $request->request->add(['OTPCode' => str_repeat(0, 5 - floor(log10($OTPCode))) . strval($OTPCode)]);
            $request->request->add(['expiresAt' => time() + 60]);    
        }
        return dd($acc->user->email);

        if (!$acc) return response()->json(['error' => 'wrong logic'], 422);

        $transfer = Transfer::create($request->all());
        return response()->json(['message' => 'Transfer has been added', 'transferId' => $transfer->id,'OTPCode' => 'send to '.$acc->user->email], 201);
    }

    public function confirm(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'OTPCode' => 'required|max:255',
            'transferId' => 'required|max:255',
        ]);
        if ($validatedData->fails()) {
            return response()->json('Parameter error', 422);
        }
        $transfer = Transfer::find($request->transferId);
        if (!$transfer || $transfer->isConfirm) {
            return response()->json('confirm error', 422);
        }
        if ($transfer->OTPCode !== $request->OTPCode) {
            return response()->json('wrong code', 403);
        }
        if (Carbon::parse($transfer->expiresAt, 'Asia/Ho_Chi_Minh')->timestamp < Carbon::now('Asia/Ho_Chi_Minh')->timestamp) {
            return response()->json('code is expires', 403);
        }
        $transfer->isConfirm = true;
        $transfer->save();
        $acc = Account::find($transfer->sender->id);
        $acc->excess -= $transfer->amount;
        $acc->save();
        $acc = Account::find($transfer->receiver->id);
        $acc->excess += $transfer->amount;
        $acc->save();
        return response()->json("success", 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function show(Transfer $transfer)
    {
        return $transfer;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function edit(Transfer $transfer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transfer $transfer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transfer $transfer)
    {
        //
    }
}
