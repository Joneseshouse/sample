<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        $defaultPassword = md5('griever87');
        DB::table('users')->insert([
            'first_name' => 'Son',
            'last_name' => 'Tran',
            'email' => 'tbson87@gmail.com',
            'password' => \Hash::make($defaultPassword),
            'activate' => true
        ]);
    }
}
