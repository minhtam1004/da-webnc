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
        $faker = Faker\Factory::create();

        $limit = 10;
        for ($i = 0; $i < $limit; $i++) {
            DB::table('users')->insert([
                'username' => $faker->userName,
                'password' =>  hash('sha256',$faker->password),
                'name' => $faker->name,
                'email' => $faker->unique()->email,
                'birthday' => Carbon::parse($faker->dateTimeBetween('1990-01-01', '2020-05-23')),
                'phone' => $faker->phoneNumber,
                'address' => $faker->address
            ]);
        }
    }
}
