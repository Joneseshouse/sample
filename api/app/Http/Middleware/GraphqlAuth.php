<?php

namespace App\Http\Middleware;

use Closure;
use App\Modules\Atoken\Models\Atoken as Atoken;
use App\Helpers\ResTools as ResTools;
use App\Helpers\Tools as Tools;
use Carbon\Carbon as Carbon;

class GraphqlAuth{
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
        if($authHeader !== null){
            $tokens = explode(' ', $authHeader);
            if(count($tokens) == 2 && in_array($tokens[0], ['Token', 'Bearer'])){
                $token = $tokens[1];
                try{
                    $token = Atoken::where('token', $token)->where('fingerprint', $fingerprint)->first();
                    if($fingerprint && $token){
                        $tokenExpiredResult = Atoken::checkExpired($token);
                        if(gettype($tokenExpiredResult) == 'boolean'){
                            if($tokenExpiredResult == false){
                                $currentRoute = \Route::getCurrentRoute()->uri();
                                $allowRoutes = explode(',', $token->parent->permissions);
                                $allow = false;
                                if(in_array($currentRoute, $allowRoutes)){
                                    $allow = true;
                                }
                                // if(in_array($token->role, \Config::get('app.admin_roles'))){
                                if($token->role_type === 'admin'){
                                    # Admin user
                                    // if(in_array($token->role, \Config::get('app.admin_roles'))){
                                    if($token->role_type === 'admin'){
                                        if($token->role !== config('app.sadmin') && !$allow){
                                            # Not super admin -> check permission
                                            return response()->json(ResTools::err(
                                                trans('auth.not_enough_permission'),
                                                ResTools::$ERROR_CODES['FORBIDEN']
                                            ));
                                        }
                                        # Renew token
                                        $token->created_at = Carbon::now();
                                        $token->save();

                                        $request->token = $token;
                                        return $next($request);
                                    }
                                    return response()->json(ResTools::err(
                                        trans('auth.not_enough_permission'),
                                        ResTools::$ERROR_CODES['FORBIDEN']
                                    ));
                                // }else if(in_array($token->role, \Config::get('app.user_roles'))){
                                }else if($token->role_type === 'user'){
                                    # Normal user
                                    # Renew token
                                    if(!$allow){
                                        # Not super admin -> check permission
                                        return response()->json(ResTools::err(
                                            trans('auth.not_enough_permission'),
                                            ResTools::$ERROR_CODES['FORBIDEN']
                                        ));
                                    }
                                    $token->created_at = Carbon::now();
                                    $token->save();

                                    $request->token = $token;
                                    return $next($request);
                                }else{
                                    # Other user
                                    # Kick out
                                    $token->delete();
                                    return response()->json(ResTools::err(
                                        trans('auth.session_expired'),
                                        ResTools::$ERROR_CODES['UNAUTHORIZED']
                                    ));
                                }
                            }
                            # Remove old token
                            $token->delete();
                            return response()->json(ResTools::err(
                                trans('auth.session_expired'),
                                ResTools::$ERROR_CODES['UNAUTHORIZED']
                            ));
                        }
                        return response()->json(ResTools::err($tokenExpiredResult));
                    }else{
                        return response()->json(ResTools::err(
                            trans('auth.login_required'),
                            ResTools::$ERROR_CODES['UNAUTHORIZED']
                        ));
                    }
                }catch(\Exception $e){
                    return response()->json(ResTools::err(
                        trans('auth.login_required'),
                        ResTools::$ERROR_CODES['UNAUTHORIZED']
                    ));
                }
            }else{
                return response()->json(ResTools::err(
                    trans('auth.login_required'),
                    ResTools::$ERROR_CODES['UNAUTHORIZED']
                ));
            }
        }
        return response()->json(ResTools::err(
            trans('auth.login_required'),
            ResTools::$ERROR_CODES['UNAUTHORIZED']
        ));
    }
}
