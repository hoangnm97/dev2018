<?php
/**
 * Created by PhpStorm.
 * User: EmoGun
 * Date: 23/08/2018
 * Time: 10:37
 */

Route::group( [ 'namespace' => 'Frontend' ], function () {

    Route::get('/index', function () {
        return view('frontend/index');
    });

    Route::get('/signin', function () {
        return view('frontend/login/login');
    });

    Route::get('/detail', function () {
        return view('frontend/details/lead_detail');
    });

    Route::get('/explain-lead-detail', function () {
        return view('frontend/details/explain_lead_detail');
    });

    Route::get('/comfirm-after-call', function () {
        return view('frontend/details/comfirm_after_call');
    });

    Route::get('/search', function () {
        return view('frontend/search/search');
    });

    Route::get('/sort', function () {
        return view('frontend/search/sort');
    });

    Route::get('/filter', function () {
        return view('frontend/search/filter');
    });

});