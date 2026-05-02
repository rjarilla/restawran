<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckAdminSession
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('CheckAdminSession middleware triggered for URL: ' . $request->fullUrl());
        Log::info('Session data: ' . json_encode($request->session()->all()));
        Log::info('Session data: ' . json_encode(!session()->has('user_id') ? 'No user_id in session' : 'user_id exists in session'));
        if (!session()->has('user_id')) {
            return redirect()->route('admin.signin');
        }

        return $next($request);
    }
}
