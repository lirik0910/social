<?php

namespace App\Http\Middleware;

use App\Helpers\LanguageHelper;
use Closure;
use Illuminate\Http\Request;
use URL;

class SetLocale
{
	public function handle(Request $request, Closure $next)
	{
        $locale = LanguageHelper::LOCALE_EN;

	    $passed_param = $request->route()->parameter('locale');

        if (preg_match('/^[a-zA-Z]{2}$/', $passed_param)) {
            if (in_array($passed_param, LanguageHelper::availableParams('locale'))) {
                $locale = $passed_param;
            } else {
                abort(404);
            }
        }

        app()->setLocale($locale);

        $locale_param_for_urls = $locale === LanguageHelper::LOCALE_EN
            ? ''
            : $locale;

        URL::defaults(['locale' => $locale_param_for_urls]);

		return $next($request);
	}
}
