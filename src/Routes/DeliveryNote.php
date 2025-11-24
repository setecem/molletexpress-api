<?php

use App\Controller\Albaran;
use Cavesman\Router;

Router::mount('/api/v1/delivery-note', function () {

    // Obtener todos los origenes
    /** @see Albaran::listOrigen() */
    Router::get('/origen', Albaran::class . '@listOrigen');

    /** @see Albaran::filterAll() */
    Router::get('/all/filter', Albaran::class . '@filterAll');

    /** @see Albaran::filter() */
    Router::get('/(\d+)/filter', Albaran::class . '@filter');

    /** @see Albaran::get() */
    Router::get('/(\d+)', Albaran::class . '@get');

    /** @see Albaran::active() */
    Router::get('/(\d+)/active', Albaran::class . '@active');

    /** @see Albaran::add() */
    Router::post('/', Albaran::class . '@add');

    /** @see Albaran::update() */
    Router::put('/(\d+)', Albaran::class . '@update');

    /** @see Albaran::delete() */
    Router::delete('/(\d+)', Albaran::class . '@delete');


    //  AlbaranFeed

    /** @see Albaran::filterAllFeed() */
    Router::get('/all/(\d+)/filter', Albaran::class . '@filterAllFeed');

    /** @see Albaran::filterPublicFeed() */
    Router::get('/public/(\d+)/filter', Albaran::class . '@filterPublicFeed');

    /** @see Albaran::getFeed() */
    Router::get('/feed/(\d+)', Albaran::class . '@getFeed');

    /** @see Albaran::addFeed() */
    Router::post('/feed', Albaran::class . '@addFeed');

    /** @see Albaran::updateFeed() */
    Router::put('/feed/(\d+)', Albaran::class . '@updateFeed');

    /** @see Albaran::deleteFeed() */
    Router::delete('/feed/(\d+)', Albaran::class . '@deleteFeed');

    // AlbaranFeedAlert

    /** @see Albaran::addAlert() */
    Router::post('/alert', Albaran::class . '@addAlert');

    /** @see Albaran::updateAlert() */
    Router::put('/alert/(\d+)', Albaran::class . '@updateAlert');

    /** @see Albaran::getAlert() */
    Router::get('/alert/(\d+)', Albaran::class . '@getAlert');
});
