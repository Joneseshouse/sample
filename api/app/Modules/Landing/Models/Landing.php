<?php

namespace App\Modules\Landing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Landing extends Model{
	public static function getRate(){
    	$result = [
            'rate' => 20
        ];
        return ResTools::obj($result);
    }
}
