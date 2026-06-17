<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\ContactController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

$supportedLocales = ['fr', 'en', 'pl', 'es'];

Route::get('/', function (Request $request) use ($supportedLocales) {
    $locale = 'en';

    $header = $request->header('Accept-Language', '');
    if ($header) {
        $languages = [];
        foreach (explode(',', $header) as $part) {
            [$tag, $q] = array_pad(explode(';q=', trim($part)), 2, '1');
            $languages[strtolower(trim($tag))] = (float) $q;
        }
        arsort($languages);

        foreach (array_keys($languages) as $tag) {
            $short = substr($tag, 0, 2);
            if (in_array($short, $supportedLocales)) {
                $locale = $short;
                break;
            }
        }
    }

    return redirect("/{$locale}");
});

Route::group(['prefix' => '{locale}', 'middleware' => 'setLocale'], function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('home');


    Route::get('/simulate', function () {
        // return view('simulate');
        // return view('/#simulate');
        return Redirect::to('/#simulate');

    })->name('simulate');

    Route::get('/about', function () {
        return view('about');
    })->name('about');

    Route::get('/contact', function () {
        return view('contact');
    })->name('contact');

    Route::get('/apply-loan', function () {
        return view('apply-loan');
    })->name('loan');

    Route::get('/loan/complete', [LoanController::class, 'showDocuments'])->name('loan.complete');

    Route::get('/terms', function () {
        return view('terms');
    })->name('terms');

    Route::get('/privacy', function () {
        return view('privacy');
    })->name('privacy');

    Route::get('/faq', function () {
        return view('faq');
    })->name('faq');

    Route::get('/services', function () {
        return view('services');
    })->name('services');

    Route::get('/services/auto-loan', function () {
        return view('service-d-auto-loan');
    })->name('services.auto');

    Route::get('/services/personal-loan', function () {
        return view('service-d-personal-loan');
    })->name('services.personal');

    Route::get('/services/home-loan', function () {
        return view('service-d-home-loan');
    })->name('services.home');

    Route::get('/services/study-loan', function () {
        return view('service-d-study-loan');
    })->name('services.study');

    Route::get('/services/business-loan', function () {
        return view('service-d-business-loan');
    })->name('services.business');

    Route::get('/services/bike-loan', function () {
        return view('service-d-bike-loan');
    })->name('services.bike');

});
Route::post('/loan/simulate', [LoanController::class, 'simulate'])->name('loan.simulate');
Route::post('/contact/send', [ContactController::class, 'sendMail'])->name('contact.send');
Route::post('/subscribe/send', [ContactController::class, 'subscribeMail'])->name('subscribe.send');
Route::post('/loan/request', [LoanController::class, 'sendMail'])->name('loan.request');
Route::post('/loan/documents', [LoanController::class, 'sendDocuments'])->name('loan.documents');
