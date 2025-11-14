<?php

use App\Controller\Role;
use App\Controller\Auth;
use Cavesman\Router;

Router::mount('/api/v1/role', function (): void {

    /** @see App\Controller\Role::list()
     * @see App\Controller\Auth::middleware()
     */
    Router::get('/', Role::class . '@list', Auth::class . '@middleware');
});
