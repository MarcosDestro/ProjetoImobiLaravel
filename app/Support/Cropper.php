<?php

namespace App\Support;

use CoffeeCode\Cropper\Cropper as CropperCropper;

class Cropper
{
    // Tornar estático sem instanciar a classe
    public static function thumb(string $uri, int $weight, int $height = null)
    {   
        //Caminho para criar a pasta de cache
        $cropper = new CropperCropper('../public/storage/cache');
        /** O caminho pode ser melhorado */

        $pathThumb = $cropper->make(
            //Caminho absoluto da imagem
            config('filesystems.disks.public.root') . '/'. $uri,
            $weight,
            $height);
        
        $file = "cache/". collect(explode("/", $pathThumb))->last();

        return $file;

    }

    public static function flush(?string $path)
    {
        /** Instancia do método original */
        $cropper = new CropperCropper('../public/storage/cache');

        /** Caso tenha path executa o flush pelo path, caso contrário, flush em tudo */
        if(!empty($path)){
            $cropper->flush($path);
        } else {
            $cropper->flush();
        }
    }

}