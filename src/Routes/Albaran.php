<?php

use App\Controller\Albaran;
use Cavesman\Router;

Router::mount('/api/v1/delivery-note', function () {

    /** @see Albaran::filterAll() */
    Router::get('/all/filter', Albaran::class . '@filterAll');

    /** @see Albaran::filter() */
    Router::get('/(\d+)/filter', Albaran::class . '@filter');

    /** @see Albaran::export() */
    Router::get('/(\d{4}-\d{2}-\d{2})/(\d{4}-\d{2}-\d{2})/(\d+)/export', Albaran::class . '@export');

    /** @see Albaran::list() */
    Router::get('/(\d{4}-\d{2}-\d{2})/(\d{4}-\d{2}-\d{2})/(\d+)', Albaran::class . '@list');

    /** @see Albaran::facturar() */
    Router::get('/(\d{4}-\d{2}-\d{2})/(\d{4}-\d{2}-\d{2})/(\d+)/(\d{4}-\d{2}-\d{2})', Albaran::class . '@facturar');

    /** @see Albaran::sendEmail() */
    Router::get('/(\d+)/email', Albaran::class . '@sendEmail');

    /** @see Albaran::get() */
    Router::get('/(\d+)', Albaran::class . '@get');

    /** @see Albaran::print() */
    Router::get('/(\d+)/print', Albaran::class . '@print');

    /** @see Albaran::factura() */
    Router::get('/(\d+)/factura', Albaran::class . '@factura');

    /** @see Albaran::active() */
    Router::get('/(\d+)/active', Albaran::class . '@active');

    /** @see Albaran::add() */
    Router::post('/', Albaran::class . '@add');

    /** @see Albaran::update() */
    Router::put('/(\d+)', Albaran::class . '@update');

    /** @see Albaran::delete() */
    Router::delete('/(\d+)', Albaran::class . '@delete');

});
