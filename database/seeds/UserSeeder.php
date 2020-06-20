<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
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
            DB::table('users')->insert([
                'username' => 'user'.$i,
                'password' =>  bcrypt('123456'),
                'name' => 'name'.$i,
                'email' => 'email'.$i.'@gmail.com',
                'phone' => '123123123',
            ]);
        }
    }
}
