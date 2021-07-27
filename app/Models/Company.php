<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user',
        'social_name',
        'alias_name',
        'document_company',
        'document_company_secondary',
        'zipcode',
        'street',
        'number',
        'complement',
        'neighborhood',
        'state',
        'city',
    ];

    // Relação de usuário que possui essa empresa
    public function owner()
    {
        // Classe de relação, coluna externa, coluna local (chave)
        return $this->hasOne(User::class, 'id', 'user');
    }

    public function setDocumentCompanyAttribute($value)
    {
        $this->attributes['document_company'] = $this->clearField($value);
    }

    // Voltando a máscara de cnpj
    public function getDocumentCompanyAttribute($value)
    {
        return substr($value, 0, 2) .
        '.' . substr($value, 2, 3) .
        '.' . substr($value, 5, 3) .
        '.' . substr($value, 8, 4) . 
        '-' . substr($value, 12, 2);
    }

    public function setZipcodeAttribute($value)
    {
        $this->attributes['zipcode'] = $this->clearField($value);
    }

    public function getZipcodeAttribute($value)
    {
        return substr($value, 0, 5) . '-' . substr($value, 5, 3);
    }

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
