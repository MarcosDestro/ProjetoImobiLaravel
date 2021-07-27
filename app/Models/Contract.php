<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'purpose',
        'owner',
        'owner_spouse',
        'owner_company',
        'acquirer',
        'acquirer_spouse',
        'acquirer_company',
        'property',
        'sale_price',
        'rent_price',
        'price',
        'tribute',
        'condominium',
        'due_date',
        'deadline',
        'start_at',
        'status'
    ];

    public function ownerRelation()
    {
        // Classe de relação, coluna externa, coluna interna
        return $this->hasOne(User::class, 'id', 'owner');

        /** Funciona, mas não é o certo */
        //return $this->belongsTo(User::class, 'owner', 'id');
    }

    public function propertyRelation(){
        // Classe de relação, coluna externa, coluna interna
        return $this->hasOne(Property::class, 'id', 'property');
    }

    public function ownerCompanyRelation(){
        // Classe de relação, coluna externa, coluna interna
        return $this->hasOne(Company::class, 'id', 'owner_company');
    }

    public function acquirerRelation()
    {
        // Classe de relação, coluna externa, coluna interna
        return $this->hasOne(User::class, 'id', 'acquirer');
        
        /** Funciona, mas não é o certo */
        //return $this->belongsTo(User::class, 'acquirer', 'id');
    }

    public function acquirerCompanyRelation(){
        // Classe de relação, coluna externa, coluna interna
        return $this->hasOne(Company::class, 'id', 'acquirer_company');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCanceled($query)
    {
        return $query->where('status', 'canceled');
    }

    /**
     * Accessor e Mutators
     */
    public function setPurposeAttribute($value)
    {
        if($value == 'sale'){
            $this->attributes['purpose'] = 'sale';
        } else {
            $this->attributes['purpose'] = 'rent';
        }
    }

    public function setOwnerSpouseAttribute($value)
    {
        $this->attributes['owner_spouse'] = ($value == '1' ? 1 : 0 );
    }

    public function setOwnerCompanyAttribute($value)
    {
        $this->attributes['owner_company'] = ($value == '0' ? null : $value);
        // if($value == '0'){
        //     $this->attributes['owner_company'] = null;
        // } else {
        //     $this->attributes['owner_company'] = $value;
        // }
    }

    public function setAcquirerSpouseAttribute($value)
    {
        $this->attributes['acquirer_spouse'] = ($value == '1' ? 1 : 0 );
    }

    public function setAcquirerCompanyAttribute($value)
    {
        $this->attributes['acquirer_company'] = ($value == '0' ? null : $value);
    }

    public function getPriceAttribute($value)
    {
        return number_format($value, 2, ',', '.');
    }

    public function setSalePriceAttribute($value)
    {
        if(!empty($value)){
            $this->attributes['price'] = floatval($this->ConvertStringToDouble($value));
        }
    }

    public function setRentPriceAttribute($value)
    {
        if(!empty($value)){
            $this->attributes['price'] = floatval($this->ConvertStringToDouble($value));
        }
    }

    public function getTributeAttribute($value)
    {
        return number_format($value, 2, ',', '.');
    }

    public function setTributeAttribute($value)
    {
        if(!empty($value)){
            $this->attributes['tribute'] = floatval($this->ConvertStringToDouble($value));
        }
    }

    public function getCondominiumAttribute($value)
    {
        return number_format($value, 2, ',', '.');
    }

    public function setCondominiumAttribute($value)
    {
        if(!empty($value)){
            $this->attributes['condominium'] = floatval($this->ConvertStringToDouble($value));
        }
    }
    
    public function getStartAtAttribute($value)
    {
        return date('d/m/Y', strtotime($value));
    }

    public function setStartAtAttribute($value)
    {
        if(!empty($value)){
            $this->attributes['start_at'] = $this->ConvertStringToDate($value);
        }
    }


    /**
     * Funções Auxiliares
     */

    private function ConvertStringToDouble(?string $param)
    {
        /** Se vazio, devolver null */
        if(empty($param)){
            return null;
        }
        return str_replace(',','.',str_replace('.', '', $param));
    }

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
        // return date('Y-m-d', strtotime($year . "-" . $month . "-" . $day)); // Outra forma
    }

    public function terms()
    {
        
        // Finalidade [Venda/Locação]
        if ($this->purpose == 'sale') {
            $parameters = [
                'purpouse' => 'VENDA',
                'part' => 'VENDEDOR',
                'part_opposite' => 'COMPRADOR',
            ];
        }

        if ($this->purpose == 'rent') {
            $parameters = [
                'purpouse' => 'LOCAÇÃO',
                'part' => 'LOCADOR',
                'part_opposite' => 'LOCATÁRIO',
            ];
        }

        $terms[] = "<p style='text-align: center;'>{$this->id} - CONTRATO DE {$parameters['purpouse']} DE IMÓVEL</p>";
        
        // OWNER
        if (!empty($this->owner_company)) { // Se tem empresa

            if (!empty($this->owner_spouse)) { // E tem conjuge
                $terms[] = "<p><b>1. {$parameters['part']}: {$this->ownerCompanyRelation->social_name}</b>, inscrito sob C. N. P. J. nº {$this->ownerCompanyRelation->document_company} e I. E. nº {$this->ownerCompanyRelation->document_company_secondary} exercendo suas atividades no endereço {$this->ownerCompanyRelation->street}, nº {$this->ownerCompanyRelation->number}, {$this->ownerCompanyRelation->complement}, {$this->ownerCompanyRelation->neighborhood}, {$this->ownerCompanyRelation->city}/{$this->ownerCompanyRelation->state}, CEP {$this->ownerCompanyRelation->zipcode} tendo como responsável legal {$this->ownerRelation->name}, natural de {$this->ownerRelation->place_of_birth}, {$this->ownerRelation->getCivilStatusTranslateAttribute($this->ownerRelation->civil_status, $this->ownerRelation->genre)}, {$this->ownerRelation->occupation}, portador da cédula de identidade R. G. nº {$this->ownerRelation->document_secondary} {$this->ownerRelation->document_secondary_complement}, e inscrição no C. P. F. nº {$this->ownerRelation->document}, e cônjuge {$this->ownerRelation->spouse_name}, natural de {$this->ownerRelation->spouse_place_of_birth}, {$this->ownerRelation->spouse_occupation}, portador da cédula de identidade R. G. nº {$this->ownerRelation->spouse_document_secondary} {$this->ownerRelation->spouse_document_secondary_complement}, e inscrição no C. P. F. nº {$this->ownerRelation->spouse_document}, residentes e domiciliados à {$this->ownerRelation->street}, nº {$this->ownerRelation->number}, {$this->ownerRelation->complement}, {$this->ownerRelation->neighborhood}, {$this->ownerRelation->city}/{$this->ownerRelation->state}, CEP {$this->ownerRelation->zipcode}.</p>";
            } else { // E não tem conjuge
                $terms[] = "<p><b>1. {$parameters['part']}: {$this->ownerCompanyRelation->social_name}</b>, inscrito sob C. N. P. J. nº {$this->ownerCompanyRelation->document_company} e I. E. nº {$this->ownerCompanyRelation->document_company_secondary} exercendo suas atividades no endereço {$this->ownerCompanyRelation->street}, nº {$this->ownerCompanyRelation->number}, {$this->ownerCompanyRelation->complement}, {$this->ownerCompanyRelation->neighborhood}, {$this->ownerCompanyRelation->city}/{$this->ownerCompanyRelation->state}, CEP {$this->ownerCompanyRelation->zipcode} tendo como responsável legal {$this->ownerRelation->name}, natural de {$this->ownerRelation->place_of_birth}, {$this->ownerRelation->getCivilStatusTranslateAttribute($this->ownerRelation->civil_status, $this->ownerRelation->genre)}, {$this->ownerRelation->occupation}, portador da cédula de identidade R. G. nº {$this->ownerRelation->document_secondary} {$this->ownerRelation->document_secondary_complement}, e inscrição no C. P. F. nº {$this->ownerRelation->document}, residente e domiciliado à {$this->ownerRelation->street}, nº {$this->ownerRelation->number}, {$this->ownerRelation->complement}, {$this->ownerRelation->neighborhood}, {$this->ownerRelation->city}/{$this->ownerRelation->state}, CEP {$this->ownerRelation->zipcode}.</p>";
            }

        } else { // Se não tem empresa

            if (!empty($this->owner_spouse)) { // E tem conjuge
                $terms[] = "<p><b>1. {$parameters['part']}: {$this->ownerRelation->name}</b>, natural de {$this->ownerRelation->place_of_birth}, {$this->ownerRelation->getCivilStatusTranslateAttribute($this->ownerRelation->civil_status, $this->ownerRelation->genre)}}, {$this->ownerRelation->occupation}, portador da cédula de identidade R. G. nº {$this->ownerRelation->document_secondary} {$this->ownerRelation->document_secondary_complement}, e inscrição no C. P. F. nº {$this->ownerRelation->document}, e cônjuge {$this->ownerRelation->spouse_name}, natural de {$this->ownerRelation->spouse_place_of_birth}, {$this->ownerRelation->spouse_occupation}, portador da cédula de identidade R. G. nº {$this->ownerRelation->spouse_document_secondary} {$this->ownerRelation->spouse_document_secondary_complement}, e inscrição no C. P. F. nº {$this->ownerRelation->spouse_document}, residentes e domiciliados à {$this->ownerRelation->street}, nº {$this->ownerRelation->number}, {$this->ownerRelation->complement}, {$this->ownerRelation->neighborhood}, {$this->ownerRelation->city}/{$this->ownerRelation->state}, CEP {$this->ownerRelation->zipcode}.</p>";
            } else { // E não tem conjuge
                $terms[] = "<p><b>1. {$parameters['part']}: {$this->ownerRelation->name}</b>, natural de {$this->ownerRelation->place_of_birth}, {$this->ownerRelation->getCivilStatusTranslateAttribute($this->ownerRelation->civil_status, $this->ownerRelation->genre)}, {$this->ownerRelation->occupation}, portador da cédula de identidade R. G. nº {$this->ownerRelation->document_secondary} {$this->ownerRelation->document_secondary_complement}, e inscrição no C. P. F. nº {$this->ownerRelation->document}, residente e domiciliado à {$this->ownerRelation->street}, nº {$this->ownerRelation->number}, {$this->ownerRelation->complement}, {$this->ownerRelation->neighborhood}, {$this->ownerRelation->city}/{$this->ownerRelation->state}, CEP {$this->ownerRelation->zipcode}.</p>";
            }

        }

        // ACQUIRER
        if (!empty($this->acquirer_company)) { // Se tem empresa

            if (!empty($this->acquirer_spouse)) { // E tem conjuge
                $terms[] = "<p><b>2. {$parameters['part_opposite']}: {$this->acquirerCompanyRelation->social_name}</b>, inscrito sob C. N. P. J. nº {$this->acquirerCompanyRelation->document_company} e I. E. nº {$this->acquirerCompanyRelation->document_company_secondary} exercendo suas atividades no endereço {$this->acquirerCompanyRelation->street}, nº {$this->acquirerCompanyRelation->number}, {$this->acquirerCompanyRelation->complement}, {$this->acquirerCompanyRelation->neighborhood}, {$this->acquirerCompanyRelation->city}/{$this->acquirerCompanyRelation->state}, CEP {$this->acquirerCompanyRelation->zipcode} tendo como responsável legal {$this->acquirerRelation->name}, natural de {$this->acquirerRelation->place_of_birth}, {$this->acquirerRelation->getCivilStatusTranslateAttribute($this->acquirerRelation->civil_status, $this->acquirerRelation->genre)}, {$this->acquirerRelation->occupation}, portador da cédula de identidade R. G. nº {$this->acquirerRelation->document_secondary} {$this->acquirerRelation->document_secondary_complement}, e inscrição no C. P. F. nº {$this->acquirerRelation->document}, e cônjuge {$this->acquirerRelation->spouse_name}, natural de {$this->acquirerRelation->spouse_place_of_birth}, {$this->acquirerRelation->spouse_occupation}, portador da cédula de identidade R. G. nº {$this->acquirerRelation->spouse_document_secondary} {$this->acquirerRelation->spouse_document_secondary_complement}, e inscrição no C. P. F. nº {$this->acquirerRelation->spouse_document}, residentes e domiciliados à {$this->acquirerRelation->street}, nº {$this->acquirerRelation->number}, {$this->acquirerRelation->complement}, {$this->acquirerRelation->neighborhood}, {$this->acquirerRelation->city}/{$this->acquirerRelation->state}, CEP {$this->acquirerRelation->zipcode}.</p>";
            } else { // E não tem conjuge
                $terms[] = "<p><b>2. {$parameters['part_opposite']}: {$this->acquirerCompanyRelation->social_name}</b>, inscrito sob C. N. P. J. nº {$this->acquirerCompanyRelation->document_company} e I. E. nº {$this->acquirerCompanyRelation->document_company_secondary} exercendo suas atividades no endereço {$this->acquirerCompanyRelation->street}, nº {$this->acquirerCompanyRelation->number}, {$this->acquirerCompanyRelation->complement}, {$this->acquirerCompanyRelation->neighborhood}, {$this->acquirerCompanyRelation->city}/{$this->acquirerCompanyRelation->state}, CEP {$this->acquirerCompanyRelation->zipcode} tendo como responsável legal {$this->acquirerRelation->name}, natural de {$this->acquirerRelation->place_of_birth}, {$this->acquirerRelation->getCivilStatusTranslateAttribute($this->acquirerRelation->civil_status, $this->acquirerRelation->genre)}, {$this->acquirerRelation->occupation}, portador da cédula de identidade R. G. nº {$this->acquirerRelation->document_secondary} {$this->acquirerRelation->document_secondary_complement}, e inscrição no C. P. F. nº {$this->acquirerRelation->document}, residente e domiciliado à {$this->acquirerRelation->street}, nº {$this->acquirerRelation->number}, {$this->acquirerRelation->complement}, {$this->acquirerRelation->neighborhood}, {$this->acquirerRelation->city}/{$this->acquirerRelation->state}, CEP {$this->acquirerRelation->zipcode}.</p>";
            }

        } else { // Se não tem empresa

            if (!empty($this->acquirer_spouse)) { // E tem conjuge
                $terms[] = "<p><b>2. {$parameters['part_opposite']}: {$this->acquirerRelation->name}</b>, natural de {$this->acquirerRelation->place_of_birth}, {$this->acquirerRelation->getCivilStatusTranslateAttribute($this->acquirerRelation->civil_status, $this->acquirerRelation->genre)}, {$this->acquirerRelation->occupation}, portador da cédula de identidade R. G. nº {$this->acquirerRelation->document_secondary} {$this->acquirerRelation->document_secondary_complement}, e inscrição no C. P. F. nº {$this->acquirerRelation->document}, e cônjuge {$this->acquirerRelation->spouse_name}, natural de {$this->acquirerRelation->spouse_place_of_birth}, {$this->acquirerRelation->spouse_occupation}, portador da cédula de identidade R. G. nº {$this->acquirerRelation->spouse_document_secondary} {$this->acquirerRelation->spouse_document_secondary_complement}, e inscrição no C. P. F. nº {$this->acquirerRelation->spouse_document}, residentes e domiciliados à {$this->acquirerRelation->street}, nº {$this->acquirerRelation->number}, {$this->acquirerRelation->complement}, {$this->acquirerRelation->neighborhood}, {$this->acquirerRelation->city}/{$this->acquirerRelation->state}, CEP {$this->acquirerRelation->zipcode}.</p>";
            } else { // E não tem conjuge
                $terms[] = "<p><b>2. {$parameters['part_opposite']}: {$this->acquirerRelation->name}</b>, natural de {$this->acquirerRelation->place_of_birth}, {$this->acquirerRelation->getCivilStatusTranslateAttribute($this->acquirerRelation->civil_status, $this->acquirerRelation->genre)}, {$this->acquirerRelation->occupation}, portador da cédula de identidade R. G. nº {$this->acquirerRelation->document_secondary} {$this->acquirerRelation->document_secondary_complement}, e inscrição no C. P. F. nº {$this->acquirerRelation->document}, residente e domiciliado à {$this->acquirerRelation->street}, nº {$this->acquirerRelation->number}, {$this->acquirerRelation->complement}, {$this->acquirerRelation->neighborhood}, {$this->acquirerRelation->city}/{$this->acquirerRelation->state}, CEP {$this->acquirerRelation->zipcode}.</p>";
            }

        }

        $terms[] = "<p style='font-style: italic; font-size: 0.875em;'>A falsidade dessa declaração configura crime previsto no Código Penal Brasileiro, e passível de apuração na forma da Lei.</p>";

        $terms[] = "<p><b>5. IMÓVEL:</b> {$this->propertyRelation->category}, {$this->propertyRelation->type}, localizada no endereço {$this->propertyRelation->street}, nº {$this->propertyRelation->number}, {$this->propertyRelation->complement}, {$this->propertyRelation->neighborhood}, {$this->propertyRelation->city}/{$this->propertyRelation->state}, CEP {$this->propertyRelation->zipcode}</p>";

        $terms[] = "<p><b>6. PERÍODO:</b> {$this->deadline} meses</p>";

        $terms[] = "<p><b>7. VIGÊNCIA:</b> O presente contrato tem como data de início {$this->start_at} e o término exatamente após a quantidade de meses descrito no item 6 deste.</p>";

        $terms[] = "<p><b>8. VENCIMENTO:</b> Fica estipulado o vencimento no dia {$this->due_date} do mês posterior ao do início de vigência do presente contrato.</p>";

        $terms[] = "<p>Florianópolis, " . date('d/m/Y') . ".</p>";

        $terms[] = "
            <table width='100%' style='margin-top: 50px;'>
                <tr>
                    <td>_________________________</td>
                    " . ($this->owner_spouse ? "<td>_________________________</td>" : "") . "
                </tr>
                <tr>
                    <td>{$parameters['part']}: {$this->ownerRelation->name}</td>
                    " . ($this->owner_spouse ? "<td>Conjuge: {$this->ownerRelation->spouse_name}</td>" : "") . "
                </tr>
                <tr>
                    <td>Documento: {$this->ownerRelation->document}</td>
                    " . ($this->owner_spouse ? "<td>Documento: {$this->ownerRelation->spouse_document}</td>" : "") . "
                </tr>
            </table>";


        $terms[] = "
            <table width='100%' style='margin-top: 50px;'>
                <tr>
                    <td>_________________________</td>
                    " . ($this->acquirer_spouse ? "<td>_________________________</td>" : "") . "
                </tr>
                <tr>
                    <td>{$parameters['part_opposite']}: {$this->acquirerRelation->name}</td>
                    " . ($this->acquirer_spouse ? "<td>Conjuge: {$this->acquirerRelation->spouse_name}</td>" : "") . "
                </tr>
                <tr>
                    <td>Documento: {$this->acquirerRelation->document}</td>
                    " . ($this->acquirer_spouse ? "<td>Documento: {$this->acquirerRelation->spouse_document}</td>" : "") . "
                </tr>
            </table>";

        $terms[] = "
            <table width='100%' style='margin-top: 50px;'>
                <tr>
                    <td>_________________________</td>
                    <td>_________________________</td>
                </tr>
                <tr>
                    <td>1ª Testemunha: </td>
                    <td>2ª Testemunha: </td>
                </tr>
                <tr>
                    <td>Documento: </td>
                    <td>Documento: </td>
                </tr>
            </table>";

        return implode('', $terms);
        
    }


}
