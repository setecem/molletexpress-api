<?php

namespace App\Controller;

use App\Entity\Document\Albaran\Albaran;
use App\Entity\Document\Factura\Factura;
use App\Entity\Employee\Employee;
use App\Enum\FileType;
use App\Tool\Fs;
use Cavesman\Config;
use Cavesman\Db;
use Cavesman\FileSystem;
use Cavesman\Http\JsonResponse;
use Cavesman\Http\Response;
use Cavesman\Request;
use Doctrine\ORM\Exception\ORMException;
use Exception;

final class File
{

    public static function uploadInDataBase($id): JsonResponse
    {
        try {

            $item = \App\Entity\File::findOneBy(['id' => $id, 'deletedOn' => null]);

            if (!$item)
                return new JsonResponse(['message' => "Archivo no encontrado"], 404);

            $model = \App\Model\File::fromRequest();

            if ($id != $model->id)
                return new JsonResponse(['message' => "La id indicada en la url no corresponde a la enviada en el modelo"], 404);

            /** @var \App\Entity\File $entity */
            $entity = $model->entity();
            $em = DB::getManager();
            $em->persist($entity);
            $em->flush();

            return new JsonResponse([
                'message' => "Actualizado correctamente",
                'item' => $entity->model(\App\Model\File::class)->json()
            ]);
        } catch (Exception|ORMException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }

    }

    public static function upload(): JsonResponse
    {
        try {

            $em = Db::getManager();

            $section = Request::post('section', false);

            if (!$section)
                return new JsonResponse(['message' => 'Sección no encontrada'], 404);

            $item = null;

            switch ($section) {
                case 'employee':
                case 'employee-signed':
                case 'employee-validated':
                case 'employee-delivery':
                    $item = Employee::findOneBy(['id' => Request::post('item')]);
                    break;
                case 'invoice':
                case 'invoice-signed':
                case 'invoice-validated':
                case 'invoice-delivery':
                    $item = Factura::findOneBy(['id' => Request::post('item')]);
                    break;
                case 'delivery-note':
                case 'delivery-note-signed':
                case 'delivery-note-validated':
                case 'delivery-note-delivery':
                    $item = Albaran::findOneBy(['id' => Request::post('item')]);
                    break;
            }

            if (!$item)
                return new JsonResponse(['message' => 'Objeto no encontrado'], 404);

            if (!Request::files('file'))
                return new JsonResponse(['message' => 'No se han encontrado archivos para subir'], 400);

            $directory = Config::get('files.' . $section, '%ROOT%/data/files/' . $section);
            $directory = str_replace('%ROOT%', FileSystem::documentRoot(), $directory);

            foreach (str_split(Request::post('item')) as $number) {
                $directory .= '/' . $number;
            }

            if (!is_dir($directory))
                mkdir($directory, 0777, true);

            $data = [];

            // Recorre cada archivo subido
            foreach (Request::files('file')['name'] as $key => $nombreArchivo) {
                try {
                    // Recoge la información necesaria de cada archivo
                    $name = $nombreArchivo;
                    $mime = $_FILES['file']['type'][$key];
                    $size = $_FILES['file']['size'][$key];
                    $source = $_FILES['file']['tmp_name'][$key];

                    $file = new \App\Entity\File();

                    $file->name = $name;
                    $file->mime = $mime;
                    $file->size = $size;

                    if ($section == 'employee') {
                        $file->employee = $item;
                    }

                    $em->persist($file);
                    $em->flush();

                    if (!@copy($source, $directory . '/' . $file->id . '.' . pathinfo($file->name, PATHINFO_EXTENSION))) {
                        $em->remove($file);
                        $em->flush();
                        continue;
                    }

                } catch (Exception|ORMException $e) {
                    return new JsonResponse(['message' => 'file.error.save', 'exception' => $e->getMessage()], 500);
                }

                $data[] = [
                    "id" => $file->id,
                    "name" => $file->name,
                    "type" => $file->mime,
                    "size" => $file->size
                ];

            }
            return new JsonResponse([
                'file' => $data,
                'message' => 'Archivo subido con éxito'
            ]);

        } catch (Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }

    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public static function delete($id): JsonResponse
    {
        try {
            $em = Db::getManager();

            /** @var \App\Entity\File $file */
            $file = \App\Entity\File::findOneBy(['id' => $id, 'deletedOn' => null]);

            if (!$file)
                return new JsonResponse(['message' => 'error.file.not-found'], 404);

            /** @var \App\Model\File $model */
            $model = $file->model(\App\Model\File::class);

            $section = 'default';

            $item = null;

            if ($file->employee) {
                $section = 'employee';
                $item = $file->employee;
            }

            $em->remove($file);
            $em->flush();

            $directory = Config::get('files.' . $section, '%ROOT%/data/files/' . $section);
            $directory = str_replace('%ROOT%', FileSystem::documentRoot(), $directory);

            $directoryFinal = $directory;
            foreach (str_split($item->id) as $number) {
                $directoryFinal .= '/' . $number;
            }

            if (!is_dir($directory))
                return new JsonResponse(['message' => 'file.success.delete']);

            $sourceFile = $directoryFinal . '/' . $id . '.' . pathinfo($model->name, PATHINFO_EXTENSION);

            if (file_exists($sourceFile))
                unlink($sourceFile);

            Fs::clearEmptyFolders($directory);

            return new JsonResponse(['message' => 'file.success.delete']);
        } catch (Exception|ORMException $e) {
            return new JsonResponse(['message' => 'file.error.delete', 'exception' => $e->getMessage()], 404);
        }

    }

    public static function download($id): JsonResponse|Response
    {

        try {
            $file = \App\Entity\File::findOneBy(['id' => $id]);

            $section = self::getSectionFromType($file);
            $item = self::getItemFromType($file);

            $directory = Config::get('files.' . $section, '%ROOT%/data/files/' . $section);
            $directory = str_replace('%ROOT%', FileSystem::documentRoot(), $directory);

            foreach (str_split($item->id) as $number) {
                $directory .= '/' . $number;
            }

            if (!is_dir($directory))
                mkdir($directory, 0777, true);

            $sourceFile = $directory . '/' . $id . '.' . pathinfo($file->name, PATHINFO_EXTENSION);
            if (!file_exists($sourceFile))
                return new JsonResponse('', 404);

            $source = file_get_contents($sourceFile);

            header('Content-disposition: filename="' . $file->name . '"');
            return new Response($source, 200, $file->mime);
        } catch (Exception $e) {
            return new JsonResponse(['message' => 'file.error.delete', 'exception' => $e->getMessage()], 404);
        }


    }

    /**
     * @param \App\Entity\File $file
     * @return string
     */
    public static function getSectionFromType(\App\Entity\File $file): string
    {
        //            case FileType::SIGNED:
        //                $section = 'default';
        //                if ($file->contact) {
        //                    $section = 'contact-signed';
        //                } elseif ($file->employee) {
        //                    $section = 'employee-signed';
        //                }
        //                break;
        //            case FileType::VALIDATED:
        //                $section = 'default';
        //                if ($file->contact) {
        //                    $section = 'contact-validated';
        //                } elseif ($file->employee) {
        //                    $section = 'employee-validated';
        //                }
        //                break;
        //            case FileType::DELIVERY:
        //                $section = 'default';
        //                if ($file->contact) {
        //                    $section = 'contact-delivery';
        //                } elseif ($file->employee) {
        //                    $section = 'employee-delivery';
        //                }
        //                break;
        if ($file->type == FileType::GENERAL) {
            $section = 'default';
            if ($file->employee) {
                $section = 'employee';
            }
        }
        return $section ?? 'default';
    }

    /**
     * @param \App\Entity\File|null $file
     * @return Employee
     */
    public static function getItemFromType(?\App\Entity\File $file): Employee
    {
        $item = null;

        //            case FileType::SIGNED:
        //            case FileType::VALIDATED:
        //            case FileType::DELIVERY:
        if ($file->type == FileType::GENERAL) {
            if ($file->employee) {
                $item = $file->employee;
            }
        }
        return $item;
    }
}