<?php

namespace App\Service;

use App\Entity\Document\Factura\Factura;
use App\Model\Verifactu\FacturaVerifactu;
use App\Model\Verifactu\FacturaVerifactuRegistro;
use App\Model\Verifactu\VerifactuEstado;
use Cavesman\Config;
use RuntimeException;

class Verifacti
{
    // =========================
    // Core HTTP
    // =========================
    private static function request(
        string            $method,
        string            $path,
        array|object|null $body = null,
        array             $query = []
    ): array
    {
        $url = rtrim(Config::get('verifacti.endpoint'), '/') . $path;

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $headers = [
            "Authorization: Bearer " . Config::get('verifacti.api_key'),
            "Accept: application/json",
            "Content-Type: application/json",
        ];

        $curl = curl_init($url);

        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
        ];

        if ($body !== null) {
            $opts[CURLOPT_POSTFIELDS] = json_encode($body, JSON_UNESCAPED_UNICODE);
        }

        curl_setopt_array($curl, $opts);

        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($response === false) {
            $err = curl_error($curl);
            curl_close($curl);
            throw new RuntimeException("cURL error: {$err}");
        }

        curl_close($curl);

        return [
            'status' => $status,
            'body' => json_decode($response, true),
        ];
    }

    // =========================
    // API METHODS TIPADOS
    // =========================

    public static function health(): array
    {
        return self::request('GET', '/verifactu/health');
    }

    public static function createFactura(FacturaVerifactu $factura): array
    {
        return self::request('POST', '/verifactu/create', $factura);
    }

    /**
     * @param Factura[] $facturas
     * @return array
     */
    public static function createFacturasBulk(array $facturas): array
    {
        // array<FacturaVerifactu>
        return self::request('POST', '/verifactu/create_bulk', $facturas);
    }

    public static function modifyFactura(FacturaVerifactu $factura): array
    {
        return self::request('PUT', '/verifactu/modify', $factura);
    }

    public static function cancelFactura(FacturaVerifactuRegistro $registro): array
    {
        return self::request('POST', '/verifactu/cancel', $registro);
    }

    public static function statusFactura(FacturaVerifactuRegistro $registro): array
    {
        return self::request('POST', '/verifactu/status', $registro);
    }

    public static function statusRegistro(string $uuid): array
    {
        return self::request(
            'GET',
            '/verifactu/status',
            null,
            ['uuid' => $uuid]
        );
    }

    public static function listFacturas(VerifactuEstado $filtro): array
    {
        return self::request('POST', '/verifactu/list', $filtro);
    }

    public static function downloadXML(FacturaVerifactuRegistro $registro): array
    {
        return self::request('POST', '/verifactu/downloadXML', $registro);
    }

    public static function exportXMLs(VerifactuEstado $filtro): array
    {
        return self::request('POST', '/verifactu/export', $filtro);
    }

    public static function declaracion(): array
    {
        return self::request('GET', '/verifactu/declaracion');
    }
}
