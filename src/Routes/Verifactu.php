<?php

use App\Controller\Verifactu;
use Cavesman\Router;

Router::mount('/api/v1/verifactu', function () {

    /** @see Verifactu::health() */
    Router::get('/health', Verifactu::class . '@health');

    /** @see Verifactu::declaracion() */
    Router::get('/declaracion', Verifactu::class . '@declaracion');

    /** @see Verifactu::add() */
    Router::post('/(\d+)', Verifactu::class . '@add');

    /** @see Verifactu::addBulk() */
    Router::post('/bulk/([\d,]+)', function ($ids) {
        $idArray = array_map('intval', explode(',', $ids));
        return Verifactu::addBulk($idArray);
    });

    /** @see Verifactu::cancel() */
    Router::post('/cancel/(\d+)', Verifactu::class . '@cancel');

    /** @see Verifactu::status() */
    Router::get('/status/(\d+)', Verifactu::class . '@status');

    /** @see Verifactu::statusByUuid() */
    Router::get('/status/uuid/([a-f0-9\-]+)', Verifactu::class . '@statusByUuid');

    /** @see Verifactu::downloadXml() */
    Router::get('/downloadXml/(\d+)', Verifactu::class . '@downloadXml');

    /** @see Verifactu::list() */
    Router::get('/', Verifactu::class . '@list');

    /** @see Verifactu::exportXmls() */
    Router::get('/exportXmls', Verifactu::class . '@exportXmls');

});
