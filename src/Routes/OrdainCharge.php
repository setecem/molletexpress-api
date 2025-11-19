<?php

use App\Controller\OrdainCharge;
use Cavesman\Router;

Router::mount('/api/v1/ordain-charge', function () {

    /** @see OrdainCharge::list() */
    Router::get('/', OrdainCharge::class . '@list');

    /** @see OrdainCharge::get() */
    Router::get('/(\d+)', OrdainCharge::class . '@get');

    /** @see OrdainCharge::filterAll() */
    Router::get('/all/filter', OrdainCharge::class . '@filterAll');

    /** @see OrdainCharge::filter() */
    Router::get('/(\d+)/filter', OrdainCharge::class . '@filter');

    /** @see OrdainCharge::add() */
    Router::post('/', OrdainCharge::class . '@add');

    /** @see OrdainCharge::update() */
    Router::put('/(\d+)', OrdainCharge::class . '@update');

    /** @see OrdainCharge::delete() */
    Router::delete('/(\d+)', OrdainCharge::class . '@delete');

});
