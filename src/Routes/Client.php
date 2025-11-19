<?php

use App\Controller\Client;
use Cavesman\Router;

Router::mount('/api/v1/client', function () {

    /** @see Client::list() */
    Router::get('/', Client::class . '@list');

    /** @see Client::get() */
    Router::get('/(\d+)', Client::class . '@get');

    /** @see Client::active() */
    Router::get('/(\d+)/active', Client::class . '@active');

    /** @see Client::add() */
    Router::post('/', Client::class . '@add');

    /** @see Client::update() */
    Router::put('/(\d+)', Client::class . '@update');

    /** @see Client::delete() */
    Router::delete('/(\d+)', Client::class . '@delete');
});