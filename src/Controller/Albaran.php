<?php

namespace App\Controller;

use App\Entity\Document\Albaran\AlbaranLinea;
use App\Entity\Document\Factura\FacturaLinea;
use App\Model\DataTable;
use App\Model\Pdf\DefaultPdf;
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
use ReflectionClass;
use ZipArchive;

class Albaran
{
    public static array $config = [];

    public static function filterAll(): Http\JsonResponse
    {
        try {
            $em = Db::getManager();

            $qb = $em->getRepository(\App\Entity\Document\Albaran\Albaran::class)
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

            /** @var \App\Entity\Document\Albaran\Albaran[] $list */
            $list = $qb->getQuery()->getResult();

            $datatable = new DataTable();
            $datatable->recordsTotal = count($total->getQuery()->getResult());
            foreach ($list as $item) {
                /** @var \App\Model\Document\Albaran\Albaran $model */
                $model = $item->model(\App\Model\Document\Albaran\Albaran::class);
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

            $qb = $em->getRepository(\App\Entity\Document\Albaran\Albaran::class)
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

            /** @var \App\Entity\Document\Albaran\Albaran[] $list */
            $list = $qb->getQuery()->getResult();

            $datatable = new DataTable();
            $datatable->recordsTotal = count($total->getQuery()->getResult());
            foreach ($list as $item) {
                /** @var \App\Model\Document\Albaran\Albaran $model */
                $model = $item->model(\App\Model\Document\Albaran\Albaran::class);
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

            $item = \App\Entity\Document\Albaran\Albaran::findOneBy(['id' => $id, 'deletedOn' => null]);

            return new Http\JsonResponse($item->model(\App\Model\Document\Albaran\Albaran::class)->json());
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function add(): Http\JsonResponse
    {
        try {

            $model = \App\Model\Document\Albaran\Albaran::fromRequest();

            /** @var \App\Entity\Document\Albaran\Albaran $entity */
            $entity = $model->entity();

            $em = DB::getManager();

            foreach ($entity->lineas as $linea) {
                $linea->albaran = $entity;
            }

            $em->persist($entity);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Albarán añadido correctamente",
                'item' => $entity->model(\App\Model\Document\Albaran\Albaran::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function update(int $id): Http\JsonResponse
    {
        try {
            $item = \App\Entity\Document\Albaran\Albaran::findOneBy(['id' => $id, 'deletedOn' => null]);

            if (!$item)
                return new Http\JsonResponse(['message' => "Albarán no encontrado"], 404);

            $model = \App\Model\Document\Albaran\Albaran::fromRequest();

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

            /** @var \App\Entity\Document\Albaran\Albaran $entity */
            $entity = $model->entity();

            foreach ($entity->lineas as $linea) {
                $linea->albaran = $entity;
            }

            $em->persist($entity);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Albarán actualizado correctamente",
                'item' => $entity->model(\App\Model\Document\Albaran\Albaran::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function delete(int $id): Http\JsonResponse
    {
        try {

            $em = DB::getManager();

            $item = \App\Entity\Document\Albaran\Albaran::findOneBy(['id' => $id, 'deletedOn' => null]);

            $item->deletedOn = new DateTime();

            $em->persist($item);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Albarán eliminado correctamente",
                'item' => $item->model(\App\Model\Document\Albaran\Albaran::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function factura(int $id): Http\JsonResponse
    {
        try {

            $item = \App\Entity\Document\Albaran\Albaran::findOneBy(['id' => $id, 'deletedOn' => null]);

            /** @var \App\Model\Document\Albaran\Albaran $albaran */
            $albaran = $item->model(\App\Model\Document\Albaran\Albaran::class);
            $albaran->id = null;

            foreach ($albaran->lineas as $linea) {
                $linea->id = null;
            }

            // TODO: Clonar con json_decode etc

            $array = json_decode(json_encode($albaran->json()), true);

            $factura = new \App\Model\Document\Factura\Factura($array);

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
                'item' => $item->model(\App\Model\Document\Albaran\Albaran::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function facturar(string $dateStart, string $dateEnd, int $idClient, string $dateFactura): Http\JsonResponse
    {
        try {
            $dateStart = new DateTime($dateStart);
            $dateEnd = new DateTime($dateEnd);
            $client = \App\Entity\Client::findOneBy(['id' => $idClient, 'deletedOn' => null]);
            $dateFactura = new DateTime($dateFactura);

            $em = DB::getManager();

            if (file_exists(new ReflectionClass(FacturaPdf::class)->getFileName()))
                require_once new ReflectionClass(FacturaPdf::class)->getFileName();
            else
                require_once new ReflectionClass(DefaultPdf::class)->getFileName();

            $resultItems = $em
                ->createQueryBuilder()
                ->select('o')
                ->from(\App\Entity\Document\Albaran\Albaran::class, "o")
                ->leftJoin("o.factura", "f")
                ->leftJoin("o.client", "c")
                ->where('o.date BETWEEN :dateStart AND :dateEnd')
                ->andWhere("o.factura IS NULL OR f.bloqueada = 0")
                ->orderBy("c.numAbonado", "ASC")
                ->addOrderBy("o.date", "ASC")
                ->setParameter('dateStart', $dateStart)
                ->setParameter('dateEnd', $dateEnd);
            $resultItems
                ->andWhere("o.client = :client")
                ->setParameter('client', $client);
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
                return new Http\JsonResponse(['message' => "Sin resultados: No hay pendientes de facturar"], 404);

            $zipFile = "facturas-" . time();
            $cacheDirectory = FileSystem::getPath(Directory::APP) . '/cache';

            if (!is_dir($cacheDirectory . "/pdf/" . $zipFile))
                mkdir($cacheDirectory . "/pdf/" . $zipFile, 0777, true);

            $serie = Config::get("modules.factura.factura.serie");
            $dateStart2 = new DateTime();
            $dateStart2->setDate($dateStart2->format('Y'), 1, 1)->setTime(0, 0);
            $dateEnd2 = clone $dateStart2;
            $dateEnd2->add(new DateInterval("P1Y"))->sub(new DateInterval("P1D"));
            foreach ($clients as $c) {
                $dateReference = clone $dateFactura;
                $dateReference->setDate($dateReference->format('Y'), 1, 1)->setTime(0, 0);
                $dateEndReference = clone $dateStart2;
                $dateEndReference->add(new DateInterval("P1Y"))->sub(new DateInterval("P1D"));
                $reference = Factura::getLastReference($serie, $dateReference, $dateEndReference);
                $number = static::parseFormat($serie, $dateReference, $reference);
                $f = $em->getRepository(\App\Entity\Document\Factura\Factura::class)->findOneBy(
                    [
                        "client" => $c['client'],
                        "bloqueada" => false
                    ]
                );
                if (!$f) {
                    $f = new \App\Entity\Document\Factura\Factura();
                    $f->number = $number;
                    $f->serie = $serie;
                    $f->reference = $reference;
                    $f->client = $c['client'];
                    $f->date = $dateFactura;
                } else {
                    $lineas = $em->getRepository(FacturaLinea::class)->findBy(['factura' => $f]);
                    foreach ($lineas as $l) {
                        $lineasAlbaran = $em->getRepository(AlbaranLinea::class)->findBy(['facturaLinea' => $l]);
                        foreach ($lineasAlbaran as $la) {
                            $la->facturaLinea = null;
                            $em->persist($la);
                        }
                        $em->remove($l);
                    }
                }

                $f->albaran = $c['items'][0];
                $em->persist($f);
                $subtotal = 0;
                $total = 0;

                foreach ($c['items'] as $item) {
                    $lines = $em->getRepository(FacturaLinea::class)->findBy(['client' => $item]);

                    foreach ($lines as $l) {
                        $line = new AlbaranLinea();
                        $line->albaran = $item;
                        $line->facturaLinea = $l;
                        $line->reference = $item->number;
                        $line->description = $l->description;
                        $line->discount = $l->discount;
                        $line->price = $l->price;
                        $line->total = $l->total;
                        $subtotal += $l->total;
                        $line->quantity = $l->quantity;
                        $line->tax = $l->tax;
                        $d = $l->total - ($l->total * $c['client']->descuento / 100);
                        $total += ($d + (($d * $l->tax) / 100));
                        $em->persist($line);
                        $l->factura = $f;
                        $l->facturaLinea = $line;
                        $em->persist($l);
                    }
                }
                $f->importeBruto = $subtotal;
                if ($c['client']->descuento) {
                    $f->discount = $c['client']->discount;
                    $descuento = $subtotal * $c['client']->descuento / 100;
                    $subtotal = $subtotal - $descuento;
                    $f->impDiscount = $descuento;
                }

                $f->subtotal = $subtotal;
                $f->total = $total;
                $f->bloqueada = true;
                $em->persist($f);
                try {
                    foreach ($c['items'] as $item) {
                        $item->factura = $f;
                        $em->persist($item);
                    }
                    $em->flush();
                } catch (Exception $e) {
                    return new Http\JsonResponse(['message' => $e->getMessage()], 500);
                }
                $invoice = Factura::print($f->id, true);
                $invoice->render($cacheDirectory . "/pdf/" . $zipFile . "/" . $f->getNumber() . ".pdf", 'F');
            }
            die();
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    protected static function export(string $dateStart, string $dateEnd, int $idClient)
    {
        $cacheDirectory = FileSystem::getPath(Directory::APP) . '/cache';
        try {
            $em = DB::getManager();
            if (file_exists(new ReflectionClass(FacturaPdf::class)->getFileName()))
                require_once new ReflectionClass(FacturaPdf::class)->getFileName();
            else
                require_once new ReflectionClass(DefaultPdf::class)->getFileName();

            $resultItems = $em
                ->createQueryBuilder()
                ->select('o')
                ->from(\App\Entity\Document\Albaran\Albaran::class, "o")
                ->where('o.date BETWEEN :dateStart AND :dateEnd')
                ->orderBy("o.reference", "DESC")
                ->setParameter('dateStart', new DateTime($dateStart))
                ->setParameter('dateEnd', new DateTime($dateEnd));
            $client = \App\Entity\Client::findOneBy(['id' => $idClient, 'deletedOn' => null]);
            $resultItems
                ->andWhere("o.client = :client")
                ->setParameter('client', $client);
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

            $zipFile = \App\Entity\Document\Albaran\Albaran::class . "-" . time();
            if (!is_dir($cacheDirectory . "/pdf/" . $zipFile))
                mkdir($cacheDirectory . "/pdf/" . $zipFile, 0777, true);

            foreach ($clients as $c) {
                foreach ($c['items'] as $item) {
                    $invoice = self::print($item->id, true);
                    $invoice->render($cacheDirectory . "/pdf/" . $zipFile . "/" . $item->number . ".pdf", 'F');
                }
            }

            $zip = new ZipArchive;
            if ($zip->open($cacheDirectory . "/pdf/" . $zipFile . "/albaranes.zip", ZipArchive::CREATE)) {
                foreach (glob($cacheDirectory . "/pdf/" . $zipFile . "/*.pdf") as $pdf) {
                    $zip->addFile($pdf, basename($pdf));
                }
                $zip->close();
            } else
                echo 'Failed!';

            header('Content-disposition: attachment; filename=' . \App\Entity\Document\Albaran\Albaran::class . "-" . time() . '.zip');
            header('Content-type: application/zip');
            readfile($cacheDirectory . "/pdf/" . $zipFile . "/albaranes.zip");
            die("fin");
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function print(int $id, bool $export = false)
    {
        try {
            if (!$id)
                return new Http\JsonResponse(['message' => "ID no encontrada"], 404);

            $em = DB::getManager();
            $item = $em->getRepository(\App\Entity\Document\Albaran\Albaran::class)->findOneBy(['id' => $id]);
            $lineas = $em->getRepository(AlbaranLinea::class)->findBy(['albaran' => $item]);
            $client = $item->client ?? null;

            if (!$export) {
                if (file_exists(new ReflectionClass(FacturaPdf::class)->getFileName()))
                    require_once new ReflectionClass(FacturaPdf::class)->getFileName();
                else
                    require_once new ReflectionClass(DefaultPdf::class)->getFileName();
            }

            /* Header settings */
            $invoice = new FacturaPdf("A4", "€", "es");
            $logoPath = FileSystem::getPath(Directory::PUBLIC) . '/img/logo/logo-mollet.jpg';
            $invoice->setLogo($logoPath);  //logo image path
            $invoice->setColor("#007fff");      // pdf color scheme
            $invoice->type = "albaran";    // Invoice Type
            $invoice->reference = $item->number;   // Reference
            $invoice->date = $item->date->format("d-m-Y");   //Billing Date
            $invoice->setNumberFormat(",", ".", "right");

            $invoice->from = [
                Config::get("modules.albaran.empresa.nombre_fiscal"),
                Config::get("modules.albaran.empresa.nombre_fiscal2"),
                Config::get("modules.albaran.empresa.direccion"),
                Config::get("modules.albaran.empresa.localidad"),
                Config::get("modules.albaran.empresa.cp") . " " . Config::get("modules.albaran.empresa.provincia"),
                Config::get("modules.albaran.empresa.nif")
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
            $invoice->addTotal("Dto. P.P. " . $item->discountPP . "%", $item->impDiscountPP);
            $invoice->addTotal("Base Imponible", $item->subtotal);
            $invoice->addTotal("Tipo IVA 21%", $item->total - $item->subtotal);
            $invoice->addTotal("Total", $item->total);

            if ($client)
                $invoice->addParagraph("Forma de pago: " . $client->formaPago);

            $invoice->addParagraph("Vencimiento: " . self::getDueDate($item)->format("d-m-Y"));

            if ($client && $client->formaPago == "TRANSFERENCIA BANCARIA")
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

    public static function getDueDate(\App\Entity\Document\Albaran\Albaran $item): DateTime|null
    {
        try {
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
        } catch (Exception $e) {
            return null;
        }
    }

    protected static function list(string $dateStart, string $dateEnd, int $idClient): Http\JsonResponse
    {
        try {
            $em = DB::getManager();
            if (file_exists(new ReflectionClass(FacturaPdf::class)->getFileName()))
                require_once new ReflectionClass(FacturaPdf::class)->getFileName();
            else
                require_once new ReflectionClass(DefaultPdf::class)->getFileName();

            $resultItems = $em
                ->createQueryBuilder()
                ->select('o')
                ->from(\App\Entity\Document\Albaran\Albaran::class, "o")
                ->innerJoin('o.client', 'c')
                ->where('o.date BETWEEN :dateStart AND :dateEnd')
                ->orderBy("c.num_abonado", "ASC")
                ->setParameter('dateStart', new DateTime($dateStart))
                ->setParameter('dateEnd', new DateTime($dateEnd));
            $client = \App\Entity\Client::findOneBy(['id' => $idClient, 'deletedOn' => null]);
            $resultItems
                ->andWhere("o.client = :client")
                ->setParameter('client', $client);
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
            $pdf->title('Listado de ' . \App\Entity\Document\Albaran\Albaran::class);
            $pdf->ImprovedTable($header, $data);
            $pdf->Output();
            die();
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
            if (file_exists(new ReflectionClass(FacturaPdf::class)->getFileName()))
                require_once new ReflectionClass(FacturaPdf::class)->getFileName();
            else
                require_once new ReflectionClass(DefaultPdf::class)->getFileName();

            $item = $em->getRepository(\App\Entity\Document\Albaran\Albaran::class)->findOneBy(['id' => $id]);
            $invoice = self::print($item->id, true);
            $invoice->render($cacheDirectory . "/pdf/" . hash("sha256", $item->id) . ".pdf", 'F');
            if (!$item->client)
                return new Http\JsonResponse(['message' => "El documento seleccionado no tiene cliente asignado"], 400);

            $files[$item->client->id][] = [
                "name" => $item->number . ".pdf",
                "file" => $cacheDirectory . "/pdf/" . hash("sha256", $item->id) . ".pdf"
            ];

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
                        "Nuevo documento de " . \App\Entity\Document\Albaran\Albaran::class,
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

    private static function parseFormat(string $serie = "", ?object $date = NULL, int $reference = 0): string
    {

        $format = Config::get("modules.factura." . \App\Entity\Document\Albaran\Albaran::class . ".formato");
        if (!$format) {
            if (isset(self::$config[\App\Entity\Document\Albaran\Albaran::class]['formato']))
                $format = self::$config[\App\Entity\Document\Albaran\Albaran::class]['formato'];
        }

        $length = Config::get("modules.factura." . \App\Entity\Document\Albaran\Albaran::class . ".ref_length");
        if (!$length) {
            if (isset(self::$config[\App\Entity\Document\Albaran\Albaran::class]['ref_length']))
                $format = self::$config[\App\Entity\Document\Albaran\Albaran::class]['ref_length'];
        }

        $format = str_replace("{serie}", $serie, $format);
        $format = str_replace("{year}", $date->format("y"), $format);
        return str_replace("{reference}", str_pad($reference, $length, "0", STR_PAD_LEFT), $format);
    }
}