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
    ->group(function() {

        Route::prefix('{idp}/')->group(function() {

            Route::get('logout', array(
                'as' => 'logout',
                'uses' => config('saml.controller').'@logout',
            ));

            Route::get('login', array(
                'as' => 'login',
                'uses' => config('saml.controller').'@login',
            ));

            Route::get('metadata', array(
                'as' => 'metadata',
                'uses' => config('saml.controller').'@metadata',
            ));

            Route::post('acs', array(
                'as' => 'acs',
                'uses' => config('saml.controller').'@acs',
            ));

            Route::get('sls', array(
                'as' => 'sls',
                'uses' => config('saml.controller').'@sls',
            ));

        });

    });
