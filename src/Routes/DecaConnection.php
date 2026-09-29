<?php

use App\Controller\DecaConnection;
use Cavesman\Router;

/**
 * Configuración → DeCA. Cada acción comprueba token y permiso DECA (ACCESS para ver, EDIT para cambiar).
 */
Router::mount('/api/v1/deca-connection', function () {

    /** @see DecaConnection::status() — para appDeCA, cualquier usuario identificado */
    Router::get('/status', DecaConnection::class . '@status');

    /** @see DecaConnection::get() */
    Router::get('/', DecaConnection::class . '@get');

    /** @see DecaConnection::save() */
    Router::post('/', DecaConnection::class . '@save');

    /** @see DecaConnection::test() */
    Router::post('/test', DecaConnection::class . '@test');

    /** @see DecaConnection::partners() */
    Router::get('/partners', DecaConnection::class . '@partners');

    /** @see DecaConnection::ownPartner() */
    Router::put('/own-partner', DecaConnection::class . '@ownPartner');

    /** @see DecaConnection::clients() */
    Router::get('/clients', DecaConnection::class . '@clients');

    /** @see DecaConnection::syncClients() */
    Router::post('/clients', DecaConnection::class . '@syncClients');

    /** @see DecaConnection::delete() */
    Router::delete('/', DecaConnection::class . '@delete');
});
