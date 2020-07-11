<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\User as UserResource;
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
        })->join('accounts', 'users.id', '=', 'accounts.userId')->paginate($request->limit);
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
        $user = User::where('email', $request->email)->first();
        if ($user) {
            return response()->json(['error' => 'email exist'], 422);
        }
        $user = User::where('username', $request->username)->first();
        if ($user) {
            return response()->json(['error' => 'email exist'], 422);
        }
        $request->merge(['roleId' => 2]);
        return User::create($request->all());
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
        if ($request->roleId && $request->roleId !== $user->roleId) {
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
        if ($user->roleId != 2) {
            return response()->json(['error' => 'do not have permission'], 422);
        }
        $user->delete();
        return response()->json(['message'=>'user has been remove'],200);
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
        return $user;
    }

    public function showTransfer($id)
    {
        $user = User::find($id);
        if (auth('api')->user()->roleId >= $user->roleId) return response()->json(['error' => 'do not have permission'], 403);
        $acc = $user->account;
        return $acc->sendTransfer;
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
