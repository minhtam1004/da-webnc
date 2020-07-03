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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
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
        if($userapi->id != $user->id){
            return response()->json(['error'=>'do not have permission'],403);
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
            'transferId' => 'required',
            'OTPCode' => 'required|size:6',
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error' => 'Parameter error'], 422);
        }
        if ($validatedData->fails()) {
            return response()->json('Parameter error', 422);
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
        $bank = $trans->bank;
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
                $user->excess -= $trans->payer ? $trans->amount + 20000 : $trans->amount + 10000;
                return $response;
            }
            return $response;
        } else {
            $time = $time * 1000;
            $data = $trans->amount . ',' . $trans->receivedId . ',' . $time;
            $file = file_get_contents(public_path('key/Pgpkey/PrivateKey.asc'));
            $key = OpenPGP_Message::parse(OpenPGP::unarmor($file, 'PGP PRIVATE KEY BLOCK'));
            $key = OpenPGP_Crypt_Symmetric::decryptSecretKey('Minhtam1234', $key->packets[0]);
            $data = new OpenPGP_LiteralDataPacket($data);
            $data->normalize(true);
            $gpg = new OpenPGP_Crypt_RSA($key);
            $signature = $gpg->sign($data);
            $packets = $signature->signatures()[0];
            $sign = "-----BEGIN PGP SIGNED MESSAGE-----\nHash: SHA256\n\n" . preg_replace("/^-/", "- -", $packets[0]->data) . "\n" . OpenPGP::enarmor($packets[1][0]->to_bytes(), "PGP SIGNATURE");
            $body = json_encode(['SoTien' => $trans->amount, 'SoTaiKhoan' => $trans->receivedId]);
            $client = new \GuzzleHttp\Client();
            $response = $client->put('https://nhom34bank.herokuapp.com/api/noptien', [
                RequestOptions::BODY => $body,
                RequestOptions::HEADERS => [
                    'Content-Type: application/json',
                    'bank-code' => 'partner19',
                    'time' => $time,
                    'sig' => hash('sha256', $time . json_encode($body) . 'nhom19banking'),
                    'signature-pgp' => $sign
                ],
            ]);
            if ($response->getStatusCode() === 200) {
                $user->excess -= $trans->payer ? $trans->amount + 10000 : $trans->amount;
                return $response;
            }
            return $response;
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
                'bank-code' => 'partner19',
                'time' => $time,
                'sig' => hash('sha256', $time . 'nhom19banking')
            ])
                ->get('https://nhom34bank.herokuapp.com/api/taikhoan/' . $request->id);
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
