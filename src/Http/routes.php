<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'pap',
    'namespace' => 'RazeSoldier\Seat3VPap\Http\Controller',
    'middleware' => ['web', 'auth', 'locale', 'bouncer:srp.request'],
], function () {
    Route::get('/', 'PapController@showMainPage');
    Route::get('/group/{id}', 'PapController@showGroupPap')->name('pap.pap');
    Route::get('/corporation/{id}', 'PapController@showCorporation')->name('pap.corp');
});
