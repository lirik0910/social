<?php

namespace App\Http\Middleware;

use Closure;

class GraphQLLocaleMiddleware
{
    /**
     * Set response language for GraphQL endpoint by custom header
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
	{
		app()->setLocale($request->header('App-Language', 'en'));
		return $next($request);
	}
}
