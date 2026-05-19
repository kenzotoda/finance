<?php

namespace App\Http\Requests;

use App\Models\Imposto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateImpostoRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $periodicidade = $this->input('periodicidade');
        $diaVencimento = $this->filled('dia_vencimento') ? (int) $this->input('dia_vencimento') : null;
        $renovacaoMes = $this->filled('renovacao_mes') ? (int) $this->input('renovacao_mes') : null;
        $renovacaoAno = $this->filled('renovacao_ano') ? (int) $this->input('renovacao_ano') : null;

        $this->merge([
            'dia_vencimento' => $periodicidade === Imposto::PERIODICIDADE_ANUAL ? 1 : $diaVencimento,
            'renovacao_mes' => $periodicidade === Imposto::PERIODICIDADE_ANUAL ? $renovacaoMes : null,
            'renovacao_ano' => $periodicidade === Imposto::PERIODICIDADE_ANUAL ? $renovacaoAno : null,
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo' => ['required', Rule::in(array_keys(Imposto::TIPOS))],
            'titulo' => ['required', 'string', 'max:150'],
            'valor' => ['required', 'numeric', 'min:0.01'],
            'dia_vencimento' => ['required_if:periodicidade,mensal', 'nullable', 'integer', 'between:1,31'],
            'periodicidade' => ['required', Rule::in([Imposto::PERIODICIDADE_MENSAL, Imposto::PERIODICIDADE_ANUAL])],
            'renovacao_mes' => ['required_if:periodicidade,anual', 'nullable', 'integer', 'between:1,12'],
            'renovacao_ano' => ['required_if:periodicidade,anual', 'nullable', 'integer', 'digits:4', 'min:2000'],
            'categoria_id' => [
                'nullable',
                'integer',
                Rule::exists('categorias', 'id')->where('user_id', $this->user()->id),
            ],
            'descricao' => ['nullable', 'string', 'max:500'],
            'ativa' => ['nullable', 'boolean'],
        ];
    }
}
