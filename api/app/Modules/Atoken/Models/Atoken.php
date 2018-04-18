<?php

namespace App\Modules\Atoken\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\Tools;
use Carbon\Carbon as Carbon;

class Atoken extends Model{

    public $timestamps = false;

    # use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tokens';

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token',
        'user_id',
        'admin_id',
        'role',
        'role_type',
        'fingerprint'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function parent(){
        if($this->admin_id){
            return $this->belongsTo('App\Modules\Admin\Models\Admin', 'admin_id');
        }
        return $this->belongsTo('App\Modules\User\Models\User', 'user_id');
    }

    public function validate($data){
        // make a new validator object
        $v = Validator::make($data, $this->rules);

         // check for failure
        if ($v->fails()){
            // set errors and return false
            $this->errors = $v->errors;
            return false;
        }

        // validation pass
        return true;
    }

    public static function newToken($userId, $fingerprint, $role){
        # Remove all token assoiciate with input user;
        $key = $role->role_type_uid . '_id';
        $params = [
            $key => $userId,
            'role' => $role->uid,
            'role_type' => $role->role_type_uid,
            'fingerprint' => $fingerprint
        ];
        DB::transaction(function() use($params, $key, $userId){
            Atoken::where($key, $userId)->delete();
            Atoken::create($params);
        }, 5);
        $item = Atoken::where($key, $userId)->first();
        return $item;
        # return $item->token;
    }

    public static function checkExpired($token){
        try{
            # var_dump($token->created_at, Carbon::now());die;
            $currentDate = Carbon::now();
            $tokenCreatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $token->created_at);
            $tokenLife = $currentDate->diffInMinutes($tokenCreatedAt);
            if($tokenLife > \Config::get('app.token_life')){
                return true;
            }
            return false;
        }catch(\Error $e){
            return true;
        }catch(\Exception $e){
            return true;
        }
    }

    public function save(array $options = array()){
        # Auto increase order (if order exist)
        $colums = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        $this->created_at = Tools::nowDateTime();
        if(!$this->exists){
            $this->token = str_random(36);

            if(self::count() > 0){
                $largestIdItem = self::orderBy('id', 'desc')->first();
                $this->id = $largestIdItem->id + 1;
            }else{
                $this->id = 1;
            }

            if(in_array('order', $colums)){
                $largestOrderItem = self::orderBy('order', 'desc')->first();
                $order = 1;
                if($largestOrderItem){
                    $order = $largestOrderItem->order + 1;
                }
                $this->order = $order;
            }
        }
        // before save code
        parent::save();
        // after save code
    }
}
