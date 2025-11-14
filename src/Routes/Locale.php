<?php

use Cavesman\Router;

Router::mount('/api/v1', function (): void {
    Router::mount('/locale', function (): void {
        /** @see App\Controller\Locale::list() */
        Router::get('/', App\Controller\Locale::class . '@list');
    });
});
