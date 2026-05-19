<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDespesaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:150'],
            'valor' => ['required', 'numeric', 'min:0.01'],
            'data' => ['required', 'date'],
            'categoria_id' => [
                'nullable',
                'integer',
                Rule::exists('categorias', 'id')->where('user_id', $this->user()->id),
            ],
            'descricao' => ['nullable', 'string', 'max:500'],
        ];
    }
}
