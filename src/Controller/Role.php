<?php

namespace App\Controller;

use Cavesman\Http\JsonResponse;
use Exception;

class Role
{
    /**
     * List of all available locales
     *
     * @return JsonResponse
     */
    public static function list(): JsonResponse
    {
        try {
            return new JsonResponse(\App\Entity\Role::findBy(['active' => true]));
        } catch (Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }

    }
}
