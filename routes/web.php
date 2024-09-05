<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/todos', 'TodosController@getTodos');
$router->get('/todos/{todo}', 'TodosController@getTodo');
$router->post('/todos', 'TodosController@postTodo');
$router->put('/todos/{todo}', 'TodosController@putTodo');
$router->patch('/todos/{todo}/status/{status}', 'TodosController@patchTodoStatus');
$router->delete('/todos/{todo}', 'TodosController@deleteTodo');
