<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class EnsureActiveAccount
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user(), 401);
        abort_unless($request->user()->is_active, 403, 'Tài khoản đã bị khóa.');
        return $next($request);
    }
}
