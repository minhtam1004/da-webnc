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
        $credentials = $request->only('username', 'password');
        if ($token = auth('api')->attempt($credentials)) {
            return $this->respondWithToken($token);
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
        $data = ['userId' => $user->id, 'accountNumber' => $rand, 'excess' => 0];
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
        $user->account;
        return response()->json($user);
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
            'email' => 'required',
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error' => 'Parameter error'], 422);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'email not exist'], 404);
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
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
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
