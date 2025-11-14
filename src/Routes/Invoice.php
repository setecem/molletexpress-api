<?php

use App\Controller\Invoice;
use Cavesman\Router;

Router::mount('/api/v1/invoice', function () {

    // Obtener todos los origenes
    /** @see Invoice::listOrigen() */
    Router::get('/origen', Invoice::class . '@listOrigen');

    /** @see Invoice::filterAll() */
    Router::get('/all/filter', Invoice::class . '@filterAll');

    /** @see Invoice::filter() */
    Router::get('/(\d+)/filter', Invoice::class . '@filter');

    /** @see Invoice::get() */
    Router::get('/(\d+)', Invoice::class . '@get');

    /** @see Invoice::active() */
    Router::get('/(\d+)/active', Invoice::class . '@active');

    /** @see Invoice::add() */
    Router::post('/', Invoice::class . '@add');

    /** @see Invoice::update() */
    Router::put('/(\d+)', Invoice::class . '@update');

    /** @see Invoice::delete() */
    Router::delete('/(\d+)', Invoice::class . '@delete');


    //  InvoiceFeed

    /** @see Invoice::filterAllFeed() */
    Router::get('/all/(\d+)/filter', Invoice::class . '@filterAllFeed');

    /** @see Invoice::filterPublicFeed() */
    Router::get('/public/(\d+)/filter', Invoice::class . '@filterPublicFeed');

    /** @see Invoice::getFeed() */
    Router::get('/feed/(\d+)', Invoice::class . '@getFeed');

    /** @see Invoice::addFeed() */
    Router::post('/feed', Invoice::class . '@addFeed');

    /** @see Invoice::updateFeed() */
    Router::put('/feed/(\d+)', Invoice::class . '@updateFeed');

    /** @see Invoice::deleteFeed() */
    Router::delete('/feed/(\d+)', Invoice::class . '@deleteFeed');

    // InvoiceFeedAlert

    /** @see Invoice::addAlert() */
    Router::post('/alert', Invoice::class . '@addAlert');

    /** @see Invoice::updateAlert() */
    Router::put('/alert/(\d+)', Invoice::class . '@updateAlert');

    /** @see Invoice::getAlert() */
    Router::get('/alert/(\d+)', Invoice::class . '@getAlert');
});
