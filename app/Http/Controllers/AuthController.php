<?php

namespace App\Http\Controllers;

use App\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\PasswordReset;
use App\User;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh', 'resetPassword', 'changePasswordWithMail']]);
    }

    /**
     * Get a JWT token via given credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'username' => 'required|max:255',
            'password' => 'required|min:6|max:255',
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error' => 'Parameter error'], 422);
        }
        $user = User::where('username', $request->username)->get()->first();
        if(!$user)
        {
            return response()->json(['error'=>'user does not exist'], 404);
        }
        if(!Hash::check($request->password, $user->password))
        {
            return response()->json(['error'=>'password incorrect'], 401);
        }
        $credentials = $request->only('username', 'password');
        if ($token = auth('api')->attempt($credentials)) {
            $refreshToken = auth('api')->setTTL(20160)->attempt($credentials);
            $user->refresh_token = $refreshToken;
            $user->save();
            return $this->respondWithToken($token,$refreshToken);
        }
        return response()->json(['error' => 'wrong username or password'], 401);
    }
    /**
     * Get a JWT token via given credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $user = auth('api')->user();
        if ($user && $user->roleId > 2) {
            return response()->json(['error' => 'Do not have permission to access this'], 401);
        }
        $validatedData = Validator::make($request->all(), [
            'username' => 'required|max:255',
            'password' => 'required|min:6|max:255',
            'name' => 'required|min:6|max:255',
            'email' => 'required|min:6|max:255',
            'phone' => 'required|digits:10',
            'amount' => 'required|numeric|min:50000'
        ]);

        if ($validatedData->fails()) {
            return response()->json(['error' => 'Parameter error'], 422);
        }
        if (User::where('username', $request->username)->first()) {
            return response()->json(['error' => 'user exist'], 422);
        }
        if (User::where('email', $request->email)->first()) {
            return response()->json(['error' => 'email exist'], 422);
        }
        $user = User::create($request->all());
        $rand = $user->id + 1134567890;
        while (Account::where('accountNumber', $rand)->first()) {
            $rand = ($rand + 200) % 10000000000;
        }
        $data = ['userId' => $user->id, 'accountNumber' => $rand, 'excess' => $request->amount, 'type' => 1];
        $acc = Account::create($data);
        $user->account = $acc;
        return response()->json($user);
    }
    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = auth('api')->user();
        $user->permission = $user->role->name;
        return response()->json($user,200);
    }
    public function changePassword(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'newpassword' => 'required|min:6|max:255',
            'oldpassword' => 'required|min:6|max:255',
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error' => 'Parameter error'], 422);
        }
        $user = auth('api')->user();
        if (!Hash::check($request->oldpassword, $user->password)) {
            return response()->json(['error' => 'password invalid'], 422);
        } else {
            $user->password = $request->newpassword;
            $user->save();
            return response()->json(['message' => 'change password success'], 201);
        }
    }
    public function resetPassword(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'email' => 'min:6|max:255',
            'username' => 'max:255',
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error' => 'Parameter error'], 422);
        }
        if(!$request->email && !$request->username)
        {
            return response()->json(['error' => 'Parameter error'], 422);
        }
        if($request->email && $request->username)
        {
            return response()->json(['error' => 'Parameter error'], 422);
        }
        $user = $request->email ? User::where('email', $request->email)->first() : User::where('username', $request->username)->first();
        if (!$user) {
            return response()->json(['error' => 'user not exist'], 404);
        }
        $OTPCode = rand(0, 999999);
        $OTPString = str_repeat(0, 5 - floor(log10($OTPCode))) . strval($OTPCode);
        $email = $user->email;
        $PasswordReset = PasswordReset::updateOrCreate(['email' => $email], ['token' => $OTPString]);
        if ($PasswordReset) {
            Mail::to($email)->send(new ResetPasswordMail($OTPString));
        }
        return response()->json(['message' => 'send OTPCode to ' . $email, 'email' => $email, 'username' => $user->username], 200);
    }
    public function changePasswordWithMail(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'token' => 'required|size:6',
            'email' => 'required',
            'password' => 'required|min:6|max:255'
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error' => 'Parameter error'], 422);
        }
        $passwordReset = PasswordReset::findOrFail($request->email);
        if($passwordReset->token !== $request->token)
        {
            return response()->json([
                'message' => 'This password reset token is invalid.',
            ], 422);
        }
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(60)->isPast()) {
            $passwordReset->delete();
            return response()->json([
                'message' => 'This password reset token is expires.',
            ], 422);
        }
        $user = User::where('email', $request->email)->firstOrFail();
        $user->update($request->only('password'));
        //$passwordReset->delete();

        return response()->json([
            'success'
        ]);
    }
    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'refreshToken' => 'required',
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error' => 'Parameter error'], 422);
        }
        $user = auth('api')->user();
        if(!$user)
        {
            return response()->json(['error'=>'token expires'],403);
        }
        if($user->refresh_token !== $request->refreshToken)
        {
            return response()->json(['error' => 'wrong refreshToken'], 422);
        }
        try {
            $token = auth('api')->refresh();
        } catch (TokenExpiredException $e) {
            return response()->json(['error'=>'token expires'],403);
        } catch (JWTException $e) {
            return response()->json(['error'=>'token invalid'],403);
        }
        return $this->respondWithToken($token,$request->refreshToken);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $refreshToken)
    {
        return response()->json([
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }
}
