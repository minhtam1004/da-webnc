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
            'bank_code'=> 'nhom34banking',
            'name' => 'nhom34',
            'key' => 'nhom34.asc',
            'secret_key' => 'nhom34banking',
            'rsa' => false
        ]);
        DB::table('banks')->insert([
            'bank_code'=> 'nhom5banking',
            'name' => 'nhom5',
            'key' => 'nhom19banking',
            'secret_key' => 'nhom5banking',
            'rsa' => true
        ]);
    }
}
