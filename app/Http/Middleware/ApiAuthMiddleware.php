<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
              //Comprobe if User is AUTH
              $token = $request->header('Authorization');
              $jwtAuth = new \JwtAuth();
              $checkToken = $jwtAuth->checkToken($token);   
              if($checkToken){
                return $next($request);
              }else{
                $data = array(
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'Auth Invalid'
                );
                return response()->json($data, $data['code']);
            }
    }
}
