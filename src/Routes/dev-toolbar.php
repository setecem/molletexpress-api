<?php

use Cavesman\Router;


Router::mount("/api/v1/lang", function (): void {

    /** @see App\Controller\DevToolLang::lang() */
    Router::post('/', App\Controller\DevToolLang::class . '@lang');

    /** @see App\Controller\DevToolLang::merge() */
    Router::get('/', App\Controller\DevToolLang::class . '@merge');

});

