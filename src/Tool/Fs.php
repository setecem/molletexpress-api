<?php

namespace App\Tool;

use Cavesman\FileSystem;
use InvalidArgumentException;

final class Fs {

    public static function getAbsolutePath(string $path): string {
        return FileSystem::documentRoot() . $path;
    }

    /**
     * Elimina directorios vacíos de manera recursiva.
     *
     * @param string $dir La ruta del directorio para revisar.
     * @param bool $recursive
     * @return void
     * @throws InvalidArgumentException Si el directorio no existe.
     */
    public static function clearEmptyFolders(string $dir, bool $recursive = true): void {
        if (!is_dir($dir)) {
            throw new InvalidArgumentException("El directorio $dir no existe o no es accesible.");
        }

        $items = scandir($dir);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;

            $path = $dir . '/' . $item;

            if (is_dir($path)) {
                if ($recursive) {
                    self::clearEmptyFolders($path); // Recursividad para subdirectorios
                }

                if(is_dir($path)) {
                    // Después de procesar subdirectorios, verificamos si el directorio está vacío
                    $subItems = scandir($path);
                    if (count(array_diff($subItems, ['.', '..'])) === 0) {
                        rmdir($path);
                    }
                }
            }
        }
        if(is_dir($dir)) {
            // Verificar si el directorio actual está vacío después de procesar todos los elementos
            $items = scandir($dir);
            if (count(array_diff($items, ['.', '..'])) === 0) {
                rmdir($dir);
            }
        }
    }
}
