<?php

use App\Controller\DeliveryNote;
use Cavesman\Router;

Router::mount('/api/v1/delivery-note', function () {

    // Obtener todos los origenes
    /** @see DeliveryNote::listOrigen() */
    Router::get('/origen', DeliveryNote::class . '@listOrigen');

    /** @see DeliveryNote::filterAll() */
    Router::get('/all/filter', DeliveryNote::class . '@filterAll');

    /** @see DeliveryNote::filter() */
    Router::get('/(\d+)/filter', DeliveryNote::class . '@filter');

    /** @see DeliveryNote::get() */
    Router::get('/(\d+)', DeliveryNote::class . '@get');

    /** @see DeliveryNote::active() */
    Router::get('/(\d+)/active', DeliveryNote::class . '@active');

    /** @see DeliveryNote::add() */
    Router::post('/', DeliveryNote::class . '@add');

    /** @see DeliveryNote::update() */
    Router::put('/(\d+)', DeliveryNote::class . '@update');

    /** @see DeliveryNote::delete() */
    Router::delete('/(\d+)', DeliveryNote::class . '@delete');


    //  DeliveryNoteFeed

    /** @see DeliveryNote::filterAllFeed() */
    Router::get('/all/(\d+)/filter', DeliveryNote::class . '@filterAllFeed');

    /** @see DeliveryNote::filterPublicFeed() */
    Router::get('/public/(\d+)/filter', DeliveryNote::class . '@filterPublicFeed');

    /** @see DeliveryNote::getFeed() */
    Router::get('/feed/(\d+)', DeliveryNote::class . '@getFeed');

    /** @see DeliveryNote::addFeed() */
    Router::post('/feed', DeliveryNote::class . '@addFeed');

    /** @see DeliveryNote::updateFeed() */
    Router::put('/feed/(\d+)', DeliveryNote::class . '@updateFeed');

    /** @see DeliveryNote::deleteFeed() */
    Router::delete('/feed/(\d+)', DeliveryNote::class . '@deleteFeed');

    // DeliveryNoteFeedAlert

    /** @see DeliveryNote::addAlert() */
    Router::post('/alert', DeliveryNote::class . '@addAlert');

    /** @see DeliveryNote::updateAlert() */
    Router::put('/alert/(\d+)', DeliveryNote::class . '@updateAlert');

    /** @see DeliveryNote::getAlert() */
    Router::get('/alert/(\d+)', DeliveryNote::class . '@getAlert');
});
