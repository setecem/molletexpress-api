<?php

use App\Controller\Customer;
use Cavesman\Router;

Router::mount('/api/v1/customer', function () {

    /** @see Customer::list() */
    Router::get('/', Customer::class . '@list');

    /** @see Customer::filterAll() */
    Router::get('/all/filter', Customer::class . '@filterAll');

    /** @see Customer::filter() */
    Router::get('/(\d+)/filter', Customer::class . '@filter');

    /** @see Customer::get() */
    Router::get('/(\d+)', Customer::class . '@get');

    /** @see Customer::active() */
    Router::get('/(\d+)/active', Customer::class . '@active');

    /** @see Customer::add() */
    Router::post('/', Customer::class . '@add');

    /** @see Customer::update() */
    Router::put('/(\d+)', Customer::class . '@update');

    /** @see Customer::delete() */
    Router::delete('/(\d+)', Customer::class . '@delete');
});