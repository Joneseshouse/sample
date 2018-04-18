<?php

namespace App\Modules\LoginFailed\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\Tools;
use Carbon\Carbon as Carbon;

class LoginFailed extends Model{

    # use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'login_failed_logs';

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'fingerprint',
        'fail_count',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public static function newItem($email, $fingerprint){
        # Remove half of table when it reach limit.
        $maxFailRecord = \Config::get('app.max_fail_record');
        $totalCount = self::count();
        if($totalCount >= $maxFailRecord){
            var_dump('here');die;
            $listRecord = self::orderBy('id')->limit($maxFailRecord/2);
            foreach($listRecord as $record){
                $record->remove;
            }
        }
        # Insert new record
        $params = [
            'email' => $email,
            'fingerprint' => $fingerprint,
            'fail_count' => 1
        ];
        LoginFailed::create($params);
        $item = LoginFailed::where('email', $email)->where('fingerprint', $fingerprint)->first();
        return $item;
    }

    public static function updateItem($email, $fingerprint){
        $item = LoginFailed::where('email', $email)->where('fingerprint', $fingerprint)->first();
        if($item){
            $item->fail_count = $item->fail_count + 1;
            $item->save();
        }else{
            $item = self::newItem($email, $fingerprint);
        }
        return $item;
    }

    public static function removeItem($email, $fingerprint){
        $item = LoginFailed::where('email', $email)->where('fingerprint', $fingerprint)->first();
        if($item){
            $item->delete();
        }
        return null;
    }

    public static function checkAllowLogin($email, $fingerprint){
        try{
            # Blog when fail_count >= max_fail AND current - updated_at < 15 mins
            $item = LoginFailed::where('email', $email)->where('fingerprint', $fingerprint)->first();
            if(!$item){
                return true;
                // $item = self::newItem($email, $fingerprint);
            }
            $currentDate = Carbon::now();
            $itemUpdatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $item->updated_at);
            $itemLife = $currentDate->diffInMinutes($itemUpdatedAt);
            if($item->fail_count >= \Config::get('app.max_fail') && $itemLife < \Config::get('app.waiting_on_max_fail')){
                return false;
            }
            if($item->fail_count >= \Config::get('app.max_fail')){
                $item->fail_count = 0;
                $item->save();
            }
            return true;
        }catch(\Error $e){
            return true;
        }
    }

    public function save(array $options = array()){
        # Auto increase order (if order exist)
        $colums = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        $this->updated_at = Tools::nowDateTime();
        if(!$this->exists){
            $this->created_at = Tools::nowDateTime();
            if(self::count() > 0){
                $largestIdItem = self::orderBy('id', 'desc')->first();
                $this->id = $largestIdItem->id + 1;
            }else{
                $this->id = 1;
            }
            if(in_array('order', $colums)){
                if($this->order === 0){
                    $largestOrderItem = self::orderBy('order', 'desc')->first();
                    if($largestOrderItem){
                        $this->order = $largestOrderItem->order + 1;
                    }else{
                        $this->order = 1;
                    }
                }
            }
        }
        // before save code
        parent::save();
        // after save code
    }
}
