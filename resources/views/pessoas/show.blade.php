<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-2 text-gray-500 text-sm mb-1">
                    <a href="{{ route('pessoas.index') }}" class="hover:text-indigo-600">Pessoas</a>
                    <span>/</span>
                    <span class="text-gray-900">{{ $pessoa->nome }}</span>
                </div>
                <h2 class="font-semibold text-xl text-gray-800 flex items-center gap-2">
                    {{ $pessoa->nome }}
                    @php $cor = $pessoa->tipoCor(); @endphp
                    <span class="text-xs font-normal px-2 py-0.5 rounded-full bg-{{ $cor }}-100 text-{{ $cor }}-800">
                        {{ $pessoa->tipoLabel() }}
                    </span>
                    @if($pessoa->certidoesVencidas() > 0)
                    <span class="text-xs font-normal px-2 py-0.5 rounded-full bg-red-100 text-red-700">
                        ⚠️ {{ $pessoa->certidoesVencidas() }} vencida(s)
                    </span>
                    @endif
                </h2>
            </div>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('pessoas.edit', $pessoa) }}"
               class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                ✏️ Editar
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3">
                {{ session('success') }}
            </div>
            @endif

            {{-- Tabs --}}
            <div x-data="{ tab: '{{ session('_tab', 'geral') }}' }">

                <div class="flex gap-1 border-b border-gray-200 mb-6 flex-wrap">
                    @foreach([
                        'geral'     => 'Dados Gerais',
                        'endereco'  => 'Endereço',
                        'certidoes' => 'Certidões (' . $pessoa->certidoes->count() . ')',
                        'imoveis'   => 'Imóveis (' . ($pessoa->imoveis->count() + $pessoa->lotes->filter(fn($l) => $l->imovel)->count()) . ')',
                    ] as $key => $label)
                    <button @click="tab = '{{ $key }}'"
                            :class="tab === '{{ $key }}'
                                ? 'border-b-2 border-indigo-600 text-indigo-600'
                                : 'text-gray-500 hover:text-gray-700'"
                            class="px-4 py-2 text-sm font-medium transition whitespace-nowrap">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>

                {{-- ────────── ABA DADOS GERAIS ────────── --}}
                <div x-show="tab === 'geral'" x-cloak>
                    <div class="bg-white shadow-sm rounded-lg divide-y divide-gray-100">

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-0 divide-x divide-y divide-gray-100">

                            @php
                            $campos = [
                                'CPF/CNPJ'          => $pessoa->cpfCnpjFormatado() ?: null,
                                'RG'                => $pessoa->rg ? $pessoa->rg . ($pessoa->orgao_emissor_rg ? ' / ' . $pessoa->orgao_emissor_rg : '') : null,
                                'Nascimento'        => $pessoa->data_nascimento?->format('d/m/Y'),
                                'Estado Civil'      => $pessoa->estado_civil ? $pessoa->estadoCivilLabel() : null,
                                'Profissão'         => $pessoa->profissao,
                                'Nacionalidade'     => $pessoa->nacionalidade,
                                'Como chegou'       => $pessoa->origem,
                            ];
                            @endphp

                            @foreach($campos as $label => $valor)
                            <div class="px-4 py-3">
                                <span class="block text-xs text-gray-400 mb-0.5">{{ $label }}</span>
                                <span class="text-sm font-medium text-gray-800">{{ $valor ?? '—' }}</span>
                            </div>
                            @endforeach
                        </div>

                        {{-- Contatos --}}
                        <div class="px-5 py-4">
                            <p class="text-xs text-gray-400 uppercase font-medium mb-2">Contato</p>
                            <div class="flex flex-wrap gap-4 text-sm text-gray-700">
                                @if($pessoa->celular)
                                <span>📱 {{ $pessoa->celular }}</span>
                                @endif
                                @if($pessoa->telefone)
                                <span>📞 {{ $pessoa->telefone }}</span>
                                @endif
                                @if($pessoa->email)
                                <a href="mailto:{{ $pessoa->email }}" class="text-indigo-600 hover:underline">✉️ {{ $pessoa->email }}</a>
                                @endif
                                @if(! $pessoa->celular && ! $pessoa->telefone && ! $pessoa->email)
                                <span class="text-gray-400">Nenhum contato cadastrado.</span>
                                @endif
                            </div>
                        </div>

                        @if($pessoa->obs)
                        <div class="px-5 py-4">
                            <p class="text-xs text-gray-400 uppercase font-medium mb-1">Observações</p>
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $pessoa->obs }}</p>
                        </div>
                        @endif

                    </div>
                </div>

                {{-- ────────── ABA ENDEREÇO ────────── --}}
                <div x-show="tab === 'endereco'" x-cloak>
                    <div class="bg-white shadow-sm rounded-lg divide-y divide-gray-100">
                        @if($pessoa->endereco && $pessoa->endereco->enderecoCompleto() !== '—')
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-0 divide-x divide-y divide-gray-100">
                            @php
                            $end = $pessoa->endereco;
                            $camposEnd = [
                                'CEP'         => $end->cep,
                                'Logradouro'  => $end->logradouro,
                                'Número'      => $end->numero,
                                'Complemento' => $end->complemento,
                                'Bairro'      => $end->bairro,
                                'Cidade'      => $end->cidade,
                                'Estado'      => $end->estado,
                            ];
                            @endphp
                            @foreach($camposEnd as $label => $valor)
                            <div class="px-4 py-3">
                                <span class="block text-xs text-gray-400 mb-0.5">{{ $label }}</span>
                                <span class="text-sm font-medium text-gray-800">{{ $valor ?? '—' }}</span>
                            </div>
                            @endforeach
                        </div>
                        <div class="px-5 py-3">
                            <p class="text-xs text-gray-400 mb-1">Endereço completo</p>
                            <p class="text-sm text-gray-700">{{ $pessoa->endereco->enderecoCompleto() }}</p>
                        </div>
                        @else
                        <div class="text-center py-12 text-gray-400">
                            <div class="text-4xl mb-2">📍</div>
                            <p class="text-sm">Endereço não cadastrado.</p>
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('pessoas.edit', $pessoa) }}" class="text-indigo-600 text-sm hover:underline mt-2 inline-block">
                                + Adicionar endereço
                            </a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                {{-- ────────── ABA CERTIDÕES ────────── --}}
                <div x-show="tab === 'certidoes'" x-cloak>

                    @if($pessoa->certidoesVencidas() > 0)
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3">
                        ⚠️ {{ $pessoa->certidoesVencidas() }} documento(s) vencido(s) — verifique a listagem abaixo.
                    </div>
                    @endif

                    {{-- Form upload --}}
                    @if(auth()->user()->isAdmin())
                    <div class="bg-white shadow-sm rounded-lg p-5 mb-5"
                         x-data="uploadCertidao()" @dragover.prevent="dragover = true" @dragleave="dragover = false"
                         @drop.prevent="onDrop($event)">

                        <h3 class="font-semibold text-gray-700 text-sm mb-4">Enviar Certidão / Documento</h3>

                        <form method="POST" action="{{ route('pessoas.certidoes.store', $pessoa) }}"
                              enctype="multipart/form-data" id="formCertidao" @submit="submitting = true">
                            @csrf

                            {{-- Drop zone --}}
                            <div :class="dragover ? 'border-indigo-400 bg-indigo-50' : 'border-gray-300 bg-gray-50'"
                                 class="border-2 border-dashed rounded-lg p-6 text-center cursor-pointer transition mb-4"
                                 @click="$refs.arquivo.click()">
                                <input type="file" name="arquivo" x-ref="arquivo"
                                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                       class="hidden" @change="onFile($event)">
                                <template x-if="!fileName">
                                    <div>
                                        <div class="text-3xl mb-1">📎</div>
                                        <p class="text-sm text-gray-500">Arraste o arquivo ou <span class="text-indigo-600 font-medium">clique para selecionar</span></p>
                                        <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG, DOC — máx. 20 MB</p>
                                    </div>
                                </template>
                                <template x-if="fileName">
                                    <div>
                                        <div class="text-2xl mb-1">✅</div>
                                        <p class="text-sm font-medium text-gray-700" x-text="fileName"></p>
                                        <p class="text-xs text-gray-400">Clique para trocar</p>
                                    </div>
                                </template>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Tipo</label>
                                    <select name="tipo" class="w-full border-gray-300 rounded-md text-sm">
                                        <option value="outro">Outro</option>
                                        <option value="rg">RG</option>
                                        <option value="cpf">CPF</option>
                                        <option value="cnh">CNH</option>
                                        <option value="comprovante_residencia">Comprovante de Residência</option>
                                        <option value="certidao_nascimento">Certidão de Nascimento</option>
                                        <option value="certidao_casamento">Certidão de Casamento</option>
                                        <option value="certidao_obito">Certidão de Óbito</option>
                                        <option value="procuracao">Procuração</option>
                                    </select>
                                </div>

                                <div class="sm:col-span-2 lg:col-span-2">
                                    <label class="block text-xs text-gray-500 mb-1">Título (opcional)</label>
                                    <input type="text" name="titulo" placeholder="Ex.: RG do titular"
                                           class="w-full border-gray-300 rounded-md text-sm">
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Nº Documento</label>
                                    <input type="text" name="numero_documento"
                                           class="w-full border-gray-300 rounded-md text-sm">
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Órgão Emissor</label>
                                    <input type="text" name="orgao_emissor" placeholder="SSP/SP, Cartório..."
                                           class="w-full border-gray-300 rounded-md text-sm">
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Data Emissão</label>
                                    <input type="date" name="data_emissao"
                                           class="w-full border-gray-300 rounded-md text-sm">
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Data Vencimento</label>
                                    <input type="date" name="data_vencimento"
                                           class="w-full border-gray-300 rounded-md text-sm">
                                </div>

                            </div>

                            <div class="mt-4 flex justify-end">
                                <button type="submit" :disabled="!fileName || submitting"
                                        :class="(!fileName || submitting) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-700'"
                                        class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md transition">
                                    <span x-show="!submitting">Enviar Certidão</span>
                                    <span x-show="submitting">Enviando...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif

                    {{-- Listagem --}}
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        @if($pessoa->certidoes->isEmpty())
                        <div class="text-center py-12 text-gray-400">
                            <div class="text-4xl mb-2">🗂️</div>
                            <p class="text-sm">Nenhuma certidão cadastrada.</p>
                        </div>
                        @else
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Documento</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Emissão</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vencimento</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tamanho</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($pessoa->certidoes as $cert)
                                <tr class="{{ $cert->vencida() ? 'bg-red-50' : '' }} hover:bg-gray-50 transition">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg">{{ $cert->icone() }}</span>
                                            <div>
                                                <p class="font-medium text-gray-800">
                                                    {{ $cert->titulo ?: $cert->tipoLabel() }}
                                                </p>
                                                @if($cert->numero_documento)
                                                <p class="text-xs text-gray-400">Nº {{ $cert->numero_documento }}</p>
                                                @endif
                                                @if($cert->orgao_emissor)
                                                <p class="text-xs text-gray-400">{{ $cert->orgao_emissor }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $cert->tipoLabel() }}</td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ $cert->data_emissao?->format('d/m/Y') ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($cert->data_vencimento)
                                        <span class="{{ $cert->vencida() ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                            {{ $cert->data_vencimento->format('d/m/Y') }}
                                            @if($cert->vencida()) <span class="text-xs">(vencida)</span> @endif
                                        </span>
                                        @else
                                        <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $cert->tamanhoFormatado() }}</td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        {{-- Visualizar --}}
                                        @if($cert->isPdf() || $cert->isImage())
                                        <button type="button"
                                                @click="$dispatch('open-viewer', { url: '{{ route('pessoas.certidoes.visualizar', $cert) }}', isPdf: {{ $cert->isPdf() ? 'true' : 'false' }}, nome: '{{ addslashes($cert->titulo ?: $cert->tipoLabel()) }}' })"
                                                class="text-indigo-500 hover:text-indigo-700 text-xs mr-2">
                                            👁 Ver
                                        </button>
                                        @endif
                                        {{-- Download --}}
                                        <a href="{{ route('pessoas.certidoes.download', $cert) }}"
                                           class="text-gray-500 hover:text-gray-700 text-xs mr-2">
                                            ⬇ Baixar
                                        </a>
                                        {{-- Deletar --}}
                                        @if(auth()->user()->isAdmin())
                                        <form method="POST" action="{{ route('pessoas.certidoes.destroy', $cert) }}"
                                              class="inline"
                                              onsubmit="return confirm('Remover {{ addslashes($cert->titulo ?: $cert->tipoLabel()) }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-600 text-xs">🗑</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                    </div>
                </div>

                {{-- ────────── ABA IMÓVEIS ────────── --}}
                <div x-show="tab === 'imoveis'" x-cloak>
                    @php
                        $imoveisAtivos = $pessoa->imoveis; // pivot imovel_pessoa

                        // Imóveis via lote (lote_pessoa → lote.imovel)
                        $imoveisViaLote = $pessoa->lotes
                            ->filter(fn($l) => $l->imovel !== null)
                            ->map(fn($l) => (object)[
                                'imovel'       => $l->imovel,
                                'papel'        => $l->pivot->papel,
                                'data_vinculo' => $l->pivot->data_vinculo,
                                'via_lote'     => true,
                                'lote'         => $l,
                            ]);

                        $historicos         = $pessoa->imovelHistoricos;
                        // Mostra TODOS os desvinculados, mesmo que o imóvel esteja ativo novamente
                        $historicosPassados = $historicos->where('acao', 'desvinculado');
                        $semNada = $imoveisAtivos->isEmpty() && $imoveisViaLote->isEmpty() && $historicosPassados->isEmpty();
                    @endphp

                    @if($semNada)
                    <div class="bg-white shadow-sm rounded-lg p-12 text-center">
                        <div class="text-5xl mb-3">🏠</div>
                        <p class="text-gray-500 text-sm">Nenhum imóvel vinculado ainda.</p>
                    </div>
                    @else

                    {{-- Resumo --}}
                    @php
                        $totalAtivos     = $imoveisAtivos->count() + $imoveisViaLote->count();
                        $totalEncerrados = $historicosPassados->count();
                        $valorSnapshot   = $historicos->where('acao', 'vinculado')->sum('valor_imovel');
                    @endphp
                    <div class="grid grid-cols-3 gap-4 mb-5">
                        <div class="bg-green-50 border border-green-100 rounded-lg px-4 py-3 text-center">
                            <div class="text-2xl font-bold text-green-700">{{ $totalAtivos }}</div>
                            <div class="text-xs text-green-600 mt-0.5">Vínculos ativos</div>
                        </div>
                        <div class="bg-gray-50 border border-gray-100 rounded-lg px-4 py-3 text-center">
                            <div class="text-2xl font-bold text-gray-600">{{ $totalEncerrados }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">Imóveis encerrados</div>
                        </div>
                        <div class="bg-indigo-50 border border-indigo-100 rounded-lg px-4 py-3 text-center">
                            <div class="text-base font-bold text-indigo-700">
                                {{ $valorSnapshot ? 'R$ ' . number_format($valorSnapshot, 0, ',', '.') : '—' }}
                            </div>
                            <div class="text-xs text-indigo-500 mt-0.5">Soma valores registrados</div>
                        </div>
                    </div>

                    {{-- Vínculos Ativos --}}
                    @if($imoveisAtivos->isNotEmpty() || $imoveisViaLote->isNotEmpty())
                    <div class="mb-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">● Vínculos Ativos</p>
                        <div class="space-y-2">
                            @php
                                $papelLabels = ['proprietario'=>'👑 Proprietário','locatario'=>'🏠 Locatário','comprador'=>'🛒 Comprador','interessado'=>'🔍 Interessado'];
                            @endphp

                            @php
                                // Macro para renderizar mini-histórico de valores de um imóvel
                                $miniHistorico = function($im) {
                                    $eventos = collect($im->historicos ?? [])
                                        ->whereIn('tipo', ['compra','venda','avaliacao','permuta'])
                                        ->whereNotNull('valor')
                                        ->sortBy('data')
                                        ->values();
                                    return $eventos;
                                };
                            @endphp

                            {{-- Via imovel_pessoa --}}
                            @foreach($imoveisAtivos as $im)
                            @php
                                $papel   = $im->pivot->papel;
                                $eventos = $miniHistorico($im);
                            @endphp
                            <div class="bg-white shadow-sm rounded-lg px-5 py-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('imoveis.show', $im) }}"
                                           class="font-semibold text-gray-900 hover:text-indigo-600 text-sm">
                                            {{ $im->tipoLabel() }}@if($im->nome) · {{ $im->nome }}@endif
                                        </a>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $im->lote?->quadra?->condominio?->nome }}
                                            @if($im->lote?->quadra) · Quadra {{ $im->lote->quadra->codigo }}@endif
                                            @if($im->lote) · Lote {{ $im->lote->numero }}@endif
                                        </p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="inline-block text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-medium">
                                            {{ $papelLabels[$papel] ?? $papel }}
                                        </span>
                                        @if($im->pivot->data_vinculo)
                                        <p class="text-xs text-gray-400 mt-1">
                                            desde {{ \Carbon\Carbon::parse($im->pivot->data_vinculo)->format('d/m/Y') }}
                                        </p>
                                        @endif
                                        @if($im->valor_mercado)
                                        <p class="text-xs font-semibold text-indigo-600 mt-0.5">
                                            R$ {{ number_format($im->valor_mercado, 0, ',', '.') }}
                                        </p>
                                        @endif
                                    </div>
                                </div>
                                @if($eventos->isNotEmpty())
                                <div class="mt-3 pt-3 border-t border-gray-100">
                                    <p class="text-xs text-gray-400 uppercase font-medium mb-2">Histórico de valores</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($eventos as $ev)
                                        <div class="text-center bg-gray-50 rounded px-2 py-1 min-w-16">
                                            <div class="text-xs font-semibold text-indigo-700">R$ {{ number_format($ev->valor/1000, 0, ',', '.') }}k</div>
                                            <div class="text-xs text-gray-400">{{ $ev->data?->format('m/Y') }}</div>
                                            <div class="text-xs text-gray-300">{{ $ev->tipoLabel() }}</div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endforeach

                            {{-- Via lote_pessoa --}}
                            @foreach($imoveisViaLote as $entry)
                            @php
                                $im      = $entry->imovel;
                                $eventos = $miniHistorico($im);
                            @endphp
                            <div class="bg-white shadow-sm rounded-lg px-5 py-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('imoveis.show', $im) }}"
                                           class="font-semibold text-gray-900 hover:text-indigo-600 text-sm">
                                            {{ $im->tipoLabel() }}@if($im->nome) · {{ $im->nome }}@endif
                                        </a>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $im->lote?->quadra?->condominio?->nome }}
                                            @if($im->lote?->quadra) · Quadra {{ $im->lote->quadra->codigo }}@endif
                                            @if($im->lote) · Lote {{ $im->lote->numero }}@endif
                                        </p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="inline-block text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-medium">
                                            {{ $papelLabels[$entry->papel] ?? $entry->papel }}
                                        </span>
                                        <span class="inline-block text-xs px-2 py-0.5 rounded-full bg-blue-50 text-blue-500 font-medium ml-1">
                                            via Lote
                                        </span>
                                        @if($entry->data_vinculo)
                                        <p class="text-xs text-gray-400 mt-1">
                                            desde {{ \Carbon\Carbon::parse($entry->data_vinculo)->format('d/m/Y') }}
                                        </p>
                                        @endif
                                        @if($im->valor_mercado)
                                        <p class="text-xs font-semibold text-indigo-600 mt-0.5">
                                            R$ {{ number_format($im->valor_mercado, 0, ',', '.') }}
                                        </p>
                                        @endif
                                    </div>
                                </div>
                                @if($eventos->isNotEmpty())
                                <div class="mt-3 pt-3 border-t border-gray-100">
                                    <p class="text-xs text-gray-400 uppercase font-medium mb-2">Histórico de valores</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($eventos as $ev)
                                        <div class="text-center bg-gray-50 rounded px-2 py-1 min-w-16">
                                            <div class="text-xs font-semibold text-indigo-700">R$ {{ number_format($ev->valor/1000, 0, ',', '.') }}k</div>
                                            <div class="text-xs text-gray-400">{{ $ev->data?->format('m/Y') }}</div>
                                            <div class="text-xs text-gray-300">{{ $ev->tipoLabel() }}</div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Histórico de imóveis encerrados --}}
                    @if($historicosPassados->isNotEmpty())
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">○ Histórico — Vínculos Encerrados</p>
                        <div class="space-y-2">
                            @foreach($historicosPassados->sortByDesc('created_at') as $desvInc)
                            @php
                                $im   = $desvInc->imovel;
                                $vinc = $historicos
                                    ->where('imovel_id', $desvInc->imovel_id)
                                    ->where('acao', 'vinculado')
                                    ->where('created_at', '<=', $desvInc->created_at)
                                    ->sortByDesc('created_at')
                                    ->first();

                                // Evolução de valores do imóvel durante o período de vínculo
                                $dataEntrada = $vinc?->created_at ?? $desvInc->data_vinculo;
                                $dataSaida   = $desvInc->created_at;
                                $eventosValor = collect($im->historicos ?? [])
                                    ->whereIn('tipo', ['compra','venda','avaliacao','permuta'])
                                    ->whereNotNull('valor')
                                    ->sortBy('data')
                                    ->values();
                            @endphp
                            <div class="bg-white shadow-sm rounded-lg px-5 py-4 border-l-4 border-gray-200">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('imoveis.show', $im) }}"
                                           class="font-semibold text-gray-700 hover:text-indigo-600 text-sm">
                                            {{ $im->tipoLabel() }}@if($im->nome) · {{ $im->nome }}@endif
                                        </a>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $im->lote?->quadra?->condominio?->nome }}
                                            @if($im->lote?->quadra) · Quadra {{ $im->lote->quadra->codigo }}@endif
                                            @if($im->lote) · Lote {{ $im->lote->numero }}@endif
                                        </p>
                                    </div>
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 font-medium shrink-0">
                                        ○ Encerrado
                                    </span>
                                </div>

                                {{-- Dados do vínculo --}}
                                <div class="flex flex-wrap gap-4 mt-2 text-xs text-gray-500">
                                    @if($desvInc->papel)
                                    <span>Papel: <strong>{{ $desvInc->papelLabel() }}</strong></span>
                                    @endif
                                    @if($vinc?->valor_imovel)
                                    <span>Valor na entrada: <strong class="text-indigo-600">R$ {{ number_format($vinc->valor_imovel, 0, ',', '.') }}</strong></span>
                                    @endif
                                    @if($desvInc->valor_imovel)
                                    <span>Valor na saída: <strong class="text-gray-700">R$ {{ number_format($desvInc->valor_imovel, 0, ',', '.') }}</strong></span>
                                    @endif
                                    @if($vinc?->valor_imovel && $desvInc->valor_imovel && $vinc->valor_imovel > 0)
                                    @php $variacao = (($desvInc->valor_imovel - $vinc->valor_imovel) / $vinc->valor_imovel) * 100; @endphp
                                    <span class="{{ $variacao >= 0 ? 'text-green-600' : 'text-red-500' }} font-semibold">
                                        {{ $variacao >= 0 ? '▲' : '▼' }} {{ number_format(abs($variacao), 1) }}%
                                    </span>
                                    @endif
                                    <span class="text-gray-400">Encerrado em {{ $desvInc->created_at->format('d/m/Y') }}</span>
                                </div>

                                {{-- Evolução de valores do imóvel --}}
                                @if($eventosValor->isNotEmpty())
                                <div class="mt-3 pt-3 border-t border-gray-100">
                                    <p class="text-xs text-gray-400 uppercase font-medium mb-2">Histórico de valores do imóvel</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($eventosValor as $ev)
                                        <div class="text-center bg-gray-50 rounded px-2 py-1 min-w-16">
                                            <div class="text-xs font-semibold text-indigo-700">R$ {{ number_format($ev->valor/1000, 0, ',', '.') }}k</div>
                                            <div class="text-xs text-gray-400">{{ $ev->data?->format('m/Y') }}</div>
                                            <div class="text-xs text-gray-300">{{ $ev->tipoLabel() }}</div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @endif
                </div>

            </div>{{-- /x-data tabs --}}

        </div>
    </div>

    {{-- ── Modal Visualizador de Documentos ────────────────────── --}}
    <div x-data="docViewer()" @open-viewer.window="open($event.detail)" x-cloak>
        <div x-show="show"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4"
             @click.self="show = false">
            <div class="bg-white rounded-xl shadow-2xl flex flex-col w-full max-w-4xl max-h-[90vh]">
                <div class="flex justify-between items-center px-5 py-3 border-b">
                    <h3 class="font-semibold text-gray-700 text-sm" x-text="nome"></h3>
                    <div class="flex items-center gap-3">
                        <a :href="url" download class="text-sm text-indigo-600 hover:underline">⬇ Baixar</a>
                        <button @click="show = false" class="text-gray-400 hover:text-gray-700 text-xl leading-none">×</button>
                    </div>
                </div>
                <div class="flex-1 overflow-hidden p-2">
                    <template x-if="isPdf">
                        <iframe :src="url" class="w-full h-[75vh] rounded border-0"></iframe>
                    </template>
                    <template x-if="!isPdf">
                        <div class="flex items-center justify-center h-[75vh]">
                            <img :src="url" class="max-h-full max-w-full object-contain rounded">
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function uploadCertidao() {
        return {
            dragover: false,
            fileName: null,
            submitting: false,
            onFile(e) {
                const f = e.target.files[0];
                if (f) this.fileName = f.name;
            },
            onDrop(e) {
                this.dragover = false;
                const f = e.dataTransfer.files[0];
                if (!f) return;
                this.fileName = f.name;
                const dt = new DataTransfer();
                dt.items.add(f);
                this.$refs.arquivo.files = dt.files;
            },
        };
    }

    function docViewer() {
        return {
            show: false,
            url: '',
            isPdf: true,
            nome: '',
            open(detail) {
                this.url   = detail.url;
                this.isPdf = detail.isPdf;
                this.nome  = detail.nome;
                this.show  = true;
            },
        };
    }
    </script>
    @endpush

</x-app-layout>
