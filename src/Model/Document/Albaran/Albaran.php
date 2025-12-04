<?php

namespace App\Model\Document\Albaran;

use App\Model\Client;
use App\Model\Document\Documento;
use App\Model\Document\Factura\Factura;
use Cavesman\Db;
use Cavesman\Db\Doctrine\Entity\Base;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use Cavesman\Http;

class Albaran extends Documento
{

    const string|Base ENTITY = \App\Entity\Document\Albaran\Albaran::class;

    public ?Factura $factura = null;

    public function typeOfCollection(string $property): ?string
    {
        return match ($property) {
            'client' => Client::class,
            'lineas' => AlbaranLinea::class,
            'factura' => Factura::class,
            default => null
        };
    }

    public function factura(): Http\JsonResponse
    {
        try {

            $item = \App\Entity\Document\Albaran\Albaran::findOneBy(['id' => $this->id, 'deletedOn' => null]);

            /** @var Albaran $albaran */
            $albaran = $item->model(Albaran::class);
            $albaran->id = null;

            foreach ($albaran->lineas as $linea) {
                $linea->id = null;
            }

            $array = json_decode(json_encode($albaran->json()), true);

            $factura = new Factura($array);

            /** @var \App\Entity\Document\Factura\Factura $facturaEntity */
            $facturaEntity = $factura->entity();

            foreach ($facturaEntity->lineas as $linea) {
                $linea->factura = $facturaEntity;
            }

            $em = DB::getManager();
            $em->persist($facturaEntity);
            $em->flush();

            $em = DB::getManager();
            $item->factura = $facturaEntity;
            $em->persist($item);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Factura generada correctamente",
                'item' => $item->model(Albaran::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }
}