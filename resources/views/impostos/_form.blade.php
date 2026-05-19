<div class="space-y-4" x-data="{ periodicidade: '{{ old('periodicidade', $imposto->periodicidade ?? 'anual') }}' }">
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <x-input-label for="tipo" value="Tipo de imposto" />
            <select id="tipo" name="tipo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                <option value="">Selecione</option>
                @foreach ($tiposImposto as $valor => $label)
                    <option value="{{ $valor }}" @selected(old('tipo', $imposto->tipo ?? '') === $valor)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="titulo" value="Titulo / Descricao curta" />
            <x-text-input id="titulo" name="titulo" type="text" class="mt-1 block w-full" value="{{ old('titulo', $imposto->titulo ?? '') }}" placeholder="Ex.: IPTU apartamento" required />
            <x-input-error :messages="$errors->get('titulo')" class="mt-2" />
        </div>
    </div>
    <div class="grid gap-4 md:grid-cols-4">
        <div>
            <x-input-label for="valor" value="Valor" />
            <x-text-input id="valor" name="valor" type="number" step="0.01" min="0" class="mt-1 block w-full" value="{{ old('valor', $imposto->valor ?? '') }}" required />
            <x-input-error :messages="$errors->get('valor')" class="mt-2" />
        </div>
        <div x-show="periodicidade === 'mensal'" x-cloak>
            <x-input-label for="dia_vencimento" value="Dia do vencimento" />
            <x-text-input id="dia_vencimento" name="dia_vencimento" x-bind:disabled="periodicidade !== 'mensal'" type="number" min="1" max="31" class="mt-1 block w-full" value="{{ old('dia_vencimento', $imposto->dia_vencimento ?? 1) }}" />
            <x-input-error :messages="$errors->get('dia_vencimento')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="periodicidade" value="Periodicidade" />
            <select id="periodicidade" name="periodicidade" x-model="periodicidade" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                <option value="mensal" @selected(old('periodicidade', $imposto->periodicidade ?? 'anual') === 'mensal')>Mensal</option>
                <option value="anual" @selected(old('periodicidade', $imposto->periodicidade ?? 'anual') === 'anual')>Anual</option>
            </select>
            <x-input-error :messages="$errors->get('periodicidade')" class="mt-2" />
        </div>
        <div x-show="periodicidade === 'anual'" x-cloak>
            <x-input-label for="renovacao_mes" value="Mes do vencimento" />
            <select id="renovacao_mes" name="renovacao_mes" x-bind:disabled="periodicidade !== 'anual'" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                <option value="">Selecione</option>
                @foreach ([1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Marco', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'] as $numeroMes => $nomeMes)
                    <option value="{{ $numeroMes }}" @selected((string) old('renovacao_mes', $imposto->renovacao_mes ?? '') === (string) $numeroMes)>
                        {{ $nomeMes }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('renovacao_mes')" class="mt-2" />
        </div>
        <div x-show="periodicidade === 'anual'" x-cloak>
            <x-input-label for="renovacao_ano" value="Ano de referencia" />
            <x-text-input id="renovacao_ano" name="renovacao_ano" x-bind:disabled="periodicidade !== 'anual'" type="number" min="2000" max="9999" class="mt-1 block w-full" value="{{ old('renovacao_ano', $imposto->renovacao_ano ?? now()->year) }}" />
            <x-input-error :messages="$errors->get('renovacao_ano')" class="mt-2" />
        </div>
        <input type="hidden" name="dia_vencimento" value="1" x-bind:disabled="periodicidade !== 'anual'">
        <div class="flex items-end">
            <label class="inline-flex items-center gap-2 rounded-md border border-gray-300 px-3 py-2 text-sm">
                <input type="checkbox" name="ativa" value="1" @checked(old('ativa', $imposto->ativa ?? true)) class="rounded border-gray-300">
                Ativo
            </label>
        </div>
    </div>
    <div>
        <x-input-label for="categoria_id" value="Categoria (opcional)" />
        <select id="categoria_id" name="categoria_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            <option value="">Sem categoria</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}" @selected(old('categoria_id', $imposto->categoria_id ?? '') == $categoria->id)>{{ $categoria->nome }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('categoria_id')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="descricao" value="Observacoes (opcional)" />
        <textarea id="descricao" name="descricao" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('descricao', $imposto->descricao ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
    </div>
</div>
