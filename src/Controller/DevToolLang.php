<?php

namespace App\Controller;

use Cavesman\Http;
use Cavesman\Request;
use Cavesman\Translate;

final class DevToolLang
{

    /**
     * Receive all translations
     *
     * @return Http\JsonResponse
     */
    public static function lang(): Http\JsonResponse
    {
        $languages = json_decode(Request::post('list', '[]'), true);

        foreach ($languages as $item)
            Translate::check($item);

        return new Http\JsonResponse(Translate::list());
    }


    /**
     * Create locales by message.json
     *
     * @return Http\JsonResponse
     */
    public static function merge(): Http\JsonResponse
    {
        Translate::merge();

        return new Http\JsonResponse(Translate::list());
    }
}
