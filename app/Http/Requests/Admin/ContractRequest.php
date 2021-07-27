<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ContractRequest extends FormRequest
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
            'purpose' => 'requiered',
            'owner' => 'required',
            'acquirer' => 'required|different:owner', // Usuário diferente de owner
            'sale_price' => 'required_if:sale,on', // Só é válido caso o checkbox esteja on
            'rent_price' => 'required_if:rent,on', // Só é válido caso o checkbox esteja on
            'property' => 'required|integer',
            'due_date' => 'required|integer|min:1|max:28',
            'deadline' => 'required|integer|min:6|max:48',
            'start_at' => 'required'
        ];
    }
}
