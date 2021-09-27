<?php

use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
    'namespace' => 'Admin',
    'middleware' => ['auth']
], function () {
    Route::view('/', 'admin.layouts.master');
    
    Route::view('notifications-dropdown-menu', 'admin.layouts.notifications')->name('notifications-dropdown-menu');
    Route::get('/notificationMarkAsRead/{id}', 'DashboardController@notificationMarkAsRead');
    Route::get('/notificationMarkAllAsRead/{id}', 'DashboardController@notificationMarkAllAsRead');
    
    // Profile Routes
    Route::view('profile', 'admin.profile.index')->name('profile.index');;
    Route::view('profile/edit', 'admin.profile.edit')->name('profile.edit');
    Route::put('profile/edit', 'ProfileController@update')->name('profile.update');
    Route::put('profile/updateProfileImage', 'ProfileController@updateProfileImage')->name('profile.updateProfileImage');
    Route::view('profile/password', 'admin.profile.edit_password')->name('profile.edit.password');
    Route::post('profile/password', 'ProfileController@updatePassword')->name('profile.update.password');

    // User Routes
    
    Route::resource('/user', 'UserController');
    Route::get('user/ajax/change_status', 'UserController@change_status')->name('user.ajax.change_status'); // For change status

    // Role Routes
    Route::put('role/{id}/update', 'RoleController@update');
    Route::resource('role', 'RoleController');

    Route::resource('order', 'OrdersController');
    Route::get('order/ajax/data', 'OrdersController@datatables'); // For Datatables
    Route::get('order/ajax/data_supplier', 'OrdersController@datatables_supplier'); // For Datatables
    Route::get('order/ajax/change_status', 'OrdersController@change_status')->name('order.ajax.change_status'); // For change status
    Route::get('order/ajax/search_items', 'OrdersController@search_items')->name('order.ajax.search_items'); // For search items

});

