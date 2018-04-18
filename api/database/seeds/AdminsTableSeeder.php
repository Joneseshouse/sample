<?php

use Illuminate\Database\Seeder;

class AdminsTableSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        $defaultPassword = md5('griever87');
        for($i=0;$i<30;$i++){
            DB::table('admins')->insert([
                'first_name' => "Son $i",
                'last_name' => 'Tran',
                'email' => "tbson87$i@gmail.com",
                'password' => \Hash::make($defaultPassword)
            ]);
        }
    }
}
