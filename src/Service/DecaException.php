<?php

namespace App\Service;

use Exception;

/** Error al llamar a la API de DeCA, con el código HTTP que devolvió (0 si no hubo conexión). */
class DecaException extends Exception
{
    public function __construct(string $message, public readonly int $status = 0, public readonly mixed $data = null)
    {
        parent::__construct($message, $status);
    }
}
