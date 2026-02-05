<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (
            session()->has('locale') &&
            in_array(session()->get('locale'), ['en', 'fr', 'ar', 'de'])
        ) {
            app()->setLocale(session()->get('locale'));
        }

        return $next($request);
    }
}
