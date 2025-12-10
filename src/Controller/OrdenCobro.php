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
            return new Http\JsonResponse($item->model(\App\Model\OrdenCobro::class)->json());
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

            $item->deletedOn = new DateTime();

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

    public static function print()
    {
        try {
            $em = DB::getManager();
            $orden = $em->getReference(OrdenCobro::class, self::g("orden", 0));
            $total = 0;

            foreach ($orden->getFacturas() as $factura)
                $total += $factura->getTotal();
            //Set the initial information
            // third parameter 'pain.008.003.02' is optional would default to 'pain.008.002.02' if not changed
            $directDebit = TransferFileFacadeFactory::createDirectDebit($orden->getReference(), Config::get("modules.factura.empresa.nombre_fiscal"), 'pain.008.003.02');

            // create a payment, it's possible to create multiple payments,
            // "firstPayment" is the identifier for the transactions
            // This creates a one time debit. If needed change use ::S_FIRST, ::S_RECURRING or ::S_FINAL respectively
            $directDebit->addPaymentInfo($orden->getReference(), array(
                'id' => 'firstPayment',
                'dueDate' => $orden->getDate(), // optional. Otherwise, default period is used
                'creditorName' => Config::get("modules.factura.empresa.nombre_fiscal"),
                'creditorAccountIBAN' => Config::get("modules.factura.empresa.iban"),
                'creditorAgentBIC' => Config::get("modules.factura.empresa.bic"),
                'seqType' => PaymentInformation::S_ONEOFF,
                'creditorId' => Config::get("modules.factura.empresa.creditor_id"),
                'localInstrumentCode' => 'CORE' // default. optional.
            ));
            // Add a Single Transaction to the named payment
            $directDebit->addTransfer($orden->getReference(), array(
                'amount' => $total * 100,
                'debtorIban' => str_replace(" ", "", $orden->getClient()->getIban()),
                'debtorBic' => $orden->getClient()->getBanco(),
                'debtorName' => $orden->getClient()->getName(),
                'debtorMandate' => '',
                'debtorMandateSignDate' => '',
                'remittanceInformation' => $orden->getFacturas()->first()->getNumber()
            ));
            // Retrieve the resulting XML
            header("Content-type: text/xml");
            echo $directDebit->asXML();
            exit();
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public static function generarAdeudos() {
        try {
            $cacheDirectory = FileSystem::getPath(Directory::APP) . '/cache';

            $zip_file = "sepa-" . time();
            if (!is_dir($cacheDirectory . "/sepa/" . $zip_file))
                mkdir($cacheDirectory . "/sepa/" . $zip_file, 0777, true);

            $tmp_file = $cacheDirectory . "/sepa/" . hash("sha256", time());

            $em = DB::getManager();
            $ordenes = $em->getRepository(OrdenCobro::class)->findByPagada(false);

            $directDebit = TransferFileFacadeFactory::createDirectDebit('Ymd', Config::get("modules.factura.empresa.nombre_fiscal"));

            foreach($ordenes as $orden) {

                $total = 0;

                foreach($orden->getFacturas() as $factura)
                    $total += $factura->getTotal();

                $directDebit->addPaymentInfo( $orden->getReference(), array(
                    'id'                    => 'firstPayment',
                    'dueDate'               => $orden->getDate(), // optional. Otherwise, default period is used
                    'creditorName'          => Config::get("modules.factura.empresa.nombre_fiscal"),
                    'creditorAccountIBAN'   => Config::get("modules.factura.empresa.iban"),
                    'creditorAgentBIC'      => Config::get("modules.factura.empresa.bic"),
                    'seqType'               => PaymentInformation::S_ONEOFF,
                    'creditorId'            => Config::get("modules.factura.empresa.creditor_id"),
                    'localInstrumentCode'   => 'CORE' // default. optional.
                ));

                // Add a Single Transaction to the named payment
                $directDebit->addTransfer( $orden->getReference(), array(
                    'amount'                => $total*100,
                    'debtorIban'            => str_replace(" ", "", $orden->getClient()->getIban()),
                    'debtorBic'             => $orden->getClient()->getBanco(),
                    'debtorName'            => $orden->getClient()->getName(),
                    'debtorMandate'         => '',
                    'debtorMandateSignDate' => '',
                    'remittanceInformation' => $orden->getFacturas()->first()->getNumber()
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

            if(file_exists($cacheDirectory . "/sepa/" . $zip_file . "/" . date('d-m-Y') . ".xml")){
                header('Content-disposition: attachment; filename=' . "SEPA" . "-" . time() . '.xml');
                header('Content-type: text/xml');
                readfile($cacheDirectory . "/sepa/" . $zip_file . "/" . date('d-m-Y') . ".xml");
                exit();
            }else{
                die("Algo ha fallado");
            }
        } catch (Exception|ORMException $e) {
            return new Http\JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

}