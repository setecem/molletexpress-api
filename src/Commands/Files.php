<?php

use App\Controller\File;
use Cavesman\Console;

/** @see File::migrateContacts() */
Console::command('migrate:files:contacts', File::class . '@migrateContacts');