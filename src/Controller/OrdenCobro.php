<?php

namespace App\Controller;

use App\Model\DataTable;
use Cavesman\Config;
use Cavesman\Db;
use Cavesman\Enum\Directory;
use Cavesman\FileSystem;
use Cavesman\Http;
use Cavesman\Request;
use DateTime;
use Digitick\Sepa\PaymentInformation;
use Digitick\Sepa\TransferFile\Factory\TransferFileFacadeFactory;
use Doctrine\ORM\Exception\ORMException;
use Exception;

class OrdenCobro
{

    public static function filter(): Http\JsonResponse
    {
        try {
            $em = Db::getManager();

            $qb = $em->getRepository(\App\Entity\OrdenCobro::class)
                ->createQueryBuilder('i')
                ->where('i.deletedOn IS NULL');

            $filter = json_decode(Request::get('filter', '[]'));

            if ($filter && $filter->search) {
                foreach (explode(' ', $filter->search->value) as $key => $string) {
                    $qb
                        ->andWhere('i.reference LIKE :search_' . $key)
                        ->setParameter('search_' . $key, '%' . $string . '%');
                }
            }

            $total = clone $qb;
            $total->select('COUNT(i.id)');
            $recordsTotal = (int)$total->getQuery()->getSingleScalarResult();

            if ($filter->order && $filter->columns) {
                foreach ($filter->order as $order) {
                    $index = $order->column;
                    $columnName = $filter->columns[$index]->data;
                    $dir = strtoupper($order->dir);
                    if ($dir === 'ASC' || $dir === 'DESC')
                        $qb->addOrderBy('i.' . $columnName, $dir);
                }
            }

            if ($filter->length ?? false) {
                $qb->setMaxResults($filter->length)
                    ->setFirstResult($filter->start);
            }

            /** @var \App\Entity\OrdenCobro[] $list */
            $list = $qb->getQuery()->getResult();

            $datatable = new DataTable();
            $datatable->recordsTotal = $recordsTotal;
            $datatable->recordsFiltered = $recordsTotal;

            foreach ($list as $item) {
                foreach ($item->facturas as $factura) {
                    $factura->ordenCobro = null;
                }
                /** @var \App\Model\OrdenCobro $model */
                $model = $item->model(\App\Model\OrdenCobro::class);
                $datatable->data[] = $model->json();
            }

            return new  Http\JsonResponse($datatable);
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }

    }

    public static function get(int $id): Http\JsonResponse
    {
        try {

            $item = \App\Entity\OrdenCobro::findOneBy(['id' => $id, 'deletedOn' => null]);
            foreach ($item->facturas as $factura) {
                $factura->ordenCobro = null;
            }

            /** @var \App\Model\OrdenCobro $model */
            $model = ($item->model(\App\Model\OrdenCobro::class));
            foreach ($model->facturas as $factura) {
                $factura->date = $factura->date->format('Y-m-d\TH:i');
            }

            return new Http\JsonResponse($model->json());
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function add(): Http\JsonResponse
    {
        try {

            $model = \App\Model\OrdenCobro::fromRequest();

            if (!$model->reference)
                return new Http\JsonResponse(['message' => 'No se ha recibido todos los datos requeridos *'], 400);

            /** @var \App\Entity\OrdenCobro $entity */
            $entity = $model->entity();

            $em = DB::getManager();
            $em->persist($entity);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Orden de cobro añadido correctamente",
                'item' => $entity->model(\App\Model\OrdenCobro::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function update(int $id): Http\JsonResponse
    {
        try {
            $item = \App\Entity\OrdenCobro::findOneBy(['id' => $id, 'deletedOn' => null]);

            if (!$item)
                return new Http\JsonResponse(['message' => "Orden de cobro no encontrado"], 404);

            $model = \App\Model\OrdenCobro::fromRequest();

            $model->facturas = [];

            if (!$model->reference)
                return new Http\JsonResponse(['message' => 'No se ha recibido todos los datos requeridos *'], 400);

            if ($id != $model->id)
                return new Http\JsonResponse(['message' => "La id indicada en la url no corresponde a la enviada en el modelo"], 404);

            /** @var \App\Entity\OrdenCobro $entity */
            $entity = $model->entity();
            $em = DB::getManager();
            $em->persist($entity);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Orden de cobro actualizado correctamente",
                'item' => $entity->model(\App\Model\OrdenCobro::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function delete(int $id): Http\JsonResponse
    {
        try {

            $em = DB::getManager();

            $item = \App\Entity\OrdenCobro::findOneBy(['id' => $id, 'deletedOn' => null]);

            foreach ($item->facturas as $factura) {
                $factura->ordenCobro = null;
                $em->persist($factura);
            }

            $item->delete();

            $em->persist($item);
            $em->flush();

            return new Http\JsonResponse([
                'message' => "Orden de cobro eliminado correctamente",
                'item' => $item->model(\App\Model\OrdenCobro::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function print(int $id)
    {
        try {
            if (!$id)
                return new Http\JsonResponse(['message' => "ID no encontrada"], 404);

            $em = DB::getManager();
            $orden = $em->getRepository(\App\Entity\OrdenCobro::class)->findOneBy(['id' => $id]);

            $total = 0;

            foreach ($orden->facturas as $factura)
                $total += $factura->total;
            //Set the initial information
            // third parameter 'pain.008.003.02' is optional would default to 'pain.008.002.02' if not changed
            $directDebit = TransferFileFacadeFactory::createDirectDebit($orden->reference,Config::get("modules.factura.empresa.nombre_fiscal"), 'pain.008.003.02');

            // create a payment, it's possible to create multiple payments,
            // "firstPayment" is the identifier for the transactions
            // This creates a one time debit. If needed change use ::S_FIRST, ::S_RECURRING or ::S_FINAL respectively
            $directDebit->addPaymentInfo($orden->reference, array(
                'id' => 'firstPayment',
                'dueDate' => $orden->date, // optional. Otherwise, default period is used
                'creditorName' => Config::get("modules.factura.empresa.nombre_fiscal"),
                'creditorAccountIBAN' => Config::get("modules.factura.empresa.iban"),
                'creditorAgentBIC' => Config::get("modules.factura.empresa.bic"),
                'seqType' => PaymentInformation::S_ONEOFF,
                'creditorId' => Config::get("modules.factura.empresa.creditor_id"),
                'localInstrumentCode' => 'CORE' // default. optional.
            ));
            // Add a Single Transaction to the named payment
            $directDebit->addTransfer($orden->reference, array(
                'amount' => $total * 100,
                'debtorIban' => str_replace(" ", "", $orden->client->iban ?? ""),
                'debtorBic' => ($orden->client->banco ?? ""),
                'debtorName' => ($orden->client->name ?? ""),
                'debtorMandate' => '',
                'debtorMandateSignDate' => '',
                'remittanceInformation' => ($orden->facturas->first()->number ?? "")
            ));
            // Retrieve the resulting XML
            header("Content-type: text/xml");
            echo $directDebit->asXML();
            exit();
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function generarAdeudos()
    {
        try {
            $cacheDirectory = FileSystem::getPath(Directory::APP) . '/cache';

            $zip_file = "sepa-" . time();
            if (!is_dir($cacheDirectory . "/sepa/" . $zip_file))
                mkdir($cacheDirectory . "/sepa/" . $zip_file, 0777, true);

            $em = DB::getManager();
            $ordenes = $em->getRepository(\App\Entity\OrdenCobro::class)->findBy(['pagada' => false]);

            $directDebit = TransferFileFacadeFactory::createDirectDebit('Ymd', Config::get("modules.factura.empresa.nombre_fiscal"));

            foreach ($ordenes as $orden) {

                $total = 0;

                foreach ($orden->facturas as $factura)
                    $total += $factura->total;

                $directDebit->addPaymentInfo($orden->reference, array(
                    'id' => 'firstPayment',
                    'dueDate' => $orden->date, // optional. Otherwise, default period is used
                    'creditorName' => Config::get("modules.factura.empresa.nombre_fiscal"),
                    'creditorAccountIBAN' => Config::get("modules.factura.empresa.iban"),
                    'creditorAgentBIC' => Config::get("modules.factura.empresa.bic"),
                    'seqType' => PaymentInformation::S_ONEOFF,
                    'creditorId' => Config::get("modules.factura.empresa.creditor_id"),
                    'localInstrumentCode' => 'CORE' // default. optional.
                ));

                // Add a Single Transaction to the named payment
                $directDebit->addTransfer($orden->reference, array(
                    'amount' => $total * 100,
                    'debtorIban' => str_replace(" ", "", $orden->client->iban ?? ""),
                    'debtorBic' => ($orden->client->banco ?? ""),
                    'debtorName' => ($orden->client->name ?? ""),
                    'debtorMandate' => '',
                    'debtorMandateSignDate' => '',
                    'remittanceInformation' => ($orden->facturas->first()->number ?? "")
                ));


            }

            file_put_contents(
                $cacheDirectory . "/sepa/" . $zip_file . "/" . date('d-m-Y') . ".xml",
                $directDebit->asXML()
            );

            /*$zip = new \ZipArchive;
            if ($zip->open($cacheDirectory . "/sepa/" . $zip_file . "/sepa.zip", \ZipArchive::CREATE)) {
                foreach (glob($cacheDirectory . "/sepa/" . $zip_file . "/*.xml") as $xml) {
                    $zip->addFile($xml, basename($xml));
                }
                $zip->close();
    
            } else {
                echo 'Failed!';
            }*/

            if (file_exists($cacheDirectory . "/sepa/" . $zip_file . "/" . date('d-m-Y') . ".xml")) {
                header('Content-disposition: attachment; filename=' . "SEPA" . "-" . time() . '.xml');
                header('Content-type: text/xml');
                readfile($cacheDirectory . "/sepa/" . $zip_file . "/" . date('d-m-Y') . ".xml");
                exit();
            } else {
                die("Algo ha fallado");
            }
        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function hasUnpaid(): Http\JsonResponse
    {
        try {

            $em = DB::getManager();
            $ordenes = $em->getRepository(\App\Entity\OrdenCobro::class)->findBy(['pagada' => false]);

            if (count($ordenes) == 0)
                return new Http\JsonResponse(['message' => "No se encontraron adeudos sin pagar"], 404);
            else
                return new Http\JsonResponse(['total' => count($ordenes)]);

        } catch (Exception $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

}