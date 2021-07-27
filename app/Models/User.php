<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Support\Cropper;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'genre',
        'document',
        'document_secondary',
        'document_secondary_complement',
        'date_of_birth',
        'place_of_birth',
        'civil_status',
        'cover',
        'occupation',
        'income',
        'company_work',
        'zipcode',
        'street',
        'number',
        'complement',
        'neighborhood',
        'state',
        'city',
        'telephone',
        'cell',
        'type_of_communion',
        'spouse_name',
        'spouse_genre',
        'spouse_document',
        'spouse_document_secondary',
        'spouse_document_secondary_complement',
        'spouse_date_of_birth',
        'spouse_place_of_birth',
        'spouse_occupation',
        'spouse_income',
        'spouse_company_work',
        'lessor',
        'lessee',
        'admin',
        'client'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relacionamento de todas as empresas que esse usuário possui
    public function companies()
    {
        // Classe de relação, coluna extrangeira de referencia, coluna local de referencia
        return $this->hasMany(Company::class, 'user', 'id');
    }

    public function contractsAsAcquirer()
    {
        return $this->hasMany(Contract::class, 'acquirer', 'id');
    }

    public function scopeLessors($query)
    {
        return $query->where('lessor', true);
    }

    public function scopeLessees($query)
    {
        return $query->where('lessee', true);
    }

    public function properties()
    {
        // Classe de relação, coluna extrangeira de referencia, coluna local de referencia
        return $this->hasMany(Property::class, 'user', 'id');
    }

    public function getUrlCoverAttribute($value)
    {
        // Se o cover não estiver vazio, execute o cropper
        if(!empty($this->cover)){
            return Storage::url(Cropper::thumb($this->cover, 500, 500));
        }
        // Senão vazio
        return '';
    }

    // Accessor
    public function setLesseeAttribute($value)
    {
        // Se $value for true ou on, setar como 1, caso contrario 0
        $this->attributes['lessee'] = ($value === true || $value === 'on' ? 1 : 0 );
    }

    public function setLessorAttribute($value)
    {
        // Se $value for true ou on, setar como 1, caso contrario 0
        $this->attributes['lessor'] = ($value === true || $value === 'on' ? 1 : 0 );
    }

    public function setDocumentAttribute($value)
    {
        $this->attributes['document'] = $this->clearField($value);
    }

    public function getDocumentAttribute($value)
    {
        return substr($value, 0, 3) . "." . substr($value, 3, 3) . "." . substr($value, 6, 3) . "-" . substr($value, 9, 2);
    }

    public function setDateOfBirthAttribute($value)
    {
        $this->attributes['date_of_birth'] = $this->ConvertStringToDate($value);
    }

    public function getDateOfBirthAttribute($value)
    {
        return date('d/m/Y', strtotime($value));
    }

    public function setIncomeAttribute($value)
    {
        $this->attributes['income'] = floatval($this->ConvertStringToDouble($value));
    }

    public function getIncomeAttribute($value)
    {
        return number_format($value, 2, ',', '.');
    }

    public function setZipcodeAttribute($value)
    {
        $this->attributes['zipcode'] = $this->clearField($value);
    }

    public function getZipcodeAttribute($value)
    {
        return substr($value, 0, 5) . '-' . substr($value, 5, 3);
    }

    public function setTelephoneAttribute($value)
    {
        $this->attributes['telephone'] = $this->clearField($value);
    }

    public function setCellAttribute($value)
    {
        $this->attributes['cell'] = $this->clearField($value);
    }

    /**
    * Ao editar qualquer campo do usuário, a senha também é alterada impossibilitando efetuar um novo login.
    * Solução: Se o input for vazio, remove a posição da atualização com o unset.
    */
    public function setPasswordAttribute($value)
    {
        if (empty($value)) {
            unset($this->attributes['password']);
            return ;
        }

        $this->attributes['password'] = bcrypt($value);
    }

    public function setSpouseDocumentAttribute($value)
    {
        $this->attributes['spouse_document'] = $this->clearField($value);
    }

    public function getSpouseDocumentAttribute($value)
    {
        return substr($value, 0, 3) . "." . substr($value, 3, 3) . "." . substr($value, 6, 3) . "-" . substr($value, 9, 2);
    }

    public function setSpouseDateOfBirthAttribute($value)
    {
        $this->attributes['spouse_date_of_birth'] = $this->ConvertStringToDate($value);
    }

    public function getSpouseDateOfBirthAttribute($value)
    {
        return date('d/m/Y', strtotime($value));
    }

    public function setSpouseIncomeAttribute($value)
    {
        $this->attributes['spouse_income'] = floatval($this->ConvertStringToDouble($value));
    }

    public function getSpouseIncomeAttribute($value)
    {
        return number_format($value, 2, ',', '.');
    }

    public function setAdminAttribute($value)
    {
        // Se $value for true ou on, setar como 1, caso contrario 0
        $this->attributes['admin'] = ($value === true || $value === 'on' ? 1 : 0 );
    }

    public function setClientAttribute($value)
    {
        // Se $value for true ou on, setar como 1, caso contrario 0
        $this->attributes['client'] = ($value === true || $value === 'on' ? 1 : 0 );
    }

    public function getCivilStatusTranslateAttribute(string $status, string $genre)
    {
        if($genre == 'female'){
            if($status == 'married'){
                return 'casada';
            } elseif ($status == 'separated'){
                return 'separada';
            } elseif ($status == 'single'){
                return 'solteira';
            } elseif ($status == 'divorced'){
                return 'divorciada';
            } elseif ($status == 'widower'){
                return 'viúva';
            } else {
                return '';
            }
        } else {
            if($status == 'married'){
                return 'casado';
            } elseif ($status == 'separated'){
                return 'separado';
            } elseif ($status == 'single'){
                return 'solteiro';
            } elseif ($status == 'divorced'){
                return 'divorciado';
            } elseif ($status == 'widower'){
                return 'viúvo';
            } else {
                return '';
            }
        }
    }

    /**
     * Funções auxiliares
     */
    /** Converte de moeda - Olhar properties */
    private function ConvertStringToDouble(?string $param)
    {
        /** Se vazio, devolver null */
        if(empty($param)){
            return null;
        }
        return str_replace(',','.',str_replace('.', '', $param));
    }

    /** Converte para data do banco */
    private function ConvertStringToDate(?string $param)
    {
        /** Se vazio, devolver null */
        if(empty($param)){
            return null;
        }
        // Explode uma lista, recebendo os valores separados por barra
        list($day, $month, $year) = explode('/', $param);
        // Retorna a data conforme armazenada no banco
        return (new \DateTime($year . "-" . $month . "-" . $day))->format('Y-m-d');
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

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

}
