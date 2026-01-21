<?php

namespace App\Controller;

use App\Model\Verifactu\{FacturaVerifactu, FacturaVerifactuRegistro, Linea, VerifactuEstado};


class Verifacti
{
    // =========================
    // 1. Health check
    // =========================
    public static function health()
    {
        return \App\Service\Verifacti::health();
    }

    // =========================
    // 2. Crear factura
    // =========================
    public static function crearFactura(\App\Entity\Document\Factura\Factura $factura)
    {
        $item = self::factura($factura);

        return \App\Service\Verifacti::createFactura($item);
    }

    // =========================
    // 3. Crear facturas en bulk
    // =========================

    /**
     * @param \App\Entity\Document\Factura\Factura[] $facturas
     * @return array
     */
    public static function crearFacturasBulk(array $facturas)
    {
        $items = array_map(fn (\App\Entity\Document\Factura\Factura $factura)  => self::factura($factura), $facturas);
        return \App\Service\Verifacti::createFacturasBulk($items);
    }

    // =========================
    // 4. Modificar factura
    // =========================
    public static function modificarFactura(\App\Entity\Document\Factura\Factura $factura)
    {
        $item = self::factura($factura);

        return \App\Service\Verifacti::modifyFactura($item);
    }

    // =========================
    // 5. Cancelar factura
    // =========================
    public static function cancelarFactura(\App\Entity\Document\Factura\Factura $factura)
    {
        $registro = new FacturaVerifactuRegistro();
        $registro->uuid = $factura->verifactu;
        $registro->nif_emisor = 'B12345678';
        $registro->num_serie = $factura->serie;
        $registro->fecha_expedicion = $factura->date->format('Y-m-d');

        return \App\Service\Verifacti::cancelFactura($registro);
    }

    // =========================
    // 6. Consultar estado (por datos)
    // =========================
    public static function estadoFactura(\App\Entity\Document\Factura\Factura $factura)
    {
        $registro = new FacturaVerifactuRegistro();
        $registro->nif_emisor = 'B12345678';
        $registro->num_serie = $factura->serie;
        $registro->fecha_expedicion = $factura->date->format('Y-m-d');

        return \App\Service\Verifacti::statusFactura($registro);
    }

    // =========================
    // 7. Consultar estado (por UUID)
    // =========================
    public static function estadoPorUuid(string $uuid)
    {
        return \App\Service\Verifacti::statusRegistro(
            $uuid
        );
    }

    // =========================
    // 8. Listar facturas
    // =========================
    public static function listarFacturas()
    {
        $filtro = new VerifactuEstado();
        $filtro->estado = 'ACEPTADO';
        $filtro->nif = 'B12345678';

        return \App\Service\Verifacti::listFacturas($filtro);
    }

    // =========================
    // 9. Descargar XML
    // =========================
    public static function descargarXML(string $uuid)
    {
        $registro = new FacturaVerifactuRegistro();
        $registro->uuid = $uuid;
        $registro->nif_emisor = 'B12345678';

        return \App\Service\Verifacti::downloadXML($registro);
    }

    // =========================
    // 10. Exportar XMLs
    // =========================
    public static function exportarXMLs()
    {
        $filtro = new VerifactuEstado();
        $filtro->nif = 'B12345678';

        return \App\Service\Verifacti::exportXMLs($filtro);
    }

    // =========================
    // 11. Declaración
    // =========================
    public static function declaracion()
    {
        return \App\Service\Verifacti::declaracion();
    }


    private static function factura(\App\Entity\Document\Factura\Factura $factura): FacturaVerifactu {
        $item = new FacturaVerifactu();
        $item->serie = $factura->serie;
        $item->numero = $factura->number;
        $item->fecha_expedicion = $factura->date->format('Y-m-d');
        $item->tipo_factura = 'F1';
        $item->nif = $factura->client->nif;
        $item->nombre = $factura->client->name;
        $item->importe_total = $factura->total;

        foreach ($factura->lineas as $line) {
            $linea = new Linea();
            $linea->base_imponible = $line->total;
            $linea->tipo_impositivo = $line->tax;
            $linea->cuota_repercutida = $line->total / ($line->tax / 100);
            $item->lineas[] = $linea;
        }

        return $item;
    }
}
