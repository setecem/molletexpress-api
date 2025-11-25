<?php

use App\Controller\Factura;
use Cavesman\Router;

Router::mount('/api/v1/invoice', function () {

    /** @see Factura::filterAll() */
    Router::get('/all/filter', Factura::class . '@filterAll');

    /** @see Factura::filter() */
    Router::get('/(\d+)/filter', Factura::class . '@filter');

    /** @see Factura::get() */
    Router::get('/(\d+)', Factura::class . '@get');

    /** @see Factura::active() */
    Router::get('/(\d+)/active', Factura::class . '@active');

    /** @see Factura::add() */
    Router::post('/', Factura::class . '@add');

    /** @see Factura::update() */
    Router::put('/(\d+)', Factura::class . '@update');

    /** @see Factura::delete() */
    Router::delete('/(\d+)', Factura::class . '@delete');

});
