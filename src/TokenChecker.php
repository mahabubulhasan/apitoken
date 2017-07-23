<?php
namespace Uzzal\ApiToken;

use Closure;
/**
 *
 * @author Mahabubul Hasan <codehasan@gmail.com>
 */
class TokenChecker
{
    /**
     *
     * @var AuthService
     */
    private $service;

    public function __construct(AuthService $service) {
        $this->service = $service;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $token = $request->get('_token');

        if (!$this->service->isValidToken($token)) {
            return response(['status'=>'failed','msg'=>'Unauthorized or Invalid token'], 401);
        }

        return $next($request);
    }
}