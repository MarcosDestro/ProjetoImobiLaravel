<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user' => 'required',
            'social_name' => 'required|min:5|max:191',
            'alias_name' => 'required',
            'document_company' => 'required',
            'document_company_secondary' => 'required',
            'zipcode' => 'required|min:8|max:9',
            'street' => 'required',
            'number' => 'required',
            // 'complement' => 'required',
            'neighborhood' => 'required',
            'state' => 'required',
            'city' => 'required',
        ];
    }

    public function messages(){
        return [
            'user.required' => 'O campo responsável legal é obrigatório.'
        ];
    }
}
