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
            'reason' => 'required|max:200',
            'payer' => 'boolean'
        ]);
        if ($validatedData->fails()) {
            return response()->json('Parameter error', 422);
        }
        if (!$request->payer) $request->merge(['payer' => false]);
        $acc = null;
        if (!$request->receivedBank) {
            $acc = Account::where('accountNumber', $request->receivedId)->first();
            if (!$acc) return response()->json(['error' => 'account doesnt exist'], 204);
        }
        if (!$request->sendBank) {
            $OTPCode = rand(0, 999999);
            $OTPString = str_repeat(0, 5 - floor(log10($OTPCode))) . strval($OTPCode);
            $acc = Account::where('accountNumber', $request->sendId)->first();
            if (!$acc) {
                return response()->json(['error' => 'account doesnt exist'], 204);
            }
            if ($acc->excess < $request->amount) return response(['error' => 'not enoungh money'], 422);
            $user = $acc->user;
            $userapi = auth('api')->user();
            if ($userapi->id != $user->id) {
                return response()->json(['error' => 'do not have permission'], 403);
            }
            $email = $user->email;
            Mail::to($email)->send(new OTPMail($OTPString));
            $transfer = Transfer::where('sendId', $request->sendId)->where('isConfirm', false)->first();
            if ($transfer) {
                $transfer->sendBank = $request->sendBank;
                $transfer->receivedId = $request->receivedId;
                $transfer->receivedBank = $request->receivedBank;
                $transfer->amount = $request->payer ? $request->amount : $request->amount - 3000;
                $transfer->reason = $request->reason;
                $transfer->OTPCode = $OTPString;
                $transfer->expiresAt = time() + 60;
                $transfer->payer = $request->payer;
                $transfer->save();
                return response()->json(['message' => 'Transfer has been added', 'transferId' => $transfer->id, 'OTPCode' => 'send to ' . $email], 200);
            }
            $request->merge(['OTPCode' => str_repeat(0, 5 - floor(log10($OTPCode))) . strval($OTPCode)]);
            $request->merge(['expiresAt' => time() + 60]);
            $transfer = Transfer::create($request->all());
            return response()->json(['message' => 'Transfer has been added', 'transferId' => $transfer->id, 'OTPCode' => 'send to ' . $email], 201);
        }
        if (!$acc) return response()->json(['error' => 'wrong logic'], 422);
        $acc->excess += $request->amount;
        $acc->save();
        $transfer = Transfer::create($request->all());
        return response()->json(['message' => 'Transfer has been added'], 201);
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
        $acc->excess -= $transfer->payer ? $transfer->amount + 3000 : $transfer->amount;
        $acc->save();
        $acc = Account::find($transfer->receiver->id);
        $acc->excess += $transfer->amount;
        $acc->save();
        return response()->json("success", 200);
    }

    public function getOTP($id)
    {
        $transfer = Transfer::find($id);
        if (!$transfer) {
            return response()->json('transfer does not exist', 422);
        }
        if ($transfer->isConfirm) {
            return response()->json('transfer is confirm', 422);
        }
        $OTPCode = rand(0, 999999);
        $transfer->OTPCode = str_repeat(0, 5 - floor(log10($OTPCode))) . strval($OTPCode);
        $transfer->expiresAt = time() + 60;
        $transfer->save();
        return response()->json(['message' => 'OTP is refresh', 'transferId' => $transfer->id, 'OTPCode' => 'send to ' . $transfer->sender->user->email], 201);
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
