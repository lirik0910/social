<?php

namespace App\Http\Controllers;

use App\Helpers\LanguageHelper;
use App\Models\FaqCategory;
use App\Models\FaqQuestion;
use App\Models\ServicePage;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class FaqController extends Controller
{
    protected $locale;

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \ReflectionException
     */
    public function index(Request $request)
    {
        $this->locale = app()->getLocale();

        $categories = $this->categories();

        $current_category = $categories->first();

        $questions = $current_category
            ->questions()
            ->orderBy('order', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        return view('pages.faq.layout', [
            'categories' => $categories,
            'questions' => $questions,
            'current_category' => $current_category ?? null,
            'available_locales' => LanguageHelper::getLocaleChangeUrls($request->decodedPath()),
        ]);
    }

    /**
     * @param Request $request
     * @param string $locale
     * @param string $slug
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \ReflectionException
     */
    public function category(Request $request)
    {
        $this->locale = app()->getLocale();

        $slug = Arr::last($request->segments());

        $categories = $this->categories();

        $current_category = $categories
            ->where('slug', $slug)
            ->first();

        if (empty($current_category)) {
            abort(404);
        }

        if (!empty($current_category) && count($current_category->questions) > 0) {
            $questions = $current_category->questions
                ->sortBy('order')
                ->all();
        }

        return view('pages.faq.layout', [
            'categories' => $categories,
            'questions' => $questions ?? [],
            'current_category' => $current_category,
            'available_locales' => LanguageHelper::getLocaleChangeUrls($request->decodedPath()),
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \ReflectionException
     */
    public function servicePage(Request $request)
    {
        $this->locale = app()->getLocale();

        $categories = $this->categories();

        $slug = Arr::last($request->segments());

        $current_category = ServicePage
            ::where('slug', $slug)
            ->where('locale', $this->locale)
            ->orderBy('order', 'ASC')
            ->orderBy('id', 'ASC')
            ->first();

        if (empty($current_category)) {
            abort(404);
        }

        return view('pages.faq.layout', [
            'categories' => $categories,
            'questions' => null,
            'current_category' => $current_category,
            'prefix' => LanguageHelper::getLocaleUrlPrefix(),
            'available_locales' => LanguageHelper::getLocaleChangeUrls($request->decodedPath()),
        ]);
    }

    public function search(Request $request)
    {
        $this->locale = app()->getLocale();

        $search_param = $request->get('search_param');
        $locale = $request->get('locale');

        $questions = !empty($search_param)
            ? FaqQuestion
                ::where('title', 'like', $search_param . '%')
                ->where('status', true)
                ->where('locale', $locale)
                ->orderBy('order', 'ASC')
                ->orderBy('id', 'ASC')
                ->get()
            : [];

        return view('pages/faq.parts.content', [
            'questions' => $questions,
            'search_param' => $search_param,
            'current_category' => null,
        ]);
    }


    protected function categories()
    {
        return FaqCategory
            ::where('locale', $this->locale)
            ->where('status', true)
            ->orderBy('order', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();
    }
}
