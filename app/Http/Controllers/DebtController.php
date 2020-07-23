<?php

namespace App\Http\Controllers;

use App\Account;
use App\DebtList;
use App\Events\NotificationEvent;
use App\Mail\OTPMail;
use App\Notifications\DebtNotification;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Pusher\Pusher;

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
        $debt = auth('api')->user()->owndebts;
        return response()->json($debt,200);
    }
    public function otherindex()
    {
        $debt = auth('api')->user()->owndebts;
        return response()->json($debt,200);
    }

    public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'otherId' => 'required|max:255',
            'debt' => 'required|numeric|min:10000|max:1000000000',
            'note' => 'max:200'
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error' => 'Parameter error'], 422);
        }
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
        $data = ['owner'=>$acc1->user,'note'=>$request->note,'amount'=>$request->debt];
        $user->notify(new DebtNotification($data));
        $options = array(
            'cluster' => 'ap1',
            'encrypted' => true
        );

        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );
        //broadcast(new NotificationEvent($data))->toOthers();
        $pusher->trigger('NotificationEvent', 'send-message', $data);
        // Mail::to($acc->user->email)->send(new OTPMail('121212'));
        $debt = DebtList::create($request->all());
        return $debt;
    }
    public function destroy($id)
    {
        $debt = DebtList::find($id);
        if(!$debt)
        {
            return response()->json(['error' => 'debt is not exist'],404);
        }
        $user = auth()->user();
        $owner = $debt->owner();
        $other = $debt->other();
        $data = null;
        if($user->id === $owner->id)
        {
            $data = ['owner'=>$owner->user,'note'=>'Đã hủy',$debt];
            $other->notify(new DebtNotification($data));
        }
        if($user->id === $other->id)
        {
            $data = ['owner'=>$owner->user,'note'=>'Đã hủy',$debt];
            $owner->notify(new DebtNotification($data));
        }
        if(!$data){
            return response()->json(['error'=>'do not have permission'],403);
        }
        $debt->destroy();
        return response()->json($debt,200);

    }
}
