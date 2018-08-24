<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class CheckPermission
{
    private $auth;

    /**
     * Create a new filter instance.
     *
     * @param \Illuminate\Contracts\Auth\Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {

        if ($this->auth->check() && $this->auth->user()->canDo($permission)) {
            return $next($request);
        }

        return redirect('/backend/dashboard')->withFlashDanger('Bạn không có quyền truy cập. Khu vực bạn truy cập yêu cầu quyền: ' . $permission);
        // abort(403, 'You do not have permission');
    }
}
