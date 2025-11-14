<?php
namespace App\Controller;

use Cavesman\Http;
use Exception;

class Service
{

    public static function list(): Http\JsonResponse
    {
        try {

            $list = \App\Entity\Service::findBy(['deletedOn' => null]);

            return new Http\JsonResponse(array_map(fn(\App\Entity\Service $service) => $service->model(\App\Model\Service::class)->json(), $list));
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function get(int $id): Http\JsonResponse
    {
        try {

            $item = \App\Entity\Service::findOneBy(['id' => $id, 'deletedOn' => null]);

            return new Http\JsonResponse($item->model(\App\Model\Service::class)->json());
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

}