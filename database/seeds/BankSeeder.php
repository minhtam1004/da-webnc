<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('banks')->insert([
            'bank_code'=> 'partner34',
            'name' => 'nhom19',
            'key' => 'nhom34.pem',
            'api_trans' => 'https://nhom34bank.herokuapp.com/api/noptien',
            'api_user' => 'https://nhom34bank.herokuapp.com/api/taikhoan',
            'secret_key' => 'nhom34banking',
            'rsa' => false
        ]);
        DB::table('banks')->insert([
            'bank_code'=> 'partner5',
            'name' => 'nhom5',
            'key' => 'nhom5.pem',
            'api_trans' => 'https://w-internet-banking.herokuapp.com/api/partner',
            'api_user' => 'https://nhom5bank.herokuapp.com/api/taikhoan',
            'secret_key' => 'nhom5banking',
            'rsa' => true
        ]);
    }
}
