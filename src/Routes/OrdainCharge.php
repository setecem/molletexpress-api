<?php

use App\Controller\OrdenCobro;
use Cavesman\Router;

Router::mount('/api/v1/charge-order', function () {

    /** @see OrdenCobro::list() */
    Router::get('/', OrdenCobro::class . '@list');

    /** @see OrdenCobro::get() */
    Router::get('/(\d+)', OrdenCobro::class . '@get');

    /** @see OrdenCobro::filterAll() */
    Router::get('/all/filter', OrdenCobro::class . '@filterAll');

    /** @see OrdenCobro::filter() */
    Router::get('/(\d+)/filter', OrdenCobro::class . '@filter');

    /** @see OrdenCobro::add() */
    Router::post('/', OrdenCobro::class . '@add');

    /** @see OrdenCobro::update() */
    Router::put('/(\d+)', OrdenCobro::class . '@update');

    /** @see OrdenCobro::delete() */
    Router::delete('/(\d+)', OrdenCobro::class . '@delete');

});
