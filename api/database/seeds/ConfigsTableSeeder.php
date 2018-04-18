<?php

use Illuminate\Database\Seeder;

class ConfigsTableSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
    	foreach (range(0, 30) as $value) {
	        DB::table('configs')->insert([
	            'uid' => 'key-'.$value,
	            'value' => $value,
	        ]);
    	}
    }
}
