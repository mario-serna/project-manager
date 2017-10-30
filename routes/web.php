<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['middleware' => []], function () use ($router) {
    $router->post('/users/login', ['uses' => 'UsersController@getToken']);
});

$router->group(['middleware' => ['auth']], function () use ($router) {

    //Users
    $router->get('/users', ['uses' => 'UsersController@getAll']);
    $router->get('/users/{id}', ['uses' => 'UsersController@getUser']);
    
    $router->post('/users', ['uses' => 'UsersController@createUser']);

    $router->put('/users/{id}', ['uses' => 'UsersController@updateUser']);
    $router->delete('/users/{id}', ['uses' => 'UsersController@deleteUser']);
});

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/key', function () {
    return str_random(32);
});
