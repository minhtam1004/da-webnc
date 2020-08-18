<?php

namespace App\Http\Middleware;

use App\Bank;
use Closure;
use Illuminate\Support\Facades\Storage;
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
        if (!$request->header('X-BANK')) return response()->json(['message' => 'bank not allow to connect to this info'], 400);
        $bank = Bank::where('bank_code', $request->header('X-BANK'))->first();
        if (!$bank) return response()->json(['error' => 'bank not connected'], 400);
        if (!$request->header('X-TIME')) return response()->json(['message' => 'dont have time'], 400);
        if (!$request->header('X-SIG')) return response()->json(['message' => 'dont have signature'], 400);
        if ($request->header('X-TIME') > time() + 300) return response()->json(['expires data'], 400);
        // openssl_sign($time.$body,$rawSignature,file_get_contents(public_path('key\Rsakey\'.$bank->key)), OPENSSL_ALGO_SHA512);
        // $signature = base64_encode($rawSignature);
        if (!file_exists(public_path('key/Rsakey/' . $bank->key))) return response()->json(['dont have key file'], 400);
        $key = file_get_contents(public_path('key/Rsakey/' . $bank->key));
        $rsa = new RSA();
        $rsa->loadKey($key);
        $rsa->setSignatureMode(RSA::SIGNATURE_PKCS1);
        if (!$rsa->verify($request->header('X-TIME') . $request->getContent(), base64_decode($request->header('X-SIG'))))
            return response()->json(['message' => 'not correct key'], 400);
        $request->request->add(['sendBank' => $bank->id, 'receivedBank' => null]);
        return $next($request);
    }
}
