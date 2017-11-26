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
    $router->get('/tutors/searchBy/{key}/{value}', ['uses' => 'TutorsController@getTutorBy']);
    
    $router->post('/tutors', ['uses' => 'TutorsController@createTutor']);

    $router->put('/tutors/{id}', ['uses' => 'TutorsController@updateTutor']);
    $router->delete('/tutors/{id}', ['uses' => 'TutorsController@deleteTutor']);

    // Sections
    $router->get('/sections', ['uses' => 'SectionsController@getAll']);
    $router->get('/sections/{id}', ['uses' => 'SectionsController@getSection']);
    $router->get('/sections/search/{term}[/limit/{limit}]', ['uses' => 'SectionsController@getSectionByTerm']);
    $router->get('/sections/searchBy/{key}/{value}', ['uses' => 'SectionsController@getSectionBy']);
    
    $router->post('/sections', ['uses' => 'SectionsController@createSection']);

    $router->put('/sections/{id}', ['uses' => 'SectionsController@updateSection']);
    $router->delete('/sections/{id}', ['uses' => 'SectionsController@deleteSection']);

    // Students
    $router->get('/students', ['uses' => 'StudentsController@getAll']);
    $router->get('/students/{id}', ['uses' => 'StudentsController@getStudent']);
    $router->get('/students/search/{term}[/limit/{limit}]', ['uses' => 'StudentsController@getStudentByTerm']);
    $router->get('/students/searchBy/{key}/{value}', ['uses' => 'StudentsController@getStudentBy']);
    
    $router->post('/students', ['uses' => 'StudentsController@createStudent']);

    $router->put('/students/{id}', ['uses' => 'StudentsController@updateStudent']);
    $router->delete('/students/{id}', ['uses' => 'StudentsController@deleteStudent']);
});

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/key', function () {
    return str_random(32);
});
