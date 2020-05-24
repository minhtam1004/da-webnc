<?php

namespace App\Http\Middleware;

use App\Bank;
use Closure;
use phpseclib\Crypt\RSA;

class checkUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!$request->header('X-BANK')) return response()->json(['message' => 'bank not allow to connect to this info'],422);
        $bank = Bank::where('bank_code',$request->header('X-BANK'))->first();
        if(!$bank) return response()->json(['error'=>'bank not connected'],422); 
        if(!$request->header('X-TIME')) return response()->json(['message' => 'dont have time'],422);
        if(!$request->header('X-HASH')) return response()->json(['message' => 'dont have hash'],422);
        if($request->header('X-TIME') > time() + 300) return response()->json(['expires data'],403);
        if(!base64_encode(hash('sha256',$request->header('X-TIME').$bank->secret_key))== $request->header('X-HASH'))
        return response()->json(['message'=> 'not correct key'],422);
        return $next($request);
    }
}
