<?php

namespace App\Http\Controllers;

use App\Account;
use App\Bank;
use App\Mail\OTPMail;
use App\Notifications\DebtNotification;
use App\Transfer;
use App\User;
use App\DebtList;
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
            if ($request->payer && $acc->excess < $request->amount + 3000) return response(['error' => 'not enoungh money'], 422);
            if ($acc->excess < $request->amount) return response(['error' => 'not enoungh money'], 422);
            $user = $acc->user;
            $userapi = auth('api')->user();
            if ($userapi->id != $user->id) {
                return response()->json(['error' => 'do not have permission'], 403);
            }
            $email = $user->email;
            Mail::to($email)->send(new OTPMail($OTPString));
            $transfer = Transfer::updateOrCreate(['sendId' => $acc->accountNumber,'isConfirm' => false],[
                'sendBank' => $request->sendBank,
                'receivedId' => $request->receivedId,
                'receivedBank' => $request->receivedBank,
                'amount' => $request->payer ? $request->amount : $request->amount - 3000,
                'reason' => $request->reason,
                'OTPCode' => $OTPString,
                'expiresAt' => time() + 60,
                'payer' => $request->payer,
                'creator' => $user->id
            ]);
            return response()->json(['message' => 'Transfer has been added', 'transferId' => $transfer->id, 'OTPCode' => 'send to ' . $email], 201);
        }
        if (!$acc) return response()->json(['error' => 'wrong logic'], 422);
        $acc->excess += $request->amount;
        $acc->save();
        $transfer = Transfer::create($request->all());
        return response()->json(['message' => 'Transfer has been added'], 201);
    }

    public function confirm($id, Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'OTPCode' => 'required|max:255',
        ]);
        if ($validatedData->fails()) {
            return response()->json('Parameter error', 422);
        }
        $transfer = Transfer::find($id);
        if (!$transfer || $transfer->isConfirm) {
            return response()->json('confirm error', 422);
        }
        if ($transfer->sendId !== auth('api')->user()->account->accountNumber) {
            return response()->json('do not have permission', 403);
        }
        if ($transfer->OTPCode !== $request->OTPCode) {
            return response()->json('wrong code', 403);
        }
        if (Carbon::parse($transfer->expiresAt, 'Asia/Ho_Chi_Minh')->timestamp < Carbon::now('Asia/Ho_Chi_Minh')->timestamp) {
            return response()->json('code is expires', 403);
        }
        $transfer->isConfirm = true;
        $transfer->save();
        $acc1 = Account::find($transfer->sender->id);
        $acc1->excess -= $transfer->amount + 3000;
        $acc1->save();
        $acc = Account::find($transfer->receiver->id);
        $acc->excess += $transfer->amount;
        $acc->save();
        $user = $acc->user;
        if($transfer->debtId)
        {
            $debt = DebtList::where('id',$transfer->debtId)->update(['ispaid' => true]);
            $data = [ 'type'=>'paid','user' => $acc1->user,'account' => ['id'=>$acc1->id,'accountNumber'=>$acc1->accountNumber], 'note' => 'thanh toán '.$transfer->note, 'debt' => $debt];
            $user->notify(new DebtNotification($data));   
        }
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
        $user = auth('api')->user();
        if ($transfer->sendId !== $user->account->accountNumber) {
            return response()->json('do not have permission', 403);
        }
        $OTPCode = rand(0, 999999);
        $OTPString = str_repeat(0, 5 - floor(log10($OTPCode))) . strval($OTPCode);
        $transfer->OTPCode = $OTPString;
        $transfer->expiresAt = time() + 60;
        $transfer->save();
        Mail::to($user->email)->send(new OTPMail($OTPString));
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
