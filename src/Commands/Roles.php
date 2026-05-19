<?php
use App\Controller\Role;
use Cavesman\Console;

/** @see Role::syncRoles */
Console::command('sync:roles', Role::class . '@syncRoles');