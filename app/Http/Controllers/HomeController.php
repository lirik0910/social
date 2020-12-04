<?php

namespace App\Http\Controllers;

use App\Helpers\LanguageHelper;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function redirectWithLocale(Request $request)
    {
        return view('home', [
            'available_locales' => LanguageHelper::getLocaleChangeUrls($request->decodedPath())
        ]);
        //return redirect(app()->getLocale());
    }
}
