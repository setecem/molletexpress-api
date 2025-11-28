<?php

namespace App\Controller;

use App\Enum\Bancos;
use Cavesman\Http;
use Exception;

class Banco

{
    public static function list(): Http\JsonResponse
    {
        try {

            $list = [];
            foreach (Bancos::cases() as $case) {
                $list[$case->name] = $case->value;
            }
            return new Http\JsonResponse($list);
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function getSwift(string $name): Http\JsonResponse
    {
        try {
            if (Bancos::tryFrom($name) == null)
                return new Http\JsonResponse(['message' => "Swift not found"], 404);

            $swift = explode("_", Bancos::tryFrom($name)->name)[0];
            return new Http\JsonResponse($swift);

        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }
}