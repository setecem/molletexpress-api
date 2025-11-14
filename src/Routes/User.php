<?php

use App\Controller\User;
use Cavesman\Router;

Router::mount('/api/v1/user', function () {

    /** @see User::list() */
    Router::get('/', User::class . '@list');

    /** @see User::get() */
    Router::get('/(\d+)', User::class . '@get');

    /** @see User::active() */
    Router::get('/(\d+)/active', User::class . '@active');

    /** @see User::add() */
    Router::post('/', User::class . '@add');

    /** @see User::update() */
    Router::put('/(\d+)', User::class . '@update');

    /** @see User::delete() */
    Router::delete('/(\d+)', User::class . '@delete');

});
