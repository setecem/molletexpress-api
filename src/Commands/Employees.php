<?php
use App\Controller\Employee;
use App\Controller\User;
use Cavesman\Console;

/** @see Employee::migrateUsers */
Console::command('migrate:users:employees', Employee::class . '@migrateUsers');

/** @see User::updatePassword */
Console::command('update:user:password', User::class . '@updatePassword');