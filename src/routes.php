<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SAML Routes
|--------------------------------------------------------------------------
|
| Here are all the routes defined that are needed for SAML authentication.
|
*/

Route::name('saml.')
    ->prefix(config('saml.routes_prefix').'/')
    ->group(function () {
        Route::prefix('{idp}/')->group(function () {
            Route::get('logout', [
                'as' => 'logout',
                'uses' => config('saml.controller').'@logout',
            ]);

            Route::get('login', [
                'as' => 'login',
                'uses' => config('saml.controller').'@login',
            ]);

            Route::get('metadata', [
                'as' => 'metadata',
                'uses' => config('saml.controller').'@metadata',
            ]);

            Route::post('acs', [
                'as' => 'acs',
                'uses' => config('saml.controller').'@acs',
            ]);

            Route::get('sls', [
                'as' => 'sls',
                'uses' => config('saml.controller').'@sls',
            ]);
        });
    });
