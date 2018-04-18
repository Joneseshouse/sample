<?php

namespace App\Http\Middleware;

use Closure;
use App\Modules\Atoken\Models\Atoken as Atoken;
use App\Helpers\ResTools as ResTools;
use App\Helpers\Tools as Tools;
use Carbon\Carbon as Carbon;

class TokenUserCheck{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        # Action here
        if($request->method() == 'OPTIONS'){
            return $next($request);
        }

        $authHeader = $request->header('Authorization');
        $fingerprint = $request->header('Fingerprint');

        if($authHeader && $fingerprint){
            $tokens = explode(' ', $authHeader);
            if(count($tokens) == 2 && in_array($tokens[0], ['Token', 'Bearer'])){
                $token = $tokens[1];
                $token = Atoken::where('token', $token)->where('fingerprint', $fingerprint)->first();
                if($token && $token->role_type === 'user'){
                    $request->token = $token;
                    return $next($request);
                }
            }
        }
        return $next($request);
    }
}