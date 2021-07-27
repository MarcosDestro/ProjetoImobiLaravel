<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /**
         * Valida se o usuário está logado
         * Só logados tem autorização para submeter
         */
        return Auth::check();
    }

    public function all($keys = null)
    {
        // Executa a função passando outra função all do pai
        return $this->validateFields(parent::all());
    }

    public function validateFields(array $inputs)
    {
        // Ao consultar o input document, retire . e - desta requisição
        $inputs['document'] = str_replace(['.', '-'], '' ,$this->request->all()['document']);
        return $inputs;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:3|max:191|string',
            'genre' => 'required|in:male,female,other',

            // Exceção: Se recebido o id, então estamos atualizando o usuário
            'document' => (!empty($this->request->all()['id']) ?
                'required|min:11|max:14|unique:users,document,' . $this->request->all()['id'] : // Marcamos o id de exceção
                'required|min:11|max:14|unique:users,document'),
            
            'document_secondary' => 'required|min:8|max:12',
            'document_secondary_complement' => 'required',
            'date_of_birth' => 'required|date_format:d/m/Y',
            'place_of_birth' => 'required',
            'civil_status' => 'required|in:married,separated,single,divorced,widower',
            'cover' => 'image',

            // Income
            'occupation' => 'required',
            'income' => 'required',
            'company_work' => 'required',

            // Address
            'zipcode' => 'required|min:8|max:9',
            'street' => 'required',
            'number' => 'required',
            //'complement' => 'required',
            'neighborhood' => 'required',
            'state' => 'required',
            'city' => 'required',

            // Contact
            'cell' => 'required',

            // Access
            // Exceção: Se recebido o id, então estamos atualizando o usuário
            'email' => (!empty($this->request->all()['id']) ?
                'required|email|unique:users,email,' . $this->request->all()['id'] : // Registro id de exceção
                'required|email|unique:users,email'),

            // Spouse
            /** Aqui todos os requireds tem if, pois o cliuente pode ou não ter conjuge */
            'type_of_communion' => 'required_if:civil_status,married,separated|in:Comunhão Universal de Bens,Comunhão Parcial de Bens,Separação Total de Bens,Participação Final de Aquestos',
            'spouse_name' => 'required_if:civil_status,married,separated|min:3|max:191|string',
            'spouse_genre' => 'required_if:civil_status,married,separated|in:male,female,other',
            'spouse_document' => 'required_if:civil_status,married,separated|min:11|max:14',
            'spouse_document_secondary' => 'required_if:civil_status,married,separated|min:8|max:12',
            'spouse_document_secondary_complement' => 'required_if:civil_status,married,separated',
            'spouse_date_of_birth' => 'required_if:civil_status,married,separated|date_format:d/m/Y',
            'spouse_place_of_birth' => 'required_if:civil_status,married,separated',
            'spouse_occupation' => 'required_if:civil_status,married,separated',
            'spouse_income' => 'required_if:civil_status,married,separated',
            'spouse_company_work' => 'required_if:civil_status,married,separated',
        ];
    }
}
