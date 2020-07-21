<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'pap',
    'namespace' => 'RazeSoldier\Seat3VPap\Http\Controller',
    'middleware' => ['web', 'auth', 'locale', 'bouncer:srp.request'],
], function () {
    Route::get('/', 'PapController@showMainPage')->name('pap.home');
    Route::get('/group/{id}', 'PapController@showGroupPap')->name('pap.pap');
    Route::get('/corporation/{id}', 'PapController@showCorporation')->name('pap.corp')->middleware('bouncer:pap.admin');
    Route::get('/fleet-stat', 'FleetStatController@showHome')->name('pap.stat')->middleware(['bouncer:pap.admin', 'bouncer:pap.FC']);
    Route::get('/download/corp/{id}', 'PapController@downloadCorpPapExecl')->name('pap.corp-execl')
        ->middleware('bouncer:pap.admin');
    // Api Routes:
    Route::get('/api/group/{id}', 'ApiGroupController@getGroupPap')->name('pap.api-group')->middleware('api');
    Route::post('/api/post-stat', 'FleetStatController@postStat')->name('pap.post-stat')
        ->middleware(['api', 'bouncer:pap.admin', 'bouncer:pap.FC']);
    Route::get('/api/corp/{id}', 'ApiCorpController@getCorpMemberPap')->name('pap.get-corppap')
        ->middleware(['api', 'bouncer:pap.admin']);
});
