<?php

namespace App\Controller;

use App\Entity\Document\Factura\FacturaLinea;
use App\Enum\DocumentStatus;
use App\Model\DataTable;
use App\Model\Pdf\FacturaPdf;
use App\Model\Pdf\ListadoPdf;
use Cavesman\Config;
use Cavesman\Db;
use Cavesman\Enum\Directory;
use Cavesman\FileSystem;
use Cavesman\Http;
use Cavesman\Mail;
use Cavesman\Request;
use DateInterval;
use DateTime;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use ZipArchive;

class Factura
{
    public static function filterAll(): Http\JsonResponse
    {
        try {
            $em = Db::getManager();

            $qb = $em->getRepository(\App\Entity\Document\Factura\Factura::class)
                ->createQueryBuilder('i')
                ->where('i.deletedOn IS NULL');

            $filter = json_decode(Request::get('filter', '[]'));

            if ($filter && $filter->search) {
                foreach (explode(' ', $filter->search->value) as $key => $string) {
                    $qb
                        ->andWhere('i.serie LIKE :search_' . $key . ' OR i.number LIKE :search_' . $key
                            . ' OR i.observaciones LIKE :search_' . $key . ' OR i.tax LIKE :search_' . $key
                            . ' OR i.comments LIKE :search_' . $key
                        )
                        ->setParameter('search_' . $key, '%' . $string . '%');
                }
            }

            if ($filter->order && $filter->columns) {
                foreach ($filter->order as $order) {
                    $index = $order->column;
                    $columnName = $filter->columns[$index]->data;
                    $dir = strtoupper($order->dir);
                    if ($dir === 'ASC' || $dir === 'DESC')
                        $qb->addOrderBy('i.' . $columnName, $dir);
                }
            }

            $total = clone $qb;

            if ($filter->length ?? false) {
                $qb->setMaxResults($filter->length)
                    ->setFirstResult($filter->start);
            }

            /** @var \App\Entity\Document\Factura\Factura[] $list */
            $list = $qb->getQuery()->getResult();

            $datatable = new DataTable();
            $datatable->recordsTotal = count($total->getQuery()->getResult());
            foreach ($list as $item) {
                /** @var \App\Model\Document\Factura\Factura $model */
                $model = $item->model(\App\Model\Document\Factura\Factura::class);
                $datatable->data[] = $model->json();
            }
            $datatable->recordsFiltered = count($total->getQuery()->getResult());

            return new  Http\JsonResponse($datatable);
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }

    }

    public static function filter(int $id): Http\JsonResponse
    {
        try {
            $em = Db::getManager();

            $qb = $em->getRepository(\App\Entity\Document\Factura\Factura::class)
                ->createQueryBuilder('i')
                ->where('i.deletedOn IS NULL')
                ->andWhere('c.id = :id')
                ->setParameter('id', $id);


            $filter = json_decode(Request::get('filter', '[]'));

            if ($filter && $filter->search) {
                foreach (explode(' ', $filter->search->value) as $key => $string) {
                    $qb
                        ->andWhere('i.serie LIKE :search_' . $key . ' OR i.number LIKE :search_' . $key
                            . ' OR i.observaciones LIKE :search_' . $key . ' OR i.tax LIKE :search_' . $key
                            . ' OR i.comments LIKE :search_' . $key
                        )
                        ->setParameter('search_' . $key, '%' . $string . '%');
                }
            }

            if ($filter->order && $filter->columns) {
                foreach ($filter->order as $order) {
                    $index = $order->column;
                    $columnName = $filter->columns[$index]->data;
                    $dir = strtoupper($order->dir);
                    if ($dir === 'ASC' || $dir === 'DESC')
                        $qb->addOrderBy('i.' . $columnName, $dir);
                }
            }

            $total = clone $qb;

            if ($filter->length ?? false) {
                $qb->setMaxResults($filter->length)
                    ->setFirstResult($filter->start);
            }

            /** @var \App\Entity\Document\Factura\Factura[] $list */
            $list = $qb->getQuery()->getResult();

            $datatable = new DataTable();
            $datatable->recordsTotal = count($total->getQuery()->getResult());
            foreach ($list as $item) {
                /** @var \App\Model\Document\Factura\Factura $model */
                $model = $item->model(\App\Model\Document\Factura\Factura::class);
                $datatable->data[] = $model->json();
            }
            $datatable->recordsFiltered = count($total->getQuery()->getResult());

            return new  Http\JsonResponse($datatable);
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }

    }

    public static function get(int $id): Http\JsonResponse
    {
        try {

            $item = \App\Entity\Document\Factura\Factura::findOneBy(['id' => $id, 'deletedOn' => null]);

            return new Http\JsonResponse($item->model(\App\Model\Document\Factura\Factura::class)->json());
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function add(): Http\JsonResponse
    {
        try {

            $model = \App\Model\Document\Factura\Factura::fromRequest();

            /** @var \App\Entity\Document\Factura\Factura $entity */
            $entity = $model->entity();

            $em = DB::getManager();

            foreach ($entity->lineas as $linea) {
                $linea->factura = $entity;
            }

            $em->persist($entity);
            $em->flush();
            $em->persist($entity);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Factura añadida correctamente",
                'item' => $entity->model(\App\Model\Document\Factura\Factura::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function update(int $id): Http\JsonResponse
    {

        try {
            $item = \App\Entity\Document\Factura\Factura::findOneBy(['id' => $id, 'deletedOn' => null]);

            if (!$item)
                return new Http\JsonResponse(['message' => "Factura no encontrada"], 404);

            if ($item->status !== DocumentStatus::DRAFT)
                return new Http\JsonResponse(['message' => "No es posible modificar esta factura ya que ya ha sido enviada"], 404);

            $model = \App\Model\Document\Factura\Factura::fromRequest();

            if ($id != $model->id)
                return new Http\JsonResponse(['message' => "La id indicada en la url no corresponde a la enviada en el modelo"], 404);

            $em = DB::getManager();

            $idLineas = array_map(fn($linea) => $linea->id, $model->lineas);

            foreach ($item->lineas as $linea) {
                if (!in_array($linea->id, $idLineas)) {
                    $linea->deletedOn = new DateTime();
                    $em->persist($linea);
                }
            }

            /** @var \App\Entity\Document\Factura\Factura $entity */
            $entity = $model->entity();

            foreach ($entity->lineas as $linea) {
                $linea->factura = $entity;
            }

            $em->persist($entity);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Factura actualizada correctamente",
                'item' => $entity->model(\App\Model\Document\Factura\Factura::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function delete(int $id): Http\JsonResponse
    {
        try {

            $em = DB::getManager();

            $item = \App\Entity\Document\Factura\Factura::findOneBy(['id' => $id, 'deletedOn' => null]);

            $item->deletedOn = new DateTime();

            $em->persist($item);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Factura eliminada correctamente",
                'item' => $item->model(\App\Model\Document\Factura\Factura::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    protected static function list(string $dateStart, string $dateEnd, string $client): Http\JsonResponse
    {
        try {
            $em = DB::getManager();
            if (file_exists('src\Model\Pdf\ListadoPdf.php'))
                require 'src\Model\Pdf\ListadoPdf.php';
            else
                require 'src\Model\Pdf\DefaultPdf.php';
            
            $resultItems = $em
                ->createQueryBuilder()
                ->select('o')
                ->from(\App\Entity\Document\Factura\Factura::class, "o")
                ->innerJoin('o.client', 'c')
                ->where('o.date BETWEEN :dateStart AND :dateEnd')
                ->orderBy("c.num_abonado", "ASC")
                ->setParameter('dateStart', new DateTime($dateStart))
                ->setParameter('dateEnd', new DateTime($dateEnd));
            if ($client !== "false") {
                $clientEnt = $em->getReference(\App\Entity\Client::class, $client);
                $resultItems
                    ->andWhere("o.client = :client")
                    ->setParameter('client', $clientEnt);
            }
            $result = $resultItems
                ->getQuery()
                ->getResult();

            $data = [];
            $subtotal = 0;
            $iva = 0;
            $total = 0;
            foreach ($result as $item) {
                $data[] = [
                    $item->number,
                    $item->date->format('d/m/Y'),
                    str_pad($item->client->numAbonado, 4, "0", STR_PAD_LEFT),
                    mb_convert_encoding(substr($item->client->name, 0, 50), 'ISO-8859-1', 'UTF-8'),
                    $item->client->nif,
                    $item->subtotal,
                    ($item->total - $item->subtotal),
                    $item->total
                ];
                $subtotal += $item->subtotal;
                $iva += ($item->total - $item->subtotal);
                $total += $item->total;
            }
            $data[] = [
                '',
                '',
                '',
                '',
                'Suma:',
                $subtotal,
                $iva,
                $total
            ];

            require_once 'src\Model\Pdf\ListadoPdf.php';

            $pdf = new ListadoPdf();
            $pdf->AliasNbPages();
            // Column headings
            $header = ['Numero', 'Fecha', 'Código', 'Nombre', 'NIF', 'Bruto', 'IVA', 'TOTAL'];
            $header = array_map(function ($item) {
                return mb_convert_encoding($item, 'ISO-8859-1', 'UTF-8');
            }, $header);

            $pdf->AddPage();
            $pdf->resetFont();
            $pdf->title('Listado de ' . \App\Entity\Document\Factura\Factura::class);
            $pdf->ImprovedTable($header, $data);
            $pdf->Output();
            die();
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    protected static function export(string $dateStart, string $dateEnd, string $client)
    {
        $cacheDirectory = FileSystem::getPath(Directory::APP) . '/cache';
        try {
            $em = DB::getManager();
            if (file_exists('src\Model\Pdf\FacturaPdf.php'))
                require 'src\Model\Pdf\FacturaPdf.php';
            else
                require 'src\Model\Pdf\DefaultPdf.php';

            $resultItems = $em
                ->createQueryBuilder()
                ->select('o')
                ->from(\App\Entity\Document\Factura\Factura::class, "o")
                ->where('o.date BETWEEN :dateStart AND :dateEnd')
                ->orderBy("o.reference", "DESC")
                ->setParameter('dateStart', new DateTime($dateStart))
                ->setParameter('dateEnd', new DateTime($dateEnd));
            if ($client !== "false") {
                $clientEnt = $em->getReference(\App\Entity\Client::class, $client);
                $resultItems
                    ->andWhere("o.client = :client")
                    ->setParameter('client', $clientEnt);
            }
            $results = $resultItems
                ->getQuery()
                ->getResult();


            $clients = [];
            foreach ($results as $item) {
                if ($item->client) {
                    if (!isset($clients[$item->client->id]))
                        $clients[$item->client->id] = [
                            "client" => $item->client,
                            "items" => []
                        ];
                    $clients[$item->client->id]["items"][] = $item;
                }
            }
            if (!$clients)
                return new Http\JsonResponse(['message' => "Ningún documento encontrado"], 404);

            $zip_file = \App\Entity\Document\Factura\Factura::class . "-" . time();
            if (!is_dir($cacheDirectory . "/pdf/" . $zip_file))
                mkdir($cacheDirectory . "/pdf/" . $zip_file, 0777, true);

            foreach ($clients as $c) {
                foreach ($c['items'] as $item) {
                    $invoice = new FacturaPdf("A4", "€", "es");
                    $invoice = self::print($item->id, true, $invoice);
                    $invoice->render($cacheDirectory . "/pdf/" . $zip_file . "/" . $item->number . ".pdf", 'F');
                }
            }

            $zip = new ZipArchive;
            if ($zip->open($cacheDirectory . "/pdf/" . $zip_file . "/facturas.zip", ZipArchive::CREATE)) {
                foreach (glob($cacheDirectory . "/pdf/" . $zip_file . "/*.pdf") as $pdf) {
                    $zip->addFile($pdf, basename($pdf));
                }
                $zip->close();
            } else
                echo 'Failed!';

            header('Content-disposition: attachment; filename=' . \App\Entity\Document\Factura\Factura::class . "-" . time() . '.zip');
            header('Content-type: application/zip');
            readfile($cacheDirectory . "/pdf/" . $zip_file . "/facturas.zip");
            die("fin");
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    protected static function print(int $id, bool $export = false, FacturaPdf|bool $invoice = false)
    {
        try {
            if (!$id)
                return new Http\JsonResponse(['message' => "ID no encontrada"], 404);

            $em = DB::getManager();
            $item = $em->getRepository(\App\Entity\Document\Factura\Factura::class)->findOneBy(['id' => $id]);
            $lineas = $em->getRepository(FacturaLinea::class)->findOneBy(['factura' => $item]);
            $client = $item->client ?? false;

            if (!$export) {
                if (file_exists('src\Model\Pdf\FacturaPdf.php'))
                    require 'src\Model\Pdf\FacturaPdf.php';
                else
                    require 'src\Model\Pdf\DefaultPdf.php';
                $invoice = new FacturaPdf("A4", "€", "es");
            }

            /* Header settings */
            $invoice->setLogo("public/img/logo/logo-mollet.jpg");   //logo image path
            $invoice->setColor("#007fff");      // pdf color scheme
            $invoice->type = "factura";    // Invoice Type
            $invoice->reference = $item->number;   // Reference
            $invoice->date = $item->date->format("d-m-Y");   //Billing Date
            $invoice->setNumberFormat(",", ".", "right");

            $invoice->from = [
                Config::get("modules.factura.empresa.nombre_fiscal"),
                Config::get("modules.factura.empresa.nombre_fiscal2"),
                Config::get("modules.factura.empresa.direccion"),
                Config::get("modules.factura.empresa.localidad"),
                Config::get("modules.factura.empresa.cp") . " " . Config::get("modules.factura.empresa.provincia"),
                Config::get("modules.factura.empresa.nif")
            ];

            // Sé que es una guarrada Pedro, pero añadimos 2 líneas en blanco para poder ocultar el nif del cliente de la ventanita de las cartas
            if ($client) {
                $invoice->pedido = $client->numPedido ?? '-';
                $invoice->ibanCliente = $client->iban;
                $invoice->abonado = $client->numAbonado;
                $invoice->nif = $client->nif;
                $invoice->to = [
                    $client->name,
                    $client->direccion,
                    $client->localidad,
                    $client->codigoPostal . " " . $client->provincia,
                    " ",
                    $client->nif
                ];
            }

            foreach ($lineas as $linea) {
                $albaran = $linea->albaran ? $linea->albaran->number : '';
                $date = $linea->albaran ? $linea->albaran->date->format("d/m/Y") : '';
                $invoice->addItem($linea->reference, $linea->description, $linea->quantity, false, $linea->price, $linea->discount, $linea->total, $albaran, $date);
            }

            $invoice->addTotal("Importe Bruto", $item->importeBruto);
            $invoice->addTotal("Dto. Esp " . $item->discount . "%", $item->impDiscount);
            $invoice->addTotal("Dto. P.P. " . $item->discountPp . "%", $item->impDiscountPp);
            $invoice->addTotal("Base Imponible", $item->subtotal);
            $invoice->addTotal("Tipo IVA 21%", $item->total - $item->subtotal);
            $invoice->addTotal("Total", $item->total);

            if ($client)
                $invoice->addParagraph("Forma de pago: " . $client->formaPago);

            $invoice->addParagraph("Vencimiento: " . self::getDueDate($item)->format("d-m-Y"));

            if ($client & $client->formaPago == "TRANSFERENCIA BANCARIA")
                $invoice->addParagraph("IBAN Mollet Express: " . Config::get("modules.factura.empresa.iban"));

            $invoice->footerNote = Config::get("modules.factura.empresa.registro");

            if (!$export) {
                $invoice->render('example1.pdf', 'I');
                exit();
            } else
                return $invoice;

        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    protected static function sendEmail(int $id): Http\JsonResponse
    {
        try {
            $cacheDirectory = FileSystem::getPath(Directory::APP) . '/cache';

            if (!$id)
                return new Http\JsonResponse(['message' => "ID no encontrada"], 404);

            $em = DB::getManager();
            $files = [];
            if (file_exists('src\Model\Pdf\FacturaPdf.php'))
                require 'src\Model\Pdf\FacturaPdf.php';
            else
                require 'src\Model\Pdf\DefaultPdf.php';

            $item = null;
            if (count(explode(",", $id)) == 1) {
                $item = $em->getRepository(\App\Entity\Document\Factura\Factura::class)->findOneBy(['id' => $id]);
                $invoice = new FacturaPdf("A4", "€", "es");
                $invoice = self::print($item->id, true, $invoice);
                $invoice->render($cacheDirectory . "/pdf/" . hash("sha256", $item->id) . ".pdf", 'F');
                if (!$item->client)
                    return new Http\JsonResponse(['message' => "El documento seleccionado no tiene cliente asignado"], 400);

                $files[$item->client->id][] = [
                    "name" => $item->number . ".pdf",
                    "file" => $cacheDirectory . "/pdf/" . hash("sha256", $item->id) . ".pdf"
                ];
            } else {
                foreach (explode(",", $id) as $id) {
                    $item = $em->getRepository(\App\Entity\Document\Factura\Factura::class)->findOneBy(['id' => $id]);
                    $invoice = new FacturaPdf("A4", "€", "es");
                    $invoice = self::print($item->id, true, $invoice);
                    $invoice->render($cacheDirectory . "/pdf/" . hash("sha256", $item->id) . ".pdf", 'F');
                    if ($item->client) {
                        if (!isset($files[$item->client->id]))
                            $files[$item->client->id] = [];
                        $files[$item->client->id][] = [
                            "name" => $item->number . ".pdf",
                            "file" => $cacheDirectory . "/pdf/" . hash("sha256", $item->id) . ".pdf"
                        ];
                    }
                }
            }

            $mail = false;
            if ($item != null) {
                foreach ($files as $list) {
                    $addresses = [];
                    foreach (explode(",", str_replace(" ", "", $item->client->email)) as $key => $address) {
                        $addresses[] = [
                            "type" => $key ? "cc" : "to",
                            "email" => $address,
                            "name" => $item->client->email,
                        ];
                    }
                    $sent = Mail::send(
                        $addresses,
                        "Nuevo documento de " . \App\Entity\Document\Factura\Factura::class,
                        '<html lang="">
                        <head>
                            <meta charset="utf8">
                        </head>
                        <body>

                        </body>
                    </html>',
                        $list
                    );
                    if ($sent)
                        $mail = true;
                }
            }

            if ($mail)
                return new Http\JsonResponse(['message' => "Documento enviado correctamente"]);
            else
                return new Http\JsonResponse(['message' => "Falló el envío del documento", 500]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    protected static function generateOrdenCobro(int $id): Http\JsonResponse
    {
        try {

            $doc = \App\Entity\Document\Factura\Factura::findOneBy(['id' => $id, 'deletedOn' => null]);

            if (!$doc->client)
                return new Http\JsonResponse(['message' => "Factura sin cliente asignado"], 404);
            $em = DB::getManager();

            $date = self::getDueDate($doc);

            $client = \App\Entity\Client::findOneBy(['id' => $doc->client->id, 'deletedOn' => null]);
            $orden = \App\Entity\OrdenCobro::findOneBy([ "client" => $client,"date" => $date, "active" => false]);
            $em->persist($orden);
            $doc->ordenCobro = $orden;
            $em->persist($doc);
            $em->flush();
            return new Http\JsonResponse(['item' => $doc->model(\App\Model\Document\Factura\Factura::class)->json()]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function getDueDate(\App\Entity\Document\Factura\Factura $item): DateTime|null
    {
        try {
            if ($item->ordenCobro)
                return $item->ordenCobro->date;
            else {
                //Obtenemos el día de cobro de la factura
                $newFecha = clone $item->date;
                //Le sumamos los dias de pago al día de cobro de la factura
                $newFecha->add(new DateInterval("P" . $item->client->diasPago . "D"));

                //Si la fecha nueva es superior al día fijo de pago estipulado
                //Entonces le sumamos un mes a esta fecha
                if ($newFecha->format("j") > $item->client->diaFijoPago)
                    $newFecha->add(new DateInterval("P1M"));

                //si el mes tiene menos días que el día de pago fijado del cliente ponemos el último día del mes.
                if ($item->client->diaFijoPago > date("t"))
                    $date = $newFecha->modify("last day of this month");
                else {
                    $dateFactura = $newFecha->format("Y-m");
                    $date = DateTime::createFromFormat("Y-m-d", $dateFactura . "-" . $item->client->diaFijoPago);
                }
                // FIN CALCULO FECHA VENCIMIENTO
                return $date;
            }
        } catch (Exception $e) {
            return null;
        }
    }

}