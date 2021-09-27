<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', 'Api\AuthController@register');
Route::post('/login', 'Api\AuthController@login');

Route::group(['middleware' => ['auth:api']], function() {
  Route::get('/linnworks', 'API\LinnworksController@index');
});