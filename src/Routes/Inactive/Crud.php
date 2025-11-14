<?php

use Cavesman\Router;

Router::mount('/'."crud", function() {
    Router::get('/', self::class . '@render');
    Router::post('/saveCrud', self::class . '@save');
    Router::post('/getEntityColumns', self::class . '@getEntityColumns');
});