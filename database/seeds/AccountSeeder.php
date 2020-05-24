<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $limit = 10;
        for ($i = 0; $i < $limit; $i++) {
            DB::table('accounts')->insert([
                'userId' => $i,
                'accountNumber' =>  '123456789'.$i,
                'excess' => ($i+1)*1000000,
            ]);
        }
    }
}
