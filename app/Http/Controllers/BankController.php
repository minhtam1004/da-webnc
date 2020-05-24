<?php

namespace App\Http\Controllers;

use App\Bank;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use OpenPGP;
use OpenPGP_LiteralDataPacket;
use OpenPGP_Message;
use OpenPGP_SignaturePacket;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendMoney($request)
    {
        $validatedData = Validator::make($request->all(),[
            'sendId' => 'required|max:255',
            'sendBank' => 'max:255',
            'receivedId' => 'required|max:255',
            'receivedBank' => 'required|max:255',
            'amount' => 'required|numeric|min:10000|max:1000000000',
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error'=>'Parameter error'],422);
        }
        $user = User::find($request->sendId);
        $body = json_encode([
            'amount'=> $request->amount,
            'name' => $user->name,
            'note' => 'testnote'
        ]);
        $bank = Bank::find($request->receivedBank);
        if(!$bank) return response()->json(['error'=>'bank not connected'],422);
        $user = User::find($request->sendId);
        $time = Carbon::now()->timestamp;
        if($bank->rsa){
            openssl_sign($time.$body,$rawSignature,file_get_contents(public_path('key/Rsakey/PivateKey.txt')),OPENSSL_ALGO_SHA512);
            $signature = base64_encode($rawSignature);
            $response = Http::withHeaders([
                'X-API-KEY' => 'PGP_123456789',
                'X-REQUEST-TIME' => $time,
                'X-SIGNATURE'=> $signature])
                ->post('https://w-internet-banking.herokuapp.com/api/partner/deposits/'.$request->receivedId,
                ['amount'=>$request->amount,'name'=>$user->name,'note'>'testnote']);
            return $response;
        }else{
            $data = $request->amount.$request->receivedId.$time;
            $key = OpenPGP_Message::parse(file_get_contents(public_path('key/Pgpkey/PivateKey.txt')));
            $key = $key[0];
            $data = new OpenPGP_LiteralDataPacket($data);
            $gpg = new OpenPGP_SignaturePacket($key);
            $signature = $gpg->sign_data($data);
            $response = Http::withHeaders([
                'bank-code' => 'partner19',
                'time' => $time,
                'sig' => hash('sha256',$time.'nhom19banking'),
                'signature-pgp'=> $signature])
                ->put('https://nhom34bank.herokuapp.com/api/noptien',
                ['SoTien'=>$request->amount,'SoTaiKhoan'=>$request->receivedId]);
            return $response;
        }
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
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function show(Bank $bank)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function edit(Bank $bank)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bank $bank)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bank $bank)
    {
        //
    }
}
