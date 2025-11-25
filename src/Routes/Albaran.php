<?php

use App\Controller\Albaran;
use Cavesman\Router;

Router::mount('/api/v1/delivery-note', function () {

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

});
