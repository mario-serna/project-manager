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
    $router->post('/users/login', ['uses' => 'UController@getToken']);
});

$router->group(['middleware' => ['auth']], function () use ($router) {

    //Users
    $router->get('/users', ['uses' => 'UController@getAll']);
    $router->get('/users/{id}', ['uses' => 'UController@getUser']);
    $router->get('/users/search/{term}[/limit/{limit}]', ['uses' => 'UController@getUsersByTerm']);
    
    $router->post('/users', ['uses' => 'UController@createUser']);

    $router->put('/users/{id}', ['uses' => 'UController@updateUser']);
    $router->delete('/users/{id}', ['uses' => 'UController@deleteUser']);

    //Tutors
    $router->get('/tutors', ['uses' => 'TutorsController@getAll']);
    $router->get('/tutors/{id}', ['uses' => 'TutorsController@getTutor']);
    $router->get('/tutors/search/{term}[/limit/{limit}]', ['uses' => 'TutorsController@getTutorsByTerm']);
    
    $router->post('/tutors', ['uses' => 'TutorsController@createTutor']);

    $router->put('/tutors/{id}', ['uses' => 'TutorsController@updateTutor']);
    $router->delete('/tutors/{id}', ['uses' => 'TutorsController@deleteTutor']);
});

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/key', function () {
    return str_random(32);
});
