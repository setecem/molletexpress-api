<?php

use App\Controller\File;
use Cavesman\Router;

Router::mount('/api/v1/file', function () {

    /** @see File::upload */
    Router::post('/', File::class . '@upload');

    /** @see File::uploadInDataBase() */
    Router::put('/(\d+)', File::class . '@uploadInDataBase');

    /** @see File::download */
    Router::get('/(\d+)', File::class . '@download');

    /** @see File::delete */
    Router::delete('/(\d+)', File::class . '@delete');

});