<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 4/12/2018
 * Time: 2:06 PM
 */


Route::group( [ 'namespace' => 'Backend' ], function () {

    Route::group([
        'namespace' => 'Access',
        'prefix' => 'access',
        'middleware' => 'permission:system.user.manager'
    ],function (){

        // USER
        Route::get('/user-manager', [
            'as' => 'access.user.manager',
            'uses' => 'UserController@index'
        ]);

        Route::match(['GET', 'POST'], '/user/create', [
            'as' => 'access.user.create',
            'uses' => 'UserController@create'
        ]);

        Route::match(['GET', 'POST'], '/user/update/{id}', [
            'as' => 'access.user.update',
            'uses' => 'UserController@update'
        ]);

        // ROLES

        Route::get('/role-manager', [
            'as' => 'access.role.manager',
            'uses' => 'AccessController@role_manager'
        ]);

        Route::match(['GET', 'POST'], '/role/create', [
            'as' => 'access.role.create',
            'uses' => 'AccessController@role_create'
        ]);

        Route::match(['GET', 'POST'], '/role/update/{id}', [
            'as' => 'access.role.update',
            'uses' => 'AccessController@role_update'
        ]);


        // PERMISSION
        Route::get('/permission-manager', [
            'as' => 'access.permission.manager',
            'uses' => 'AccessController@permission_manager'
        ]);

        Route::match(['POST', 'GET'], '/permission/create', [
            'as' => 'access.permission.create',
            'uses' => 'AccessController@permission_create'
        ]);

        Route::match(['POST', 'GET'], '/permission/update/{id}', [
            'as' => 'access.permission.update',
            'uses' => 'AccessController@permission_update'
        ]);


        // PERMISSION GROUP
        Route::get('/permission-group-manager', [
            'as' => 'access.permission_group.manager',
            'uses' => 'AccessController@permission_group_manager'
        ]);

        Route::match(['POST', 'GET'], '/permission-group/create', [
            'as' => 'access.permission_group.create',
            'uses' => 'AccessController@permission_group_create'
        ]);

        Route::match(['POST', 'GET'], '/permission-group/update/{id}', [
            'as' => 'access.permission_group.update',
            'uses' => 'AccessController@permission_group_update'
        ]);

    });


    Route::group(['prefix' => 'backend',
        'middleware' => 'permission:admin.view'], function (){

        Route::get('/dashboard', [
            'as' => 'backend.app.dashboard',
            'uses' => 'AppController@index'
        ]);


        /*----------Backend setting router------------*/
        Route::group(['middleware' => 'permission:admin.view'], function(){

            Route::get('/setting/attribute', [
                'as' => 'backend.setting.attribute',
                'uses' => 'EavAttributeController@index',
            ]);

        });



    });
});

