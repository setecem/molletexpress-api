<?php
namespace App\Controller;

use Cavesman\Http;
use Exception;

class OrdainCharge
{

    public static function list(): Http\JsonResponse
    {
        try {

            $list = \App\Entity\OrdainCharge::findBy(['deletedOn' => null]);

            return new Http\JsonResponse(array_map(fn(\App\Entity\OrdainCharge $oc) => $oc->model(\App\Model\OrdainCharge::class)->json(), $list));
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function get(int $id): Http\JsonResponse
    {
        try {

            $item = \App\Entity\OrdainCharge::findOneBy(['id' => $id, 'deletedOn' => null]);

            return new Http\JsonResponse($item->model(\App\Model\OrdainCharge::class)->json());
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

}