<?php

namespace App\Models;

use App\Support\Cropper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale',
        'rent',
        'category',
        'type',
        'user',
        'sale_price',
        'rent_price',
        'tribute',
        'condominium',
        'description',
        'bedrooms',
        'suites',
        'bathrooms',
        'rooms',
        'garage',
        'garage_covered',
        'area_total',
        'area_util',
        'zipcode',
        'street',
        'number',
        'complement',
        'neighborhood',
        'state',
        'city',
        'air_conditioning',
        'bar',
        'library',
        'barbecue_grill',
        'american_kitchen',
        'fitted_kitchen',
        'pantry',
        'edicule',
        'office',
        'bathtub',
        'fireplace',
        'lavatory',
        'furnished',
        'pool',
        'steam_room',
        'view_of_the_sea',
        'status',
        'title',
        'slug',
        'headline',
        'experience'
    ];

    public function user()
    {
        //Classe de relação, coluna local(chave), coluna extrangeira
        return $this->belongsTo(User::class, 'user', 'id');
    }

    public function images()
    {
        // Classe de relação, coluna extrangeira(chave), coluna local
        return $this->hasMany(PropertyImage::class, 'property', 'id');
    }

    public function cover()
    {   
        /** Pesquisa todas as imagens deste objeto */
        $images = $this->images();
        $cover = $images->where('cover', 1)->first(['path']);

        /** Se não encontrar */
        if(!$cover){
            /** Pega a primeira */
            $images = $this->images();
            $cover = $images->first(['path']);
        }

        /** Caso não exista o arquivo ou não tenha nenhuma foto */
        if(empty($cover['path']) || !File::exists('../public/storage/' . $cover->path)){
            return url(asset('backend/assets/images/property_cover.jpeg'));
        }
        /** Retorna o caminho completo para exibição */
        return Storage::url(Cropper::thumb($cover['path'], 1366, 768));
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 1);
    }

    public function scopeUnavailable($query)
    {
        return $query->where('status', 0);
    }

    public function scopeRent($query)
    {
        return $query->where('rent', 1);
    }

    public function scopeSale($query)
    {
        return $query->where('sale', 1);
    }

    public function setZipcodeAttribute($value)
    {
        $this->attributes['zipcode'] = $this->clearField($value);
    }

    public function getZipcodeAttribute($value)
    {
        return substr($value, 0, 5) . '-' . substr($value, 5, 3);
    }

    public function setSaleAttribute($value)
    {
        $this->attributes['sale'] = ($value == true || $value == 'on' ? 1 : 0);
    }

    public function setRentAttribute($value)
    {
        $this->attributes['rent'] = ($value == true || $value == 'on' ? 1 : 0);
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = ($value == '1' ? 1 : 0);
    }

    public function getStatusAttribute($value)
    {
        return ($value == 1 ? true : false);
    }

    public function setSalePriceAttribute($value)
    {
        if(empty($value)){
            $this->attributes['sale_price'] = null;
        } else {
            $this->attributes['sale_price'] = floatval($this->ConvertStringToDouble($value));
        }
    }

    public function getSalePriceAttribute($value)
    {
        return number_format($value, 2, ',', '.');
    }

    public function setRentPriceAttribute($value)
    {
        if(empty($value)){
            $this->attributes['rent_price'] = null;
        } else {
            $this->attributes['rent_price'] = floatval($this->ConvertStringToDouble($value));
        }
    }

    public function getRentPriceAttribute($value)
    {
        return number_format($value, 2, ',', '.');
    }

    public function setTributeAttribute($value)
    {
        if(empty($value)){
            $this->attributes['tribute'] = null;
        } else {
            $this->attributes['tribute'] = floatval($this->ConvertStringToDouble($value));
        }
    }

    public function getTributeAttribute($value)
    {
        return number_format($value, 2, ',', '.');
    }

    public function setCondominiumAttribute($value)
    {
        if(empty($value)){
            $this->attributes['condominium'] = null;
        } else {
            $this->attributes['condominium'] = floatval($this->ConvertStringToDouble($value));
        }
    }

    public function getCondominiumAttribute($value)
    {
        return number_format($value, 2, ',', '.');
    }

    public function setAirConditioningAttribute($value)
    {
        $this->attributes['air_conditioning'] = ($value == true || $value == 'on' ? 1 : 0);
    }

    public function setBarAttribute($value)
    {
        $this->attributes['bar'] = ($value == true || $value == 'on' ? 1 : 0);
    }

    public function setLibraryAttribute($value)
    {
        $this->attributes['library'] = ($value == true || $value == 'on' ? 1 : 0);
    }

    public function setBarbecueGrillAttribute($value)
    {
        $this->attributes['barbecue_grill'] = ($value == true || $value == 'on' ? 1 : 0);
    }

    public function setAmericanKitchenAttribute($value)
    {
        $this->attributes['american_kitchen'] = ($value == true || $value == 'on' ? 1 : 0);
    }

    public function setFittedKitchenAttribute($value)
    {
        $this->attributes['fitted_kitchen'] = ($value == true || $value == 'on' ? 1 : 0);
    }

    public function setPantryAttribute($value)
    {
        $this->attributes['pantry'] = ($value == true || $value == 'on' ? 1 : 0);
    }

    public function setEdiculeAttribute($value)
    {
        $this->attributes['edicule'] = ($value == true || $value == 'on' ? 1 : 0);
    }

    public function setOfficeAttribute($value)
    {
        $this->attributes['office'] = ($value == true || $value == 'on' ? 1 : 0);
    }

    public function setBathtubAttribute($value)
    {
        $this->attributes['bathtub'] = ($value == true || $value == 'on' ? 1 : 0);
    }

    public function setFireplaceAttribute($value)
    {
        $this->attributes['fireplace'] = ($value == true || $value == 'on' ? 1 : 0);
    }

    public function setLavatoryAttribute($value)
    {
        $this->attributes['lavatory'] = ($value == true || $value == 'on' ? 1 : 0);
    }

    public function setFurnishedAttribute($value)
    {
        $this->attributes['furnished'] = ($value == true || $value == 'on' ? 1 : 0);
    }

    public function setPoolAttribute($value)
    {
        $this->attributes['pool'] = ($value == true || $value == 'on' ? 1 : 0);
    }

    public function setSteamRoomAttribute($value)
    {
        $this->attributes['steam_room'] = ($value == true || $value == 'on' ? 1 : 0);
    }

    public function setViewOfTheSeaAttribute($value)
    {
        $this->attributes['view_of_the_sea'] = ($value == true || $value == 'on' ? 1 : 0);
    }

    public function setSlug()
    {
        // Se tiver um título, vamos setar o slug concatenado do id
        if(!empty($this->title)){
            $this->attributes['slug'] = Str::slug($this->title) . "-" . $this->id;
            $this->save();
        }
    }

    private function ConvertStringToDouble(?string $param)
    {
        /** Se vazio, devolver null */
        if(empty($param)){
            return null;
        }
        return str_replace(',','.',str_replace('.', '', $param));
    }

    public function getPtType()
    {
        if(!empty($this->type)){
            switch ($this->type) {
            case 'home':
                $pt_type = 'Casa';
                break;
            case 'roof':
                $pt_type = 'Cobertura';
                break;
            case 'apartment':
                $pt_type = 'Apartamento';
                break;
            case 'studio':
                $pt_type = 'Studio';
                break;
            case 'kitnet':
                $pt_type = 'Kitnet';
                break;
            case 'commercial_room':
                $pt_type = 'Sala Comercial';
                break;
            case 'deposit_shed':
                $pt_type = 'Depósito/Galpão';
                break;
            case 'commercial_point':
                $pt_type = 'Ponto Comercial';
                break;
            case 'terrain':
                $pt_type = 'Terreno';
                break;
            } 
        } else {
            $pt_type = null;
        }

        return $pt_type;
    }

    public function getPtCategory()
    {
        if(!empty($this->category)){
            switch ($this->category) {
            case 'residential_property':
                $pt_category = 'Imóvel Residencial';
                break;
            case 'commercial_industrial':
                $pt_category = 'Comercial/Industrial';
                break;
            case 'terrain':
                $pt_category = 'Terreno';
                break;
            }
        } else {
           $pt_category = null; 
        }
        return $pt_category;
    }

    /** Função que limpa os caracteres indesejados */
    private function clearField(?string $param)
    {
        /** Verifica se o o parâmetro está vazio */
        if(empty($param)){
            return ''; // Devolve vazio
        }
        /** Faz os replaces necessários */
        return str_replace(['.',',','-','/','(',')',' ','\\'], '', $param);
    }

}
