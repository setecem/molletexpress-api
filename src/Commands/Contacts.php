<?php

use App\Controller\Contact;
use Cavesman\Console;

/** @see Contact::sendTodayAlerts() */
Console::command('send:contact:alerts', Contact::class . '@sendTodayAlerts');