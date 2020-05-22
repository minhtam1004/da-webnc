<?php

namespace App\Http\Middleware;
use App\Bank;
use Closure;
use phpseclib\Crypt\RSA;

class CheckBank
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
        $bank = Bank::findOrFail($request->header('X-BANK'));
        if(!$request->header('X-TIME')) return response()->json(['message' => 'dont have time'],422);
        if(!$request->header('X-SIG')) return response()->json(['message' => 'dont have signature'],422);
        //if($request->header('X-TIME') > time() + 300) return response()->json(['expires data'],403);
        $rsa = new RSA();
        $rsa->loadKey($bank->key);
        $rsa->setSignatureMode(RSA::SIGNATURE_PKCS1);
        if(!$rsa->verify($request->header('X-TIME').json_encode($request->all()),base64_decode($request->header('X-SIG'))))
        return response()->json(['message'=> 'not correct key'],422);
        return $next($request);
    }
}
