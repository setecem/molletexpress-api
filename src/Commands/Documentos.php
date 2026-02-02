<?php

use App\Controller\Albaran;
use App\Controller\Factura;
use Cavesman\Console;

/** @see Albaran::checkAllStatus */
Console::command('check:status:albaranes', Albaran::class . '@checkAllStatus');

/** @see Factura::checkAllStatus */
Console::command('check:status:facturas', Factura::class . '@checkAllStatus');