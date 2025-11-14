<?php

use App\Controller\Employee;
use Cavesman\Router;


Router::mount('/api/v1/employee', function () {

    /** @see Employee::list() */
    Router::get('/', Employee::class . '@list');

    /** @see Employee::filterAll() */
    Router::get('/all/filter', Employee::class . '@filterAll');

    /** @see Employee::filter() */
    Router::get('/(\d+)/filter', Employee::class . '@filter');

    /** @see Employee::get() */
    Router::get('/(\d+)', Employee::class . '@get');

    /** @see Employee::active() */
    Router::get('/(\d+)/active', Employee::class . '@active');

    /** @see Employee::add() */
    Router::post('/', Employee::class . '@add');

    /** @see Employee::update() */
    Router::put('/(\d+)', Employee::class . '@update');

    /** @see Employee::delete() */
    Router::delete('/(\d+)', Employee::class . '@delete');

});