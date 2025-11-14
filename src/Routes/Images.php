<?php


use App\Controller\Images;
use Cavesman\Router;

Router::mount('/api/v1/images', function () {

    /** @see Images::list() */
    Router::get('/', Images::class . '@list');

    /** @see Images::listBg() */
    Router::get('/bg', Images::class . '@listBg');

    /** @see Images::listLogo() */
    Router::get('/logo', Images::class . '@listLogo');

    /** @see Images::listIcon() */
    Router::get('/icon', Images::class . '@listIcon');


});