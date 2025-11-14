<?php

use App\Controller\Service;
use Cavesman\Router;

Router::mount('/api/v1/service', function () {

    /** @see Service::list() */
    Router::get('/', Service::class . '@list');

    /** @see Service::get() */
    Router::get('/(\d+)', Service::class . '@get');

});
