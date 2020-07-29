<?php

namespace App\Http\Controllers;

use App\Account;
use App\DebtList;
use App\Events\NotificationEvent;
use App\Mail\OTPMail;
use App\Notifications\DebtNotification;
use App\Transfer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Pusher\Pusher;

class DebtController extends Controller
{
    public function show($id)
    {
        $debt = DebtList::find($id);
        if (!$debt) {
            return response()->json(['error' => 'debt is not exist'], 404);
        }
        return response()->json($debt, 200);
    }
    public function index(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'limit' => 'numeric|min:1',
            'page' => 'numeric|min:1',
            'status' => 'array'
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error' => 'Parameter error'], 422);
        }
        $status = ['created','paid','deleted'];
        if($request->status && $request->status[0] && array_reduce($request->status, function ($isHas, $num) use($status) {
            return $isHas || !in_array($num,$status);
        })){
            return response()->json(['error' => 'status not include'], 422);
        };
        $request->status = $request->status && $request->status[0] ? $request->status : $status;
        $request->limit = $request->limit ? $request->limit : 10;
        $request->page = $request->page ? $request->page : 1;
        $request->status = $request->status ? $request->status : [];
        $user = auth('api')->user();
        $acc = $user->account;
        $debt = $acc->owndebts()->whereIn('status', $request->status)->paginate($request->limit, ['*'], 'page', $request->page);
        return response()->json($debt, 200);
    }
    public function otherindex(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'limit' => 'numeric|min:1',
            'page' => 'numeric|min:1',
            'status' => 'array'
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error' => 'Parameter error'], 422);
        }
        $status = ['created','paid','deleted'];
        if($request->status && array_reduce($request->status, function ($isHas, $num) use($status) {
            return $isHas || !in_array($num,$status);
        })){
            return response()->json(['error' => 'status not include'], 422);
        };
        $request->status = $request->status ? $request->status : $status;
        $request->limit = $request->limit ? $request->limit : 10;
        $request->page = $request->page ? $request->page : 1;
        $debt = auth('api')->user()->account->otherdebts()->whereIn('status', $request->status)->paginate($request->limit, ['*'], 'page', $request->page);;
        return response()->json($debt, 200);
    }

    public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'otherId' => 'required|max:255',
            'debt' => 'required|numeric|min:10000|max:1000000000',
            'note' => 'max:255'
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error' => 'Parameter error'], 422);
        }
        $acc = Account::where('accountNumber', $request->otherId)->first();
        if (!$acc) {
            return response()->json(['error' => 'account not exist'], 404);
        }
        $acc1 = auth('api')->user()->account;
        if (!$acc1) {
            return response()->json(['error' => 'do not have account'], 404);
        }
        if ($acc->accountNumber === $acc1->accountNumber) {
            return response()->json(['error' => 'can not debt yourself'], 422);
        }
        $request->merge(['ownerId' => $acc1->accountNumber]);
        $user = $acc->user;
        $debt = DebtList::create($request->all());
        $data = ['debtType'=>'created','user' => $acc1->user, 'account' => ['id'=>$acc1->id,'accountNumber'=>$acc1->accountNumber], 'note' => $request->note, 'debtId' => $debt->id];
        $user->notify(new DebtNotification($data));
        // $options = array(
        //     'cluster' => 'ap1',
        //     'encrypted' => true
        // );

        // $pusher = new Pusher(
        //     env('PUSHER_APP_KEY'),
        //     env('PUSHER_APP_SECRET'),
        //     env('PUSHER_APP_ID'),
        //     $options
        // );
        // //broadcast(new NotificationEvent($data))->toOthers();
        // $pusher->trigger('NotificationEvent', 'send-message', $data);
        // Mail::to($acc->user->email)->send(new OTPMail('121212'));
        return $debt;
    }
    public function destroy($id, Request $request)
    {  
        $validatedData = Validator::make($request->all(), [
            'note' => 'max:255'
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error' => 'Parameter error'], 422);
        }
        $debt = DebtList::find($id);
        if (!$debt) {
            return response()->json(['error' => 'debt is not exist'], 404);
        }
        $user = auth()->user();
        $owner = $debt->owner->user;
        $other = $debt->other->user;
        $data = null;
        if ($user->id === $owner->id) {
            $acc = $owner->account;
            $data = ['debtType'=>'deleted', 'user' => $user,'account' => ['id'=>$acc->id,'accountNumber'=>$acc->accountNumber], 'deleteNote' => $request->note,'debtId' => $debt->id];
            $other->notify(new DebtNotification($data));
        }
        if ($user->id === $other->id) {
            $acc = $other->account;
            $data = ['debtType'=>'deleted','user' => $user,'account' => ['id'=>$acc->id,'accountNumber'=>$acc->accountNumber], 'deleteNote' => $request->note,'debtId' => $debt->id];
            $owner->notify(new DebtNotification($data));
        }
        if (!$data) {
            return response()->json(['error' => 'do not have permission'], 403);
        }
        $debt->status = 'deleted';
        $debt->deleteNote = $request->note;
        $debt->save();
        return response()->json($debt->id, 200);
    }
    public function paid($id)
    {
        $debt = DebtList::find($id);
        if (!$debt) {
            return response()->json(['error' => 'debt is not exist'], 404);
        }
        $user = auth('api')->user();
        $acc = $user->account;
        if($acc->accountNumber != $debt->otherId)
        {
            return response()->json(['error' => 'do not have permission'], 403);
        }
        $OTPCode = rand(0, 999999);
        $OTPString = str_repeat(0, 5 - floor(log10($OTPCode))) . strval($OTPCode);
        if ($acc->excess < $debt->debt) return response(['error' => 'not enoungh money'], 422);
        $email = $user->email;
        Mail::to($email)->send(new OTPMail($OTPString));
        $transfer = Transfer::updateOrCreate(['sendId' => $acc->accountNumber,'isConfirm' => false],[
            'sendBank' => false,
            'receivedId' => $debt->ownerId,
            'receivedBank' => null,
            'amount' => $debt->debt,
            'reason' => $debt->note,
            'OTPCode' => $OTPString,
            'expiresAt' => time() + 60,
            'payer' => true,
            'debtId' => $id,
            'creator' => $user->id
        ]);
        return response()->json(['message' => 'Transfer has been added', 'transferId' => $transfer->id, 'OTPCode' => 'send to ' . $email], 201);
    }
}
