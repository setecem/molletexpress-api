<?php

use App\Controller\Factura;
use Cavesman\Router;

Router::mount('/api/v1/invoice', function () {

    // Obtener todos los origenes
    /** @see Factura::listOrigen() */
    Router::get('/origen', Factura::class . '@listOrigen');

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


    //  FacturaFeed

    /** @see Factura::filterAllFeed() */
    Router::get('/all/(\d+)/filter', Factura::class . '@filterAllFeed');

    /** @see Factura::filterPublicFeed() */
    Router::get('/public/(\d+)/filter', Factura::class . '@filterPublicFeed');

    /** @see Factura::getFeed() */
    Router::get('/feed/(\d+)', Factura::class . '@getFeed');

    /** @see Invoice::addFeed() */
    Router::post('/feed', Invoice::class . '@addFeed');

    /** @see Invoice::updateFeed() */
    Router::put('/feed/(\d+)', Invoice::class . '@updateFeed');

    /** @see Invoice::deleteFeed() */
    Router::delete('/feed/(\d+)', Invoice::class . '@deleteFeed');

    // FacturaFeedAlert

    /** @see Invoice::addAlert() */
    Router::post('/alert', Invoice::class . '@addAlert');

    /** @see Invoice::updateAlert() */
    Router::put('/alert/(\d+)', Invoice::class . '@updateAlert');

    /** @see Invoice::getAlert() */
    Router::get('/alert/(\d+)', Invoice::class . '@getAlert');
});
