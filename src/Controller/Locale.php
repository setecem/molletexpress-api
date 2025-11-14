<?php

namespace App\Controller;

use Cavesman\Config;
use Cavesman\Http\JsonResponse;

class Locale
{
    /**
     * List of all available locales
     * @return JsonResponse
     */
    public static function list(): JsonResponse
    {
        return new JsonResponse(Config::get('locale.languages', []));
    }
}
