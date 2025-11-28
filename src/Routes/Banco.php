<?php

use App\Controller\Banco;
use Cavesman\Router;

Router::mount('/api/v1/bank', function () {

    /** @see Banco::list() */
    Router::get('/', Banco::class . '@list');

});
