<?php

namespace App\Http\Controllers;

use App\Bank;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use OpenPGP;
use OpenPGP_Crypt_RSA;
use OpenPGP_Crypt_Symmetric;
use OpenPGP_LiteralDataPacket;
use OpenPGP_Message;
use Psr\Http\Message\ResponseInterface;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendMoney(Request $request)
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
        if(!$user) return response(['error'=>'user doesnt exist'],422);
        $body = json_encode([
            'amount'=> $request->amount,
            'name' => $user->name,
            'note' => 'testnote'
        ]);
        $bank = Bank::find($request->receivedBank);
        if(!$bank) return response()->json(['error'=>'bank not connected'],422);
        $time = Carbon::now('Asia/Ho_Chi_Minh')->timestamp;
        if($bank->rsa){
            //dd(file_get_contents(public_path('key\Rsakey\PrivateKey.txt')));
            openssl_sign($time.$body,$rawSignature,file_get_contents(public_path('key\Rsakey\PrivateKey.txt')), OPENSSL_ALGO_SHA512);
            $signature = base64_encode($rawSignature);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-API-KEY' => 'RSA_123456789',
                'X-REQUEST-TIME' => $time,
                'X-SIGNATURE'=> $signature])
                ->post('https://w-internet-banking.herokuapp.com/api/partner/deposits/'.$request->receivedId,
                ['amount'=>$request->amount,'name'=>$user->name,'note'=>'testnote']);
            return $response;
        }else{
            $time=$time*1000;
            $data = $request->amount.','.$request->receivedId.','.$time;
            $file = file_get_contents(public_path('key\Pgpkey\PrivateKey.asc'));
            $key = OpenPGP_Message::parse(OpenPGP::unarmor($file,'PGP PRIVATE KEY BLOCK'));
            $key = OpenPGP_Crypt_Symmetric::decryptSecretKey('Minhtam1234',$key->packets[0]);
            $data = new OpenPGP_LiteralDataPacket($data);
            $data->normalize(true);
            $gpg = new OpenPGP_Crypt_RSA($key);
            $signature = $gpg->sign($data);
            $packets = $signature->signatures()[0];
            $sign = "-----BEGIN PGP SIGNED MESSAGE-----\nHash: SHA256\n\n".preg_replace("/^-/", "- -", $packets[0]->data) . "\n".OpenPGP::enarmor($packets[1][0]->to_bytes(), "PGP SIGNATURE");
            $body = json_encode(['SoTien'=>$request->amount,'SoTaiKhoan'=>$request->receivedId]);
            dd($body);
            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_URL,'https://nhom34bank.herokuapp.com/api/noptien');
            // curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            // curl_setopt($ch, CURLOPT_HTTP_CONTENT_DECODING, false);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            // curl_setopt($ch, CURLOPT_HTTPHEADER, [
            //     'Content-Type: application/json',
            //     'bank-code:'.'partner19',
            //         'time:'.$time,
            //         'sig:'.hash('sha256',$time.json_encode($request->all()).'nhom19banking'),
            //         'signature-pgp:'.$sign
            // ]);
            // $responseBody=curl_exec($ch);
            // $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            // dd($responseBody);
            $client = new \GuzzleHttp\Client();
            dd('asdas');
            $response = $client->put('https://nhom34bank.herokuapp.com/api/noptien',[
                RequestOptions::BODY => $body,
                RequestOptions::HEADERS => [
                    'Content-Type: application/json',
                    'bank-code' => 'partner19',
                    'time' => $time,
                    'sig' => hash('sha256',$time.json_encode($request->all()).'nhom19banking'),
                    'signature-pgp'=> $sign
                ],
            ]);
            dd($response);
            // $req = new \GuzzleHttp\Psr7\Request('PUT',
            //     'https://nhom34bank.herokuapp.com/api/noptien',[
            //         'bank-code' => 'partner19',
            //         'time' => $time,
            //         'sig' => hash('sha256',$time.json_encode($request->all()).'nhom19banking'),
            //         'signature-pgp'=> $sign
            //     ],$body);
            // $client = new \GuzzleHttp\Client();
            // $promise = $client->sendAsync($req); 
            // $promise->then(
            //     function (ResponseInterface $res) {
            //         echo 'aaa';
            //         echo $res->getStatusCode() . "\n";
            //     },
            //     function (RequestException $e) {
            //         echo 'bbbb';
            //         echo $e->getMessage() . "\n";
            //         echo $e->getRequest()->getMethod();
            //     }
            // );
            // $response = Http::withHeaders([
            //     'bank-code' => 'partner19',
            //     'time' => $time,
            //     'sig' => hash('sha256',$time.json_encode($request->all()).'nhom19banking'),
            //     'signature-pgp'=> $sign])
            //     ->put('https://nhom34bank.herokuapp.com/api/noptien',
            //     ['SoTien'=>$request->amount,'SoTaiKhoan'=>$request->receivedId]);
        }
    }
    public function viewuser(Request $request)
    {
        $validatedData = Validator::make($request->all(),[
            'id' => 'required|string|max:16',
            'bankId'=> 'required|max:255'
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error'=>'Parameter error'],422);
        }
        $bank = Bank::find($request->bankId);
        if(!$bank) return response()->json(['error'=>'bank not connected'],422);
        $time = Carbon::now('Asia/Ho_Chi_Minh')->timestamp;
        if($bank->rsa){

            $hash = hash('sha512',$time.'!@#$%^&*(');

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-API-KEY' => 'RSA_123456789',
                'X-REQUEST-TIME' => $time,
                'X-HASH'=> $hash])
                ->get('https://w-internet-banking.herokuapp.com/api/partner/accounts/'.$request->id);
            return $response;
        }else{
            $time = $time*1000;
            $response = Http::withHeaders([
                'bank-code' => 'partner19',
                'time' => $time,
                'sig' => hash('sha256',$time.'nhom19banking')
                ])
                ->get('https://nhom34bank.herokuapp.com/api/taikhoan/'.$request->id);
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
