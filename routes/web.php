<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//
Route::get('/', 'HomeController@redirectWithLocale')->name('home');
Route::get('/faq', 'FaqController@index')->name('faq');
Route::get('/faq/{url}', 'FaqController@category')->name('faq_category');
Route::post('/faq/search', 'FaqController@search')->name('faq_search');

Route::get('/terms-of-use', 'FaqController@servicePage')->name('terms-of_use');
Route::get('/privacy-policy', 'FaqController@servicePage')->name('privacy_policy');
Route::get('/public-offer', 'FaqController@servicePage')->name('public_offer');
Route::get('/contact-us', 'FaqController@servicePage')->name('contact_us');
//Route::prefix('{locale}')->get('/', 'HomeController@redirectWithLocale')->where(['locale' => '[a-zA-Z]{2}']);

Route::group( [
	'prefix' => '{locale?}',
    'where'      => [ 'locale' => '^[a-z]{2}$' ],
	'middleware' => 'setlocale',
], function () {
    Route::get('/', 'HomeController@redirectWithLocale')->name('home');
    Route::get('/faq', 'FaqController@index')->name('faq');
    Route::get('/faq/{url}', 'FaqController@category')->name('faq_category');
    Route::post('/faq/search', 'FaqController@search')->name('faq_search');

    Route::get('/terms-of-use', 'FaqController@servicePage')->name('terms_of_use');
    Route::get('/privacy-policy', 'FaqController@servicePage')->name('privacy_policy');
    Route::get('/public-offer', 'FaqController@servicePage')->name('public_offer');
    Route::get('/contact-us', 'FaqController@servicePage')->name('contact_us');
} );


