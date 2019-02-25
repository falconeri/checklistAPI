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

$router->get(
    '/', function () use ($router) {
    return $router->app->version();
}
);

$router->post('register', 'AuthController@register');
$router->post('login', 'AuthController@login');

$router->group(
    ['middleware' => 'auth'], function () use ($router) {
    /* Checklists */
    $router->get('checklists', 'ChecklistController@index');
    $router->post('checklists', 'ChecklistController@store');
    $router->get(
        'checklists/{checklistId}', [
            'as' => 'checklist.show', 'uses' => 'ChecklistController@show'
        ]
    );
    $router->patch('checklists/{checklistId}', 'ChecklistController@update');
    $router->delete('checklists/{checklistId}', 'ChecklistController@destroy');

    /* Items */
    $router->post('checklists/complete', 'ItemController@complete');
    $router->post('checklists/incomplete', 'ItemController@incomplete');
    $router->get(
        'checklists/{checklistId}/items', [
            'as' => 'item.index', 'uses' => 'ItemController@index'
        ]
    );
    $router->post('checklists/{checklistId}/items', 'ItemController@store');
    $router->get(
        'checklists/{checklistId}/items/{itemId}', [
            'as' => 'item.show', 'uses' => 'ItemController@show'
        ]
    );
    $router->patch('checklists/{checklistId}/items/{itemId}', 'ItemController@update');
    $router->delete('checklists/{checklistId}/items/{itemId}', 'ItemController@destroy');
}
);
