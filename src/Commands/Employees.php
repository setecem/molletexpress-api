<?php
use App\Controller\Employee;
use Cavesman\Console;

/** @see Employee::migrateUsers */
Console::command('migrate:users:employee', Employee::class . '@migrateUsers');

/** @see Employee::updatePassword */
Console::command('update:user:password', Employee::class . '@updatePassword');