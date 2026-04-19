{{-- _form.blade.php — usado em create e edit --}}
<div x-data="{ aba: '{{ old('_aba', 'basicos') }}' }">

    {{-- Navegação de abas --}}
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex flex-wrap gap-x-1" aria-label="Abas">
            @php
            $abas = [
                'basicos'      => ['label' => 'Dados Básicos',  'icon' => '🏠'],
                'identificacao'=> ['label' => 'Identificação',  'icon' => '📋'],
                'endereco'     => ['label' => 'Endereço',       'icon' => '📍'],
                'financeiro'   => ['label' => 'Financeiro',     'icon' => '💰'],
            ];
            @endphp

            @foreach($abas as $key => $info)
            <button type="button"
                    @click="aba = '{{ $key }}'"
                    :class="aba === '{{ $key }}'
                        ? 'border-indigo-500 text-indigo-600'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm transition-colors">
                {{ $info['icon'] }} {{ $info['label'] }}
            </button>
            @endforeach
        </nav>
    </div>

    {{-- ────────────────────────────────────────────────── --}}
    {{-- ABA: DADOS BÁSICOS --}}
    {{-- ────────────────────────────────────────────────── --}}
    <div x-show="aba === 'basicos'" x-cloak class="space-y-5">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            {{-- Tipo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Imóvel</label>
                <select name="tipo"
                        class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">— selecione —</option>
                    @foreach([
                        'casa'        => 'Casa',
                        'apartamento' => 'Apartamento',
                        'terreno'     => 'Terreno',
                        'chacara'     => 'Chácara / Sítio',
                        'galpao'      => 'Galpão',
                        'sala'        => 'Sala Comercial',
                        'outro'       => 'Outro',
                    ] as $v => $l)
                    <option value="{{ $v }}" {{ old('tipo', $imovel->tipo ?? '') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Nome / Denominação --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome / Denominação</label>
                <input type="text" name="nome" value="{{ old('nome', $imovel->nome ?? '') }}"
                       placeholder="Ex: Casa da Colina, Apt 302..."
                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        {{-- Descrição --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
            <textarea name="descricao" rows="3"
                      placeholder="Descreva o imóvel..."
                      class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('descricao', $imovel->descricao ?? '') }}</textarea>
        </div>

        {{-- Áreas --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Área Total (m²)</label>
                <input type="number" step="0.01" min="0" name="area_total"
                       value="{{ old('area_total', $imovel->area_total ?? '') }}"
                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Área Construída (m²)</label>
                <input type="number" step="0.01" min="0" name="area_construida"
                       value="{{ old('area_construida', $imovel->area_construida ?? '') }}"
                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Área Privativa (m²)</label>
                <input type="number" step="0.01" min="0" name="area_privativa"
                       value="{{ old('area_privativa', $imovel->area_privativa ?? '') }}"
                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        {{-- Características numéricas --}}
        <div class="grid grid-cols-3 sm:grid-cols-5 gap-5">
            @foreach([
                'quartos'       => 'Quartos',
                'suites'        => 'Suítes',
                'banheiros'     => 'Banheiros',
                'vagas_garagem' => 'Vagas',
                'andares'       => 'Andares',
            ] as $field => $label)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                <input type="number" min="0" name="{{ $field }}"
                       value="{{ old($field, $imovel->{$field} ?? '') }}"
                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            {{-- Ano de construção --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ano de Construção</label>
                <input type="number" min="1800" max="{{ date('Y') }}" name="ano_construcao"
                       value="{{ old('ano_construcao', $imovel->ano_construcao ?? '') }}"
                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- Padrão de acabamento --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Padrão de Acabamento</label>
                <select name="padrao_acabamento"
                        class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">— selecione —</option>
                    @foreach(['simples' => 'Simples', 'medio' => 'Médio', 'alto' => 'Alto', 'luxo' => 'Luxo'] as $v => $l)
                    <option value="{{ $v }}" {{ old('padrao_acabamento', $imovel->padrao_acabamento ?? '') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Situação de ocupação --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Situação de Ocupação</label>
                <select name="situacao_ocupacao"
                        class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">— selecione —</option>
                    @foreach([
                        'desocupado'          => 'Desocupado',
                        'ocupado_proprietario'=> 'Ocupado pelo Proprietário',
                        'locado'              => 'Locado',
                        'outros'              => 'Outros',
                    ] as $v => $l)
                    <option value="{{ $v }}" {{ old('situacao_ocupacao', $imovel->situacao_ocupacao ?? '') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Condomínio fechado --}}
        <div class="flex items-center gap-2">
            <input type="checkbox" id="condominio_fechado" name="condominio_fechado" value="1"
                   {{ old('condominio_fechado', $imovel->condominio_fechado ?? false) ? 'checked' : '' }}
                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <label for="condominio_fechado" class="text-sm text-gray-700">Em condomínio fechado</label>
        </div>

        {{-- Observações --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
            <textarea name="observacoes" rows="3"
                      placeholder="Informações adicionais..."
                      class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('observacoes', $imovel->observacoes ?? '') }}</textarea>
        </div>
    </div>

    {{-- ────────────────────────────────────────────────── --}}
    {{-- ABA: IDENTIFICAÇÃO / REGISTRO --}}
    {{-- ────────────────────────────────────────────────── --}}
    <div x-show="aba === 'identificacao'" x-cloak class="space-y-5">

        <p class="text-xs text-gray-400 italic">Dados de registro cartorial e identificação fiscal do imóvel.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Matrícula do Imóvel</label>
                <input type="text" name="matricula_imovel"
                       value="{{ old('matricula_imovel', $imovel->matricula_imovel ?? '') }}"
                       placeholder="Nº da matrícula no cartório"
                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Inscrição Municipal (IPTU)</label>
                <input type="text" name="inscricao_municipal"
                       value="{{ old('inscricao_municipal', $imovel->inscricao_municipal ?? '') }}"
                       placeholder="Código IPTU"
                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cartório</label>
                <input type="text" name="cartorio"
                       value="{{ old('cartorio', $imovel->cartorio ?? '') }}"
                       placeholder="Nome do cartório de registro"
                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Número da Escritura</label>
                <input type="text" name="numero_escritura"
                       value="{{ old('numero_escritura', $imovel->numero_escritura ?? '') }}"
                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Livro</label>
                <input type="text" name="livro_escritura"
                       value="{{ old('livro_escritura', $imovel->livro_escritura ?? '') }}"
                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Folha</label>
                <input type="text" name="folha_escritura"
                       value="{{ old('folha_escritura', $imovel->folha_escritura ?? '') }}"
                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>
    </div>

    {{-- ────────────────────────────────────────────────── --}}
    {{-- ABA: ENDEREÇO --}}
    {{-- ────────────────────────────────────────────────── --}}
    <div x-show="aba === 'endereco'" x-cloak class="space-y-5">

        <p class="text-xs text-gray-400 italic">Preencha se o endereço do imóvel difere do condomínio.</p>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Logradouro</label>
                <input type="text" name="logradouro"
                       value="{{ old('logradouro', $imovel->logradouro ?? '') }}"
                       placeholder="Rua, Avenida, Alameda..."
                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                <input type="text" name="numero_endereco"
                       value="{{ old('numero_endereco', $imovel->numero_endereco ?? '') }}"
                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                <input type="text" name="complemento"
                       value="{{ old('complemento', $imovel->complemento ?? '') }}"
                       placeholder="Apto, Bloco, etc."
                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                <input type="text" name="bairro"
                       value="{{ old('bairro', $imovel->bairro ?? '') }}"
                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                <input type="text" name="cep"
                       value="{{ old('cep', $imovel->cep ?? '') }}"
                       placeholder="00000-000"
                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                <input type="text" name="cidade"
                       value="{{ old('cidade', $imovel->cidade ?? '') }}"
                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado (UF)</label>
                <input type="text" name="estado" maxlength="2"
                       value="{{ old('estado', $imovel->estado ?? '') }}"
                       placeholder="SP"
                       class="w-full border-gray-300 rounded-md shadow-sm text-sm uppercase focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        <div class="border-t border-gray-100 pt-4">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Coordenadas GPS</p>
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                    <input type="number" step="0.0000001" name="latitude"
                           value="{{ old('latitude', $imovel->latitude ?? '') }}"
                           placeholder="-23.5505"
                           class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                    <input type="number" step="0.0000001" name="longitude"
                           value="{{ old('longitude', $imovel->longitude ?? '') }}"
                           placeholder="-46.6333"
                           class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>
    </div>

    {{-- ────────────────────────────────────────────────── --}}
    {{-- ABA: FINANCEIRO --}}
    {{-- ────────────────────────────────────────────────── --}}
    <div x-show="aba === 'financeiro'" x-cloak class="space-y-5">

        <p class="text-xs text-gray-400 italic">Valores de referência e dados financeiros do imóvel.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Valor Venal (R$)</label>
                <input type="number" step="0.01" min="0" name="valor_venal"
                       value="{{ old('valor_venal', $imovel->valor_venal ?? '') }}"
                       placeholder="0,00"
                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Valor de Mercado (R$)</label>
                <input type="number" step="0.01" min="0" name="valor_mercado"
                       value="{{ old('valor_mercado', $imovel->valor_mercado ?? '') }}"
                       placeholder="0,00"
                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">IPTU Anual (R$)</label>
                <input type="number" step="0.01" min="0" name="valor_iptu_anual"
                       value="{{ old('valor_iptu_anual', $imovel->valor_iptu_anual ?? '') }}"
                       placeholder="0,00"
                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data da Última Avaliação</label>
                <input type="date" name="data_ultima_avaliacao"
                       value="{{ old('data_ultima_avaliacao', isset($imovel->data_ultima_avaliacao) ? $imovel->data_ultima_avaliacao->format('Y-m-d') : '') }}"
                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        {{-- Indicador visual de valorização (só no edit, quando há histórico) --}}
        @isset($imovel)
        @if($imovel->historicos?->count() > 0)
        @php
            $vendas = $imovel->historicos->whereIn('tipo', ['venda','compra'])->sortBy('data');
            $primeira = $vendas->first();
            $ultima = $vendas->last();
        @endphp
        @if($primeira && $ultima && $primeira->id !== $ultima->id && $primeira->valor && $ultima->valor)
        <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-sm font-semibold text-green-800 mb-1">📈 Evolução de Valor</p>
            <p class="text-xs text-green-700">
                De <strong>R$ {{ number_format($primeira->valor, 2, ',', '.') }}</strong>
                ({{ $primeira->data->format('d/m/Y') }})
                para <strong>R$ {{ number_format($ultima->valor, 2, ',', '.') }}</strong>
                ({{ $ultima->data->format('d/m/Y') }})
                @php $perc = $primeira->valor > 0 ? (($ultima->valor - $primeira->valor) / $primeira->valor) * 100 : 0; @endphp
                — <strong>{{ $perc >= 0 ? '+' : '' }}{{ number_format($perc, 1) }}%</strong>
            </p>
        </div>
        @endif
        @endif
        @endisset

    </div>

    {{-- Campo hidden para controle de aba ativa no redirecionamento em caso de erro --}}
    <input type="hidden" name="_aba" x-bind:value="aba">

</div>
