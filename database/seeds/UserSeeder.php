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
        DB::table('users')->insert([
            'username' => '_admin',
            'password' =>  bcrypt('123456'),
            'name' => 'testAdmin',
            'email' => 'charatsu98@gmail.com',
            'phone' => '123123123',
            'roleId' => 1
        ]);
        DB::table('users')->insert([
            'username' => 'employee',
            'password' =>  bcrypt('123456'),
            'name' => 'employee',
            'email' => 'email0@gmail.com',
            'phone' => '123123123',
            'roleId'=> 2,
        ]);
        DB::table('users')->insert([
            'username' => 'user0',
            'password' =>  bcrypt('123456'),
            'name' => 'employee',
            'email' => 'lehoangminhtam100498@gmail.com',
            'phone' => '123123123',
            'roleId'=> 3,
        ]);
        $limit = 8;
        for ($i = 1; $i <= $limit; $i++) {
            DB::table('users')->insert([
                'username' => 'user'.$i,
                'password' =>  bcrypt('123456'),
                'name' => 'name'.$i,
                'email' => 'email'.$i.'@gmail.com',
                'phone' => '123123123',
            ]);
        };
        DB::table('users')->insert([
            'username' => 'user9',
            'password' =>  bcrypt('123456'),
            'name' => 'test1',
            'email' => 'charatsu98@gmail.com',
            'phone' => '123123123',
            'roleId'=> 3,
        ]);
        
    }
}
