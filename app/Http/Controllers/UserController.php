<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\User as UserResource;
use App\Transfer;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::all();
    }
    public function employeeIndex(Request $request)
    {
        if (auth('api')->user()->roleId !== 1) return response()->json(['error' => 'do not have permission'], 403);
        if (!$request->limit) $request->merge(['limit', 10]);
        if (!$request->page) $request->merge(['page', 1]);
        if (!$request->keyword) $request->merge(['keyword', '']);
        return User::where('roleId', 2)->where(function ($query) use ($request) {
            $query->where('name', 'LIKE', "%{$request->keyword}%")
                ->orWhere('id', 'LIKE', "%{$request->keyword}%");
        })->paginate($request->limit);
    }
    public function customerIndex(Request $request)
    {
        if (auth('api')->user()->roleId > 2) return response()->json(['error' => 'do not have permission'], 403);
        if (!$request->limit) $request->merge(['limit', 10]);
        if (!$request->page) $request->merge(['page', 1]);
        if (!$request->keyword) $request->merge(['keyword', '']);
        return User::where('roleId', 3)->where(function ($query) use ($request) {
            $query->where('users.username', 'LIKE', "%{$request->keyword}%")
                ->orWhere('users.id', 'LIKE', "%{$request->keyword}%")
                ->orWhere('accounts.accountNumber', 'LIKE', "%{$request->keyword}%");
        })->join('accounts', 'users.id', '=', 'accounts.userId')->paginate($request->limit, ['users.*','accounts.accountNumber'], 'page', $request->page);
    }
    public function employeeStore(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'username' => 'required|max:64',
            'password' => 'required|min:6|max:64',
            'name' => 'required|max:60',
            'email' => 'required|max:60',
            'phone' => 'string|size:10',
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error' => 'Parameter error'], 422);
        }
        if (auth('api')->user()->roleId != 1) {
            return response()->json(['error' => 'do not have permission'], 403);
        }
        $user = User::where('email', $request->email)->first();
        if ($user) {
            return response()->json(['error' => 'email exist'], 422);
        }
        $user = User::where('username', $request->username)->first();
        if ($user) {
            return response()->json(['error' => 'email exist'], 422);
        }
        $user = User::create($request->all());
        $user->roleId = 2;
        $user->save();
        return $user;
    }
    public function employeeUpdate(Request $request, $id)
    {
        $validatedData = Validator::make($request->all(), [
            'password' => 'min:6|max:64',
            'email' => 'max:60',
            'phone' => 'string|size:10',
            'roleId' => 'digits_between:1,3'
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error' => 'Parameter error'], 422);
        }
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'user not exist'], 404);
        }
        $authUser = auth('api')->user();
        if (($authUser->roleId != 1 || $user->roleId != 2) && $authUser->id !== $user->id) {
            return response()->json(['error' => 'do not have permission'], 403);
        }
        if ($request->email && $request->email !== $user->email) {
            $temp = User::where('email', $request->email)->first();
            if ($temp) {
                return response()->json(['error' => 'email exist'], 422);
            }
            $user->email = $request->email;
        }
        if ($request->phone && $request->phone !== $user->phone) {
            $user->phone = $request->phone;
        }
        if ($request->roleId && $request->roleId !== $user->roleId && $authUser->roleId === 1) {
            $user->roleId = $request->roleId;
        }
        if ($request->password) {
            $user->password = $request->password;
        }
        $user->save();
        return $user;
    }
    public function employeeDestroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'user not exist'], 404);
        }
        if ($user->roleId != 2 || auth('api')->user()->roleId != 1) {
            return response()->json(['error' => 'do not have permission'], 422);
        }
        $user->delete();
        return response()->json(['message' => 'user has been remove'], 200);
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
     * @param  \App\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        if (auth('api')->user()->roleId >= $user->roleId) return response()->json(['error' => 'do not have permission'], 403);
        $user->account;
        return $user;
    }

    public function showTransfer($id, Request $request)
    {
        $request->limit = $request->limit ? $request->limit : 10;
        $request->page = $request->page ? $request->page : 1;
        $authUser = auth('api')->user();
        if ($id === 'me') {
            $id = $authUser->id;
        }
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'user not exist'], 404);
        }
        if ($authUser->roleId >= $user->roleId && $authUser->id !== $user->id) return response()->json(['error' => 'do not have permission'], 403);
        $acc = $user->account[0];
        if (!$acc) {
            return response()->json(['error' => 'user not have account'], 404);
        }
        $transfer = Transfer::with(['sendBank:id,name','ReceivedBank:id,name'])->where('isConfirm', true)->whereNull('debtId')->where('sendId',$acc->accountNumber)->paginate($request->limit, ['*'], 'page', $request->page);
        return $transfer;
    }
    public function showAccount($id, Request $request)
    {
        $request->limit = $request->limit ? $request->limit : 10;
        $request->page = $request->page ? $request->page : 1;
        $authUser = auth('api')->user();
        if ($id === 'me') {
            $id = $authUser->id;
        }
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'user not exist'], 404);
        }
        if ($authUser->roleId >= $user->roleId && $authUser->id !== $user->id) return response()->json(['error' => 'do not have permission'], 403);
        $acc = $user->account()->paginate($request->limit, ['accountNumber','type', 'excess'], 'page', $request->page);
        if (!$acc) {
            return response()->json(['error' => 'user not have account'], 404);
        }
        return $acc;
    }
    public function showDebtTransfer($id, Request $request)
    {
        $request->limit = $request->limit ? $request->limit : 10;
        $request->page = $request->page ? $request->page : 1;
        $authUser = auth('api')->user();
        if ($id === 'me') {
            $id = $authUser->id;
        }
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'user not exist'], 404);
        }
        if ($authUser->roleId >= $user->roleId && $authUser->id !== $user->id) return response()->json(['error' => 'do not have permission'], 403);
        $acc = $user->account[0];
        if (!$acc) {
            return response()->json(['error' => 'user not have account'], 404);
        }
        $transfer = Transfer::with(['sendBank:id,name','ReceivedBank:id,name'])->where('isConfirm', true)->whereNotNull('debtId')->where(function ($query) use ($acc) {
            $query->where('sendId', $acc->accountNumber)
                ->orWhere('receivedId', $acc->accountNumber);
        })->paginate($request->limit, ['*'], 'page', $request->page);
        return $transfer;
    }
    public function showRechargeTransfer($id, Request $request)
    {
        $request->limit = $request->limit ? $request->limit : 10;
        $request->page = $request->page ? $request->page : 1;
        $authUser = auth('api')->user();
        if ($id === 'me') {
            $id = $authUser->id;
        }
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'user not exist'], 404);
        }
        if ($authUser->roleId >= $user->roleId && $authUser->id !== $user->id) return response()->json(['error' => 'do not have permission'], 403);
        $acc = $user->account[0];
        if (!$acc) {
            return response()->json(['error' => 'user not have account'], 404);
        }
        $transfer = Transfer::with(['sendBank:id,name','ReceivedBank:id,name'])->where('isConfirm', true)->whereNull('debtId')->where('receivedId',$acc->accountNumber)->paginate($request->limit, ['*'], 'page', $request->page);
        return $transfer;
    }
    public function showNotify(Request $request)
    {
        $request->limit = $request->limit ? $request->limit : 10;
        $request->page = $request->page ? $request->page : 1;  
        $user = auth('api')->user();
        return $user->notifications()->orderBy('created_at','desc')->paginate($request->limit,['id','data','read_at as readAt'],'page',$request->page);
    }
    public function readNotify($id)
    {
        $notification = auth('api')->user()->notifications()->find($id);
        if(!$notification)
        {
            return response()->json(['error'=>'notify not exist'],200);
        }
        $notification->markAsRead();
        return response()->json(['message'=>'success'],200);
    }
    public function readAllNotify()
    {
        auth('api')->user()->unreadNotifications->markAsRead();
        return response()->json(['message'=>'success'],200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
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
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
