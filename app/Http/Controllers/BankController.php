<?php

namespace App\Http\Controllers;

use App\Account;
use App\Bank;
use App\Mail\OTPMail;
use App\Transfer;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use OpenPGP;
use OpenPGP_Crypt_RSA;
use OpenPGP_Crypt_Symmetric;
use OpenPGP_LiteralDataPacket;
use OpenPGP_Message;
use OpenPGP_SecretKeyPacket;
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
        $validatedData = Validator::make($request->all(), [
            'sendId' => 'required|max:255',
            'sendBank' => 'max:255',
            'receivedId' => 'required|max:255',
            'receivedBank' => 'required|max:255',
            'amount' => 'required|numeric|min:10000|max:1000000000',
            'reason' => 'required|max:200',
            'payer' => 'boolean'
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error' => 'Parameter error'], 422);
        }
        $acc = Account::where('accountNumber', $request->sendId)->first();
        if (!$acc) {
            return response()->json(['error' => 'account doesnt exist'], 204);
        }
        $user = $acc->user;
        $userapi = auth('api')->user();
        if ($userapi->id != $user->id) {
            return response()->json(['error' => 'do not have permission'], 403);
        }
        $email = $user->email;
        if ($acc->excess < $request->amount) return response(['error' => 'not enoungh money'], 422);
        $bank = Bank::find($request->receivedBank);
        if (!$bank) return response()->json(['error' => 'bank not connected'], 422);
        if (!$request->payer) $request->merge(['payer' => false]);
        $OTPCode = rand(0, 999999);
        $OTPString = str_repeat(0, 5 - floor(log10($OTPCode))) . strval($OTPCode);
        $request->merge(['OTPCode' => $OTPString]);
        $request->merge(['expiresAt' => time() + 60]);
        Mail::to($email)->send(new OTPMail($OTPString));
        $transfer = Transfer::where('sendId', $request->sendId)->where('isConfirm', false)->first();
        if ($transfer) {
            $transfer->sendBank = $request->sendBank;
            $transfer->receivedId = $request->receivedId;
            $transfer->receivedBank = $request->receivedBank;
            $transfer->amount = $request->amount;
            $transfer->reason = $request->reason;
            $transfer->OTPCode = $OTPString;
            $transfer->expiresAt = time() + 60;
            $transfer->payer = $request->payer;
            $transfer->save();
            return response()->json(['message' => 'Transfer has been added', 'transferId' => $transfer->id, 'OTPCode' => 'send to ' . $email], 200);
        }
        $transfer = Transfer::create($request->all());
        return response()->json(['message' => 'Transfer has been added', 'transferId' => $transfer->id, 'OTPCode' => 'send to ' . $email], 200);
    }
    public function send(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'transferId' => 'required|min:1',
            'OTPCode' => 'required|size:6',
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error' => 'Parameter error'], 422);
        }
        $trans = Transfer::find($request->transferId);
        if (!$trans || $trans->isConfirm) {
            return response()->json('confirm error', 422);
        }
        if ($trans->OTPCode !== $request->OTPCode) {
            return response()->json('wrong code', 403);
        }
        if (Carbon::parse($trans->expiresAt, 'Asia/Ho_Chi_Minh')->timestamp < Carbon::now('Asia/Ho_Chi_Minh')->timestamp) {
            return response()->json('code is expires', 403);
        }
        $trans->isConfirm = true;
        $trans->save();
        $user = $trans->sender->user;
        $time = Carbon::now('Asia/Ho_Chi_Minh')->timestamp;
        $bank = $trans->ReceivedBank;
        $trans->amount = $trans->payer ? $trans->amount : $trans->amount - 10000;
        $body = json_encode([
            'amount' => $trans->amount,
            'name' => $user->name,
            'note' => $trans->reason
        ]);
        if ($bank->rsa) {
            //dd(file_get_contents(public_path('key\Rsakey\PrivateKey.txt')));
            openssl_sign($time . $body, $rawSignature, file_get_contents(public_path('key/Rsakey/PrivateKey.txt')), OPENSSL_ALGO_SHA512);
            $signature = base64_encode($rawSignature);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-API-KEY' => 'RSA_123456789',
                'X-REQUEST-TIME' => $time,
                'X-SIGNATURE' => $signature
            ])
                ->post(
                    'https://w-internet-banking.herokuapp.com/api/partner/deposits/' . $trans->receivedId,
                    ['amount' => $trans->amount, 'name' => $user->name, 'note' => $trans->reason]
                );
            if ($response->status() === 200) {
                $user->excess -= $trans->amount + 10000;
                return $response;
            }
            return $response;
        } else {
            $time = $time * 1000;
            $data = $trans->amount . ',' . $trans->receivedId . ',' . $time;
            $file = '/public/key/Pgpkey/PrivateKey.asc';
            $pass = 'Minhtam1234';
            exec("node ../test.js {$file} {$pass} {$data} 2>&1",$result);
            //dd($result);
            // $io = [
            //     0 => ['pipe', 'r'], // node's stdin
            //     1 => ['pipe', 'w'], // node's stdout
            //     2 => ['pipe', 'w'], // node's stderr
            // ];
            
            // $proc = proc_open('node test.js', $io, $pipes);
            
            // $nodeStdout = $pipes[1]; // our end of node's stdout
            // echo date('H:i:s '), fgets($nodeStdout);
            
            // proc_close($proc);
            
            // dd($result, $var);
            // $key = OpenPGP_Message::parse(OpenPGP::unarmor($file, 'PGP PRIVATE KEY BLOCK'));
            // foreach($key as $p) {
            //     if($p instanceof OpenPGP_SecretKeyPacket){
            //         $key = $p;
            //         break;
            //     }
            // }
            // $key = OpenPGP_Crypt_Symmetric::decryptSecretKey('Minhtam1234', $key);
            // $data = new OpenPGP_LiteralDataPacket($data);
            // $data->normalize(true);
            // $gpg = new OpenPGP_Crypt_RSA($key);
            // $signature = $gpg->sign($data,'SHA512');
            // $packets = $signature->signatures()[0];
            
            // //dd(OpenPGP::enarmor($packets[1][0]->to_bytes(),"PGP SIGNATURE"));
            // // \n-----BEGIN PGP SIGNED MESSAGE-----\nHash: SHA512\n\n30000,123456789,1594195364839\n-----BEGIN PGP SIGNATURE-----\nVersion: OpenPGP.js v4.10.4\nComment: https://openpgpjs.org\n\n wpwEAQEKAAYFAl8FfaQACgkQ7ZmXlE6vaZoQAAP+LRaje3mpHZ9wPByWedbA\n214pPUO1SW6jgB09RXuTr9cifYo8pGCyoPS69RaScjzq+RhWj4HdGwWMLB0P\nJS8IIB8LBYoFOTBwoJhchC9WJ+JP9kSM6cGP8P/AigklyMOZLrsiJlsjKWEA\noSAoZsxj/e8XZdQamFXG8Ljw8n5fNsU=\n=ie7F\n-----END PGP SIGNATURE-----\n
            // $sign = "\n-----BEGIN PGP SIGNED MESSAGE-----\nHash: SHA512\n\n" . preg_replace("/^-/", "- -", $packets[0]->data) . OpenPGP::enarmor($packets[1][0]->to_bytes(), "PGP SIGNATURE");
            $sign = $result[0];
            //$body = json_encode(['accNum' => (int) $trans->receivedId, 'moneyAmount' => (int) $trans->amount]);
            $body = json_encode(['accNum' => (int) $trans->receivedId, 'moneyAmount' => (int) $trans->amount]);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://banking34.herokuapp.com/api/transfer/update",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_HTTPHEADER => array(
                    "x-time: " . $time,
                    "x-partner-code: partner19",
                    "x-hash: " . hash('sha256', $time . $body . 'nhom19banking'),
                    'x-signature-pgp: ' . $sign,
                    "Content-Type: application/json"
                ),
            ));

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);
            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_URL, 'https://banking34.herokuapp.com/api/transfer/update');
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            // curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            // curl_setopt($ch, CURLOPT_VERBOSE, true);
            // //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
            // curl_setopt($ch, CURLOPT_HTTPHEADER, [
            //     'Accept: application/json',
            //     'Content-Type: application/json',
            //     'x-partner-code: partner19',
            //     'x-time' => $time,
            //     'x-hash' => hash('sha256', $time . $body . 'nhom19banking'),
            //     'x-signature-pgp' => $sign
            // ]);
            // //dd($response, $httpCode);
            // curl_close($ch);
            // return response()->json($response,$httpCode);

            // $client = new Client();
            // $response = $client->post('http://localhost:6069/test', [
            //     RequestOptions::BODY => $body,
            //     RequestOptions::HEADERS => [
            //         'Content-Type' => 'application/json',
            //         'x-partner-code' => 'partner19',
            //         'x-time' => $time,
            //         'x-hash' => hash('sha256', $time.$body.'nhom19banking'),
            //         'x-signature-pgp' => $sign
            //     ],
            // ]);
            // $response = Http::withHeaders([
            //     'Content-Type' => 'application/json',
            //     'x-partner-code' => 'partner19',
            //     'x-time' => $time,
            //     'x-hash' => hash('sha256', $time . $body . 'nhom19banking'),
            //     'x-signature-pgp' => $sign
            // ])
            //     ->post(
            //         'http://localhost:6069/test',
            //         ['data' => [ 'moneyAmount' => $trans->amount, 'accNum' => $trans->receivedId]]
            //     );
            if ($httpCode === 200) {
                $user->excess -= $trans->amount + 10000;
                return $response;
            }
            return response()->json($response,$httpCode);
        }
    }
    public function viewuser(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'id' => 'required|string|max:16',
            'bankId' => 'required|max:255'
        ]);
        if ($validatedData->fails()) {

            return response()->json(['error' => 'Parameter error'], 422);
        }
        $bank = Bank::find($request->bankId);
        if (!$bank) return response()->json(['error' => 'bank not connected'], 422);
        $time = Carbon::now('Asia/Ho_Chi_Minh')->timestamp;
        if ($bank->rsa) {

            $hash = hash('sha512', $time . '!@#$%^&*(');

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-API-KEY' => 'RSA_123456789',
                'X-REQUEST-TIME' => $time,
                'X-HASH' => $hash
            ])
                ->get('https://w-internet-banking.herokuapp.com/api/partner/accounts/' . $request->id);
            return $response;
        } else {
            $time = $time * 1000;
            $response = Http::withHeaders([
                'x-partner-code' => 'partner19',
                'x-time' => $time,
                'x-signature' => hash('sha256', $time . 'nhom19banking')
            ])
                ->get('https://banking34.herokuapp.com/api/user/' . $request->id);
            return $response;
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Bank::all();
    }
    public function transfer(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'startDate' => 'date_format:d/m/Y',
            'endDate' => 'date_format:d/m/Y',
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error' => 'Parameter error'], 422);
        }
        $start = $request->startDate ? Carbon::createFromFormat('d/m/Y', $request->startDate, 'Asia/Ho_Chi_Minh')->timestamp : 0;
        $end = $request->endDate ? Carbon::createFromFormat('d/m/Y', $request->endDate, 'Asia/Ho_Chi_Minh')->timestamp : null;
        if(auth('api')->user()->roleId > 1)
        {
            return response()->json(['error'>'do not have permission'],403);
        }
        return $end===null ? Transfer::where('isConfirm', true)->where('created_at','>',date('Y-m-d',$start))->where(function ($query) {
            $query->whereNotNull('receivedBank')
            ->orWhereNotNull('sendBank');
        })->paginate(10):Transfer::where('isConfirm', true)->where('created_at','>',date('Y-m-d',$start))->where('created_at','<',date('Y-m-d',$end))->where(function ($query) {
            $query->whereNotNull('receivedBank')
            ->orWhereNotNull('sendBank');
        })->paginate(10);
    }
    public function bankTransfer($id, Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'startDate' => 'date_format:d/m/Y',
            'endDate' => 'date_format:d/m/Y',
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error' => 'Parameter error'], 422);
        }
        $start = $request->startDate ? Carbon::createFromFormat('d/m/Y', $request->startDate, 'Asia/Ho_Chi_Minh')->timestamp : 0;
        $end = $request->endDate ? Carbon::createFromFormat('d/m/Y', $request->endDate, 'Asia/Ho_Chi_Minh')->timestamp : null;
        if(auth('api')->user()->roleId > 1)
        {
            return response()->json(['error'>'do not have permission'],403);
        }
        $bank = Bank::find($id);
        if(!$bank)
        {
            return response()->json(['error'=>'bank does not exist'],404);
        }
        return $end===null ? Transfer::where('isConfirm', true)->where('created_at','>',date('Y-m-d',$start))->where(function ($query) use ($bank) {
            $query->where('receivedBank',$bank->id)
            ->orWhere('sendBank',$bank->id);
        })->paginate(10):Transfer::where('isConfirm', true)->where('created_at','>',date('Y-m-d',$start))->where('created_at','<',date('Y-m-d',$end))->where(function ($query) use ($bank) {
            $query->where('receivedBank',$bank->id)
            ->orWhere('sendBank',$bank->id);
        })->paginate(10);
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
