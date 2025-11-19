<?php

use App\Controller\Service;
use Cavesman\Router;

Router::mount('/api/v1/service', function () {

    /** @see Service::list() */
    Router::get('/', Service::class . '@list');

    /** @see Service::get() */
    Router::get('/(\d+)', Service::class . '@get');

    /** @see Service::filterAll() */
    Router::get('/all/filter', Service::class . '@filterAll');

    /** @see Service::filter() */
    Router::get('/(\d+)/filter', Service::class . '@filter');

    /** @see Service::add() */
    Router::post('/', Service::class . '@add');

    /** @see Service::update() */
    Router::put('/(\d+)', Service::class . '@update');

    /** @see Service::delete() */
    Router::delete('/(\d+)', Service::class . '@delete');

});
