<?php

use App\Controller\Auth;
use Cavesman\Http\JsonResponse;
use Cavesman\Router;

Router::set404(fn() => new JsonResponse(['Route not found'], 404));

Router::get('/', fn() => new Cavesman\Http\JsonResponse(['message' => 'Welcome to Cavesman Framework!']));
Router::get('/info', function() {
    phpinfo();
    exit();
});

Router::mount('/api/v1', function (): void {

    Router::get("/", fn() => new JsonResponse(['message' => 'Welcome API Version: 2']));

    /** @see App\Controller\Auth::check() */
    Router::get('/check', Auth::class . '@check');

    /** @see App\Controller\Auth::auth() */
    Router::post('/auth', Auth::class . '@auth');

});
