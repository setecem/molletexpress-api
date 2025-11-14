<?php

use App\Controller\OrdainCharge;
use Cavesman\Router;

Router::mount('/api/v1/ordain-charge', function () {

    /** @see Service::list() */
    Router::get('/', OrdainCharge::class . '@list');

    /** @see Service::get() */
    Router::get('/(\d+)', OrdainCharge::class . '@get');

});
