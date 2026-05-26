<?php

namespace App\Http\Requests;

use App\Models\ContaPagarReceber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContaPagarReceberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo' => ['required', Rule::in([ContaPagarReceber::TIPO_PAGAR, ContaPagarReceber::TIPO_RECEBER])],
            'titulo' => ['required', 'string', 'max:150'],
            'valor' => ['required', 'numeric', 'min:0.01'],
            'data' => ['required', 'date'],
            'quantidade_parcelas' => ['required', 'integer', 'between:1,48'],
            'categoria_id' => [
                'nullable',
                'integer',
                Rule::exists('categorias', 'id')->where('user_id', $this->user()->id),
            ],
            'descricao' => ['nullable', 'string', 'max:500'],
        ];
    }
}
