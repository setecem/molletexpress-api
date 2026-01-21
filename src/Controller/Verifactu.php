<?php

namespace App\Controller;

use App\Entity\Document\Factura\Factura;
use Cavesman\Exception\ModuleException;
use Cavesman\Http\JsonResponse;

class Verifactu
{
    /**
     * Enviar factura por ID
     */
    public static function add(int $id): JsonResponse
    {
        $factura = Factura::findOneBy(['id' => $id]);

        if (!$factura) {
            return new JsonResponse(['error' => 'Factura no encontrada'], 404);
        }

        $respuesta = Verifacti::crearFactura($factura);

        return new JsonResponse([
            'message' => 'Factura enviada a Verifactu',
            'data' => $respuesta
        ]);
    }

    /**
     * Enviar varias facturas por IDs
     * @param int[] $ids
     * @throws ModuleException
     */
    public static function addBulk(array $ids): JsonResponse
    {
        $facturas = Factura::findBy(['id' => $ids]);

        if (!$facturas) {
            return new JsonResponse(['error' => 'Facturas no encontradas'], 404);
        }

        $respuesta = Verifacti::crearFacturasBulk($facturas);

        return new JsonResponse([
            'message' => 'Facturas enviadas a Verifactu',
            'data' => $respuesta
        ]);
    }

    /**
     * Cancelar factura por ID
     * @throws ModuleException
     */
    public static function cancel(int $id): JsonResponse
    {
        $factura = Factura::findOneBy(['id' => $id]);

        if (!$factura) {
            return new JsonResponse(['error' => 'Factura no encontrada'], 404);
        }

        $respuesta = Verifacti::cancelarFactura($factura);

        return new JsonResponse([
            'message' => 'Factura cancelada en Verifactu',
            'data' => $respuesta
        ]);
    }

    /**
     * Consultar estado por ID
     * @throws ModuleException
     */
    public static function status(int $id): JsonResponse
    {
        $factura = Factura::findOneBy(['id' => $id]);

        if (!$factura) {
            return new JsonResponse(['error' => 'Factura no encontrada'], 404);
        }

        $respuesta = Verifacti::estadoFactura($factura);

        return new JsonResponse([
            'message' => 'Estado de la factura',
            'data' => $respuesta
        ]);
    }

    /**
     * Consultar estado por UUID
     */
    public static function statusByUuid(string $uuid): JsonResponse
    {
        $respuesta = Verifacti::estadoPorUuid($uuid);

        return new JsonResponse([
            'message' => 'Estado de la factura por UUID',
            'data' => $respuesta
        ]);
    }

    /**
     * Descargar XML por ID
     * @throws ModuleException
     */
    public static function downloadXml(int $id): JsonResponse
    {
        $factura = Factura::findOneBy(['id' => $id]);

        if (!$factura || !$factura->verifactu) {
            return new JsonResponse(['error' => 'Factura o UUID no encontrado'], 404);
        }

        $respuesta = Verifacti::descargarXML($factura->verifactu);

        return new JsonResponse([
            'message' => 'XML descargado',
            'data' => $respuesta
        ]);
    }

    /**
     * Listar facturas filtradas
     */
    public static function list(): JsonResponse
    {
        $respuesta = Verifacti::listarFacturas();

        return new JsonResponse([
            'message' => 'Listado de facturas',
            'data' => $respuesta
        ]);
    }

    /**
     * Exportar XMLs
     */
    public static function exportXmls(): JsonResponse
    {
        $respuesta = Verifacti::exportarXMLs();

        return new JsonResponse([
            'message' => 'Exportación de XMLs',
            'data' => $respuesta
        ]);
    }

    /**
     * Health check de Verifactu
     */
    public static function health(): JsonResponse
    {
        $respuesta = Verifacti::health();

        return new JsonResponse([
            'message' => 'Estado de conexión con Verifactu',
            'data' => $respuesta
        ]);
    }

    /**
     * Declaración
     */
    public static function declaracion(): JsonResponse
    {
        $respuesta = Verifacti::declaracion();

        return new JsonResponse([
            'message' => 'Declaración obtenida',
            'data' => $respuesta
        ]);
    }
}
