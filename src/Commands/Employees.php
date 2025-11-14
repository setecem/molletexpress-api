<?php
use App\Controller\Employee;
use Cavesman\Console;

/** @see Employee::migrateUsers */
Console::command('migrate:users:employees', Employee::class . '@migrateUsers');

/** @see Employee::updatePassword */
Console::command('update:employee:password', Employee::class . '@updatePassword');