<?php

use App\Controller\Contact;
use Cavesman\Router;

Router::mount('/api/v1/contact', function () {

    // Obtener todos los origenes
    /** @see Contact::listOrigen() */
    Router::get('/origen', Contact::class . '@listOrigen');

    /** @see Contact::filterAll() */
    Router::get('/all/filter', Contact::class . '@filterAll');

    /** @see Contact::filter() */
    Router::get('/(\d+)/filter', Contact::class . '@filter');

    /** @see Contact::get() */
    Router::get('/(\d+)', Contact::class . '@get');

    /** @see Contact::active() */
    Router::get('/(\d+)/active', Contact::class . '@active');

    /** @see Contact::add() */
    Router::post('/', Contact::class . '@add');

    /** @see Contact::update() */
    Router::put('/(\d+)', Contact::class . '@update');

    /** @see Contact::delete() */
    Router::delete('/(\d+)', Contact::class . '@delete');


    //  ContactFeed

    /** @see Contact::filterAllFeed() */
    Router::get('/all/(\d+)/filter', Contact::class . '@filterAllFeed');

    /** @see Contact::filterPublicFeed() */
    Router::get('/public/(\d+)/filter', Contact::class . '@filterPublicFeed');

    /** @see Contact::getFeed() */
    Router::get('/feed/(\d+)', Contact::class . '@getFeed');

    /** @see Contact::addFeed() */
    Router::post('/feed', Contact::class . '@addFeed');

    /** @see Contact::updateFeed() */
    Router::put('/feed/(\d+)', Contact::class . '@updateFeed');

    /** @see Contact::deleteFeed() */
    Router::delete('/feed/(\d+)', Contact::class . '@deleteFeed');

    // ContactFeedAlert

    /** @see Contact::addAlert() */
    Router::post('/alert', Contact::class . '@addAlert');

    /** @see Contact::updateAlert() */
    Router::put('/alert/(\d+)', Contact::class . '@updateAlert');

    /** @see Contact::getAlert() */
    Router::get('/alert/(\d+)', Contact::class . '@getAlert');
});
