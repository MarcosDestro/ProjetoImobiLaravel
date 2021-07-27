<?php

namespace App\Models;

use App\Support\Cropper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PropertyImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'property',
        'path',
        'cover'
    ];

    public function getUrlCroppedAttribute($weight = 1366, $height = 768)
    {
        // Caminho de storage, recortando a imagem passando o path
        return Storage::url(Cropper::thumb($this->path, ($weight ?? 1366), ($height ?? 768)));
    }
}
