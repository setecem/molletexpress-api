<?php

namespace App\Controller;

use Cavesman\Http\JsonResponse;
use Exception;

class Images
{
    /**
     * List of all available locales
     *
     * @return JsonResponse
     */
    public static function list(): JsonResponse
    {
        try {
            return new JsonResponse(\App\Enum\Images::list());
        } catch (Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }

    }

    public static function listBg(): JsonResponse
    {
        try {
            return new JsonResponse(\App\Enum\Images::listBg());
        } catch (Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }

    }

    public static function listLogo(): JsonResponse
    {
        try {
            return new JsonResponse(\App\Enum\Images::listLogo());
        } catch (Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }

    }

    public static function listIcon(): JsonResponse
    {
        try {
            return new JsonResponse(\App\Enum\Images::listIcon());
        } catch (Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }

    }
}
