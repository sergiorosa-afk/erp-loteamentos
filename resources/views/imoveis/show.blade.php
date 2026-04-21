<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-2 text-gray-500 text-sm mb-1">
                    <a href="{{ route('condominios.index') }}" class="hover:text-indigo-600">Condomínios</a>
                    <span>/</span>
                    <a href="{{ route('condominios.show', $imovel->lote->quadra->condominio) }}" class="hover:text-indigo-600">{{ $imovel->lote->quadra->condominio->nome }}</a>
                    <span>/</span>
                    <a href="{{ route('quadras.show', $imovel->lote->quadra) }}" class="hover:text-indigo-600">Quadra {{ $imovel->lote->quadra->codigo }}</a>
                    <span>/</span>
                    <a href="{{ route('lotes.show', $imovel->lote) }}" class="hover:text-indigo-600">Lote {{ $imovel->lote->numero }}</a>
                    <span>/</span>
                    <span class="text-gray-900">Imóvel</span>
                </div>
                <h2 class="font-semibold text-xl text-gray-800 flex items-center gap-2">
                    {{ $imovel->tipoLabel() }}
                    @if($imovel->nome)
                        <span class="text-gray-500 font-normal">· {{ $imovel->nome }}</span>
                    @endif
                    @if($imovel->padrao_acabamento)
                    @php $padroes = ['simples'=>'gray','medio'=>'blue','alto'=>'purple','luxo'=>'yellow']; $pc = $padroes[$imovel->padrao_acabamento] ?? 'gray'; @endphp
                    <span class="text-xs bg-{{ $pc }}-100 text-{{ $pc }}-800 px-2 py-0.5 rounded-full font-normal capitalize">{{ $imovel->padrao_acabamento }}</span>
                    @endif
                </h2>
            </div>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('imoveis.edit', $imovel) }}"
               class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                ✏️ Editar Imóvel
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3">
                {{ session('success') }}
            </div>
            @endif

            {{-- Card de capa / resumo rápido --}}
            <div class="bg-white shadow-sm rounded-lg p-5 mb-6 flex flex-wrap gap-6 items-center">
                @php $capa = $imovel->capa(); @endphp
                @if($capa)
                <img src="{{ $capa->url() }}" alt="Foto principal"
                     class="h-28 w-44 object-cover rounded-lg border border-gray-200 shrink-0">
                @else
                <div class="h-28 w-44 flex items-center justify-center bg-gray-100 rounded-lg border border-gray-200 shrink-0 text-4xl">🏠</div>
                @endif

                <div class="flex-1 min-w-0 grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                    @if($imovel->area_construida)
                    <div><span class="text-gray-400 block text-xs">Área Construída</span><span class="font-semibold">{{ number_format($imovel->area_construida, 2, ',', '.') }} m²</span></div>
                    @endif
                    @if($imovel->area_total)
                    <div><span class="text-gray-400 block text-xs">Área Total</span><span class="font-semibold">{{ number_format($imovel->area_total, 2, ',', '.') }} m²</span></div>
                    @endif
                    @if($imovel->quartos)
                    <div><span class="text-gray-400 block text-xs">Quartos</span><span class="font-semibold">{{ $imovel->quartos }}@if($imovel->suites) <span class="text-xs text-gray-400">({{ $imovel->suites }} suítes)</span>@endif</span></div>
                    @endif
                    @if($imovel->banheiros)
                    <div><span class="text-gray-400 block text-xs">Banheiros</span><span class="font-semibold">{{ $imovel->banheiros }}</span></div>
                    @endif
                    @if($imovel->vagas_garagem)
                    <div><span class="text-gray-400 block text-xs">Vagas</span><span class="font-semibold">{{ $imovel->vagas_garagem }}</span></div>
                    @endif
                    @if($imovel->ano_construcao)
                    <div><span class="text-gray-400 block text-xs">Construção</span><span class="font-semibold">{{ $imovel->ano_construcao }}</span></div>
                    @endif
                    @if($imovel->valor_mercado)
                    <div><span class="text-gray-400 block text-xs">Valor de Mercado</span><span class="font-semibold text-indigo-700">R$ {{ number_format($imovel->valor_mercado, 2, ',', '.') }}</span></div>
                    @endif
                    @if($imovel->situacao_ocupacao)
                    @php $ocup = ['desocupado'=>'gray','ocupado_proprietario'=>'blue','locado'=>'green','outros'=>'orange']; $oc = $ocup[$imovel->situacao_ocupacao] ?? 'gray'; $oclabels = ['desocupado'=>'Desocupado','ocupado_proprietario'=>'Ocupado (proprietário)','locado'=>'Locado','outros'=>'Outros']; @endphp
                    <div><span class="text-gray-400 block text-xs">Ocupação</span><span class="inline-block text-xs bg-{{ $oc }}-100 text-{{ $oc }}-800 px-2 py-0.5 rounded-full">{{ $oclabels[$imovel->situacao_ocupacao] ?? $imovel->situacao_ocupacao }}</span></div>
                    @endif
                </div>
            </div>

            {{-- Abas de visualização --}}
            <div x-data="{ aba: '{{ session('tab', 'basicos') }}' }">

                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex flex-wrap gap-x-1">
                        @php
                        $abas = [
                            'basicos'       => ['label' => 'Dados Básicos',  'icon' => '🏠'],
                            'pessoas'       => ['label' => 'Pessoas',        'icon' => '👥', 'badge' => $imovel->pessoas->count() + ($imovel->lote->pessoas->count() ?? 0)],
                            'identificacao' => ['label' => 'Identificação',  'icon' => '📋'],
                            'endereco'      => ['label' => 'Endereço',       'icon' => '📍'],
                            'financeiro'    => ['label' => 'Financeiro',     'icon' => '💰'],
                            'midias'        => ['label' => 'Mídias',         'icon' => '🖼️', 'badge' => $imovel->midias->count()],
                            'documentos'    => ['label' => 'Documentos',     'icon' => '📁', 'badge' => $imovel->documentos->count()],
                            'historico'     => ['label' => 'Histórico',      'icon' => '📅', 'badge' => $imovel->historicos->count()],
                            'sync'          => ['label' => 'Sync Site',      'icon' => '🔄'],
                        ];
                        @endphp
                        @foreach($abas as $key => $info)
                        <button type="button" @click="aba = '{{ $key }}'"
                                :class="aba === '{{ $key }}' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm transition-colors flex items-center gap-1.5">
                            {{ $info['icon'] }} {{ $info['label'] }}
                            @if(!empty($info['badge']) && $info['badge'] > 0)
                            <span class="bg-indigo-100 text-indigo-600 text-xs rounded-full px-1.5 py-0.5 leading-none">{{ $info['badge'] }}</span>
                            @endif
                        </button>
                        @endforeach
                    </nav>
                </div>

                {{-- ABA: DADOS BÁSICOS --}}
                <div x-show="aba === 'basicos'" x-cloak>
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        @if($imovel->descricao)
                        <p class="text-sm text-gray-700 mb-5 leading-relaxed">{{ $imovel->descricao }}</p>
                        @endif

                        <dl class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-4 text-sm">
                            @foreach([
                                'Tipo'             => $imovel->tipoLabel(),
                                'Área Total'       => $imovel->area_total ? number_format($imovel->area_total, 2, ',', '.') . ' m²' : null,
                                'Área Construída'  => $imovel->area_construida ? number_format($imovel->area_construida, 2, ',', '.') . ' m²' : null,
                                'Área Privativa'   => $imovel->area_privativa ? number_format($imovel->area_privativa, 2, ',', '.') . ' m²' : null,
                                'Quartos'          => $imovel->quartos,
                                'Suítes'           => $imovel->suites,
                                'Banheiros'        => $imovel->banheiros,
                                'Vagas de Garagem' => $imovel->vagas_garagem,
                                'Andares'          => $imovel->andares,
                                'Ano de Construção'=> $imovel->ano_construcao,
                                'Padrão'           => $imovel->padrao_acabamento ? ucfirst($imovel->padrao_acabamento) : null,
                                'Cond. Fechado'    => $imovel->condominio_fechado !== null ? ($imovel->condominio_fechado ? 'Sim' : 'Não') : null,
                            ] as $label => $value)
                            @if($value !== null && $value !== '')
                            <div>
                                <dt class="text-gray-400 text-xs">{{ $label }}</dt>
                                <dd class="font-medium text-gray-900 mt-0.5">{{ $value }}</dd>
                            </div>
                            @endif
                            @endforeach
                        </dl>

                        @if($imovel->observacoes)
                        <div class="mt-5 pt-4 border-t border-gray-100">
                            <dt class="text-xs text-gray-400 mb-1">Observações</dt>
                            <dd class="text-sm text-gray-700 leading-relaxed">{{ $imovel->observacoes }}</dd>
                        </div>
                        @endif

                        @if(!$imovel->tipo && !$imovel->area_total && !$imovel->quartos)
                        <p class="text-center text-gray-400 text-sm py-6">Nenhum dado básico informado ainda.</p>
                        @endif
                    </div>
                </div>

                {{-- ABA: PESSOAS --}}
                <div x-show="aba === 'pessoas'" x-cloak>

                    @php
                        $pessoasLote   = $imovel->lote->pessoas ?? collect();
                        $pessoasImovel = $imovel->pessoas;

                        // IDs já no imóvel para não duplicar no bloco do lote
                        $idsImovel = $pessoasImovel->pluck('id')->toArray();

                        $papelLabels = [
                            'proprietario' => '👑 Proprietário',
                            'comprador'    => '🛒 Comprador',
                            'interessado'  => '🔍 Interessado',
                            'locatario'    => '🏠 Locatário',
                        ];
                        $papelCores = [
                            'proprietario' => 'green',
                            'comprador'    => 'blue',
                            'interessado'  => 'yellow',
                            'locatario'    => 'purple',
                        ];
                    @endphp

                    {{-- Pessoas vinculadas diretamente ao Imóvel --}}
                    @include('partials._vinculos_pessoa', [
                        'pessoas'          => $pessoasImovel,
                        'papeis'           => ['proprietario' => '👑 Proprietário', 'locatario' => '🏠 Locatário'],
                        'storeRouteName'   => 'imoveis.pessoas.store',
                        'destroyRouteName' => 'imoveis.pessoas.destroy',
                        'storeModel'       => $imovel,
                        'titulo'           => 'Pessoas Vinculadas ao Imóvel',
                    ])

                    {{-- Pessoas vinculadas ao Lote correspondente (somente leitura) --}}
                    @if($pessoasLote->isNotEmpty())
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden mt-4">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h3 class="text-base font-semibold text-gray-900">
                                🏗️ Pessoas Vinculadas ao Lote
                                <span class="ml-1 text-sm font-normal text-gray-500">({{ $pessoasLote->count() }})</span>
                            </h3>
                            <a href="{{ route('lotes.show', $imovel->lote) }}"
                               class="text-xs text-indigo-600 hover:text-indigo-800">
                                Gerenciar no Lote →
                            </a>
                        </div>
                        <ul class="divide-y divide-gray-100">
                            @foreach($pessoasLote as $pessoa)
                            @php
                                $papel = $pessoa->pivot->papel;
                                $cor   = $papelCores[$papel] ?? 'gray';
                                $jaNoImovel = in_array($pessoa->id, $idsImovel);
                            @endphp
                            <li class="flex items-center gap-4 px-6 py-3 {{ $jaNoImovel ? 'opacity-50' : '' }}">
                                <div class="w-9 h-9 rounded-full bg-{{ $cor }}-100 flex items-center justify-center shrink-0 text-sm font-bold text-{{ $cor }}-700">
                                    {{ mb_substr($pessoa->nome, 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="{{ route('pessoas.show', $pessoa) }}"
                                           class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                                            {{ $pessoa->nome }}
                                        </a>
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $cor }}-100 text-{{ $cor }}-700 font-medium">
                                            {{ $papelLabels[$papel] ?? $papel }}
                                        </span>
                                        @if($jaNoImovel)
                                        <span class="text-xs text-gray-400 italic">já no imóvel</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400 flex flex-wrap gap-2 mt-0.5">
                                        @if($pessoa->celular)<span>📱 {{ $pessoa->celular }}</span>@endif
                                        @if($pessoa->email)<span>✉ {{ $pessoa->email }}</span>@endif
                                        @if($pessoa->pivot->data_vinculo)
                                        <span>📅 {{ \Carbon\Carbon::parse($pessoa->pivot->data_vinculo)->format('d/m/Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Histórico de vínculos (todas as ações: vinculado e desvinculado) --}}
                    @php $phist = $imovel->pessoaHistoricos()->with('pessoa')->get(); @endphp
                    @if($phist->isNotEmpty())
                    <div class="mt-4 bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-3 border-b border-gray-100 bg-gray-50">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">📋 Histórico Completo de Vínculos</p>
                        </div>
                        <table class="w-full text-sm divide-y divide-gray-100">
                            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                                <tr>
                                    <th class="px-4 py-2 text-left">Pessoa</th>
                                    <th class="px-4 py-2 text-left">Papel</th>
                                    <th class="px-4 py-2 text-left">Ação</th>
                                    <th class="px-4 py-2 text-left">Valor na época</th>
                                    <th class="px-4 py-2 text-left">Data vínculo</th>
                                    <th class="px-4 py-2 text-left">Registrado em</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($phist as $h)
                                @php $acaoCor = $h->acao === 'vinculado' ? 'green' : 'gray'; @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2">
                                        <a href="{{ route('pessoas.show', $h->pessoa) }}"
                                           class="font-medium text-gray-900 hover:text-indigo-600">
                                            {{ $h->pessoa->nome }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-2 text-gray-600">{{ $h->papelLabel() }}</td>
                                    <td class="px-4 py-2">
                                        <span class="inline-block text-xs px-2 py-0.5 rounded-full
                                            bg-{{ $acaoCor }}-100 text-{{ $acaoCor }}-700 font-medium capitalize">
                                            {{ $h->acao }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-gray-600">
                                        {{ $h->valor_imovel ? 'R$ ' . number_format($h->valor_imovel, 2, ',', '.') : '—' }}
                                    </td>
                                    <td class="px-4 py-2 text-gray-500 text-xs">
                                        {{ $h->data_vinculo?->format('d/m/Y') ?? '—' }}
                                    </td>
                                    <td class="px-4 py-2 text-gray-400 text-xs">
                                        {{ $h->created_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                </div>

                {{-- ABA: IDENTIFICAÇÃO --}}
                <div x-show="aba === 'identificacao'" x-cloak>
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        @php
                        $campos = [
                            'Matrícula'          => $imovel->matricula_imovel,
                            'Inscrição Municipal'=> $imovel->inscricao_municipal,
                            'Cartório'           => $imovel->cartorio,
                            'Nº Escritura'       => $imovel->numero_escritura,
                            'Livro'              => $imovel->livro_escritura,
                            'Folha'              => $imovel->folha_escritura,
                        ];
                        $temDados = collect($campos)->filter()->isNotEmpty();
                        @endphp
                        @if($temDados)
                        <dl class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-4 text-sm">
                            @foreach($campos as $label => $value)
                            @if($value)
                            <div>
                                <dt class="text-gray-400 text-xs">{{ $label }}</dt>
                                <dd class="font-medium text-gray-900 mt-0.5">{{ $value }}</dd>
                            </div>
                            @endif
                            @endforeach
                        </dl>
                        @else
                        <p class="text-center text-gray-400 text-sm py-8">Nenhum dado de identificação cadastrado ainda.</p>
                        @endif
                    </div>
                </div>

                {{-- ABA: ENDEREÇO --}}
                <div x-show="aba === 'endereco'" x-cloak>
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        @php
                        $temEndereco = $imovel->logradouro || $imovel->cidade || $imovel->cep;
                        @endphp
                        @if($temEndereco)
                        <p class="text-sm text-gray-800 mb-4">
                            @if($imovel->logradouro){{ $imovel->logradouro }}@if($imovel->numero_endereco), {{ $imovel->numero_endereco }}@endif
                            @endif
                            @if($imovel->complemento) — {{ $imovel->complemento }}@endif
                            @if($imovel->bairro)<br>{{ $imovel->bairro }}@endif
                            @if($imovel->cidade || $imovel->estado)<br>{{ $imovel->cidade }}@if($imovel->estado) / {{ strtoupper($imovel->estado) }}@endif
                            @endif
                            @if($imovel->cep)<br>CEP: {{ $imovel->cep }}@endif
                        </p>
                        @if($imovel->latitude && $imovel->longitude)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-xs text-gray-400 mb-2">📍 Coordenadas GPS</p>
                            <p class="text-sm font-mono text-gray-700">{{ $imovel->latitude }}, {{ $imovel->longitude }}</p>
                            <a href="https://maps.google.com/?q={{ $imovel->latitude }},{{ $imovel->longitude }}"
                               target="_blank"
                               class="inline-block mt-2 text-xs text-indigo-600 hover:text-indigo-800">
                               Abrir no Google Maps ↗
                            </a>
                        </div>
                        @endif
                        @else
                        <p class="text-center text-gray-400 text-sm py-8">Nenhum endereço cadastrado ainda.</p>
                        @endif
                    </div>
                </div>

                {{-- ABA: FINANCEIRO --}}
                <div x-show="aba === 'financeiro'" x-cloak>
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        @php
                        $temFinanceiro = $imovel->valor_venal || $imovel->valor_mercado || $imovel->valor_iptu_anual;
                        @endphp
                        @if($temFinanceiro)
                        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm mb-6">
                            @if($imovel->valor_venal)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <dt class="text-xs text-gray-400 mb-1">Valor Venal</dt>
                                <dd class="text-lg font-bold text-gray-900">R$ {{ number_format($imovel->valor_venal, 2, ',', '.') }}</dd>
                            </div>
                            @endif
                            @if($imovel->valor_mercado)
                            <div class="bg-indigo-50 rounded-lg p-4">
                                <dt class="text-xs text-gray-400 mb-1">Valor de Mercado</dt>
                                <dd class="text-lg font-bold text-indigo-700">R$ {{ number_format($imovel->valor_mercado, 2, ',', '.') }}</dd>
                            </div>
                            @endif
                            @if($imovel->valor_iptu_anual)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <dt class="text-xs text-gray-400 mb-1">IPTU Anual</dt>
                                <dd class="text-lg font-bold text-gray-700">R$ {{ number_format($imovel->valor_iptu_anual, 2, ',', '.') }}</dd>
                                <p class="text-xs text-gray-400 mt-0.5">≈ R$ {{ number_format($imovel->valor_iptu_anual / 12, 2, ',', '.') }}/mês</p>
                            </div>
                            @endif
                        </dl>
                        @if($imovel->data_ultima_avaliacao)
                        <p class="text-xs text-gray-400">Última avaliação: {{ $imovel->data_ultima_avaliacao->format('d/m/Y') }}</p>
                        @endif
                        @else
                        <p class="text-center text-gray-400 text-sm py-8">Nenhum dado financeiro cadastrado ainda.</p>
                        @endif

                        {{-- Histórico de vendas resumido --}}
                        @if($imovel->historicos->count() > 0)
                        @php $vendas = $imovel->historicos->whereIn('tipo', ['venda','compra'])->sortBy('data'); @endphp
                        @if($vendas->count() > 1)
                        <div class="mt-6 pt-5 border-t border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">📈 Evolução de Valor (Transações)</p>
                            <div class="flex items-end gap-2 overflow-x-auto pb-2">
                                @foreach($vendas as $v)
                                @if($v->valor)
                                <div class="text-center min-w-20 flex-shrink-0">
                                    <div class="text-xs font-semibold text-indigo-700">R$ {{ number_format($v->valor/1000, 0) }}k</div>
                                    <div class="h-2 bg-indigo-400 rounded mt-1" style="width: 100%"></div>
                                    <div class="text-xs text-gray-400 mt-1">{{ $v->data?->format('m/Y') }}</div>
                                </div>
                                @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>

                {{-- ABA: MÍDIAS --}}
                <div x-show="aba === 'midias'" x-cloak
                     x-data="{
                         lightbox: false,
                         lightboxSrc: '',
                         lightboxTipo: '',
                         lightboxTitulo: '',
                         open(src, tipo, titulo) { this.lightboxSrc = src; this.lightboxTipo = tipo; this.lightboxTitulo = titulo; this.lightbox = true; },
                         dragover: false
                     }">

                    {{-- Lightbox --}}
                    <div x-show="lightbox" x-cloak
                         @keydown.escape.window="lightbox = false"
                         class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4"
                         @click.self="lightbox = false">
                        <div class="relative max-w-4xl w-full">
                            <button @click="lightbox = false"
                                    class="absolute -top-10 right-0 text-white text-sm hover:text-gray-300">
                                ✕ Fechar
                            </button>
                            <template x-if="lightboxTipo === 'imagem'">
                                <img :src="lightboxSrc" :alt="lightboxTitulo"
                                     class="w-full max-h-[80vh] object-contain rounded-lg">
                            </template>
                            <template x-if="lightboxTipo === 'video'">
                                <video :src="lightboxSrc" controls autoplay
                                       class="w-full max-h-[80vh] rounded-lg bg-black"></video>
                            </template>
                            <template x-if="lightboxTipo === 'pdf'">
                                <iframe :src="lightboxSrc" class="w-full h-[80vh] rounded-lg bg-white"></iframe>
                            </template>
                            <p x-show="lightboxTitulo" x-text="lightboxTitulo"
                               class="text-center text-white text-sm mt-3 opacity-80"></p>
                        </div>
                    </div>

                    {{-- Galeria --}}
                    @if($imovel->midias->count() > 0)
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-4">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-0.5 bg-gray-100">
                            @foreach($imovel->midias as $midia)
                            <div class="relative group bg-white">
                                {{-- Preview clicável --}}
                                <button type="button"
                                        @click="open('{{ $midia->url() }}', '{{ $midia->tipo }}', '{{ addslashes($midia->titulo ?? $midia->nome_original) }}')"
                                        class="w-full block">
                                    @if($midia->tipo === 'imagem')
                                    <img src="{{ $midia->url() }}"
                                         alt="{{ $midia->titulo ?? $midia->nome_original }}"
                                         class="w-full h-40 object-cover">
                                    @elseif($midia->tipo === 'video')
                                    <div class="w-full h-40 bg-gray-900 flex flex-col items-center justify-center gap-2">
                                        <span class="text-4xl">▶️</span>
                                        <span class="text-xs text-gray-300">{{ $midia->nome_original }}</span>
                                    </div>
                                    @else
                                    <div class="w-full h-40 bg-gray-50 flex flex-col items-center justify-center gap-2 border border-gray-200">
                                        <span class="text-4xl">📄</span>
                                        <span class="text-xs text-gray-500 px-2 text-center truncate w-full">{{ $midia->nome_original }}</span>
                                    </div>
                                    @endif
                                </button>

                                {{-- Badge capa --}}
                                @if($midia->capa)
                                <span class="absolute top-2 left-2 bg-yellow-400 text-yellow-900 text-xs px-1.5 py-0.5 rounded font-semibold shadow">
                                    ★ Capa
                                </span>
                                @endif

                                {{-- Overlay de ações (admin) --}}
                                @if(auth()->user()->isAdmin())
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all flex items-end justify-between p-2 pointer-events-none opacity-0 group-hover:opacity-100">
                                    <div class="flex gap-1 pointer-events-auto">
                                        @if($midia->tipo === 'imagem' && !$midia->capa)
                                        <form action="{{ route('imoveis.midias.capa', $midia) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" title="Definir como capa"
                                                    class="bg-yellow-400 text-yellow-900 text-xs px-2 py-1 rounded font-semibold hover:bg-yellow-300">
                                                ★
                                            </button>
                                        </form>
                                        @endif
                                        <a href="{{ $midia->url() }}" download="{{ $midia->nome_original }}"
                                           class="bg-white/90 text-gray-700 text-xs px-2 py-1 rounded hover:bg-white pointer-events-auto">
                                            ⬇
                                        </a>
                                    </div>
                                    <form action="{{ route('imoveis.midias.destroy', $midia) }}" method="POST"
                                          onsubmit="return confirm('Remover esta mídia?')"
                                          class="pointer-events-auto">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="bg-red-500 text-white text-xs px-2 py-1 rounded hover:bg-red-600">
                                            ✕
                                        </button>
                                    </form>
                                </div>
                                @endif

                                {{-- Título / tamanho --}}
                                <div class="px-2 py-1.5 bg-white border-t border-gray-100">
                                    <p class="text-xs text-gray-600 truncate">
                                        {{ $midia->titulo ?: $midia->nome_original }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $midia->tamanhoFormatado() }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Upload (admin) --}}
                    @if(auth()->user()->isAdmin())
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-5 py-3 border-b border-gray-100">
                            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Enviar Mídias</p>
                        </div>
                        <form action="{{ route('imoveis.midias.store', $imovel) }}" method="POST"
                              enctype="multipart/form-data" id="form-midia-upload">
                            @csrf
                            <div class="p-5">
                                {{-- Drop zone --}}
                                <label for="input-midias"
                                       @dragover.prevent="dragover = true"
                                       @dragleave.prevent="dragover = false"
                                       @drop.prevent="dragover = false; $refs.inputMidias.files = $event.dataTransfer.files; $refs.inputMidias.dispatchEvent(new Event('change'))"
                                       :class="dragover ? 'border-indigo-400 bg-indigo-50' : 'border-gray-300 bg-gray-50 hover:bg-gray-100'"
                                       class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed rounded-lg cursor-pointer transition-colors">
                                    <div class="text-center pointer-events-none">
                                        <p class="text-3xl mb-1">📎</p>
                                        <p class="text-sm text-gray-600 font-medium">Arraste arquivos ou clique para selecionar</p>
                                        <p class="text-xs text-gray-400 mt-1">Imagens (JPG, PNG, WEBP), Vídeos (MP4, MOV), PDF — máx. 100MB por arquivo</p>
                                    </div>
                                    <input id="input-midias" x-ref="inputMidias" type="file" name="arquivos[]"
                                           multiple accept="image/*,video/*,.pdf"
                                           class="hidden"
                                           @change="
                                               const names = Array.from($event.target.files).map(f => f.name).join(', ');
                                               $refs.fileNames.textContent = names || 'Nenhum arquivo selecionado';
                                           ">
                                </label>
                                <p x-ref="fileNames" class="text-xs text-gray-500 mt-2 text-center">Nenhum arquivo selecionado</p>
                            </div>
                            <div class="px-5 pb-4 flex justify-end">
                                <button type="submit"
                                        class="px-5 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-700">
                                    Enviar Mídias
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif

                    @if($imovel->midias->count() === 0 && !auth()->user()->isAdmin())
                    <div class="bg-white shadow-sm rounded-lg p-12 text-center">
                        <p class="text-5xl mb-3">🖼️</p>
                        <p class="text-gray-500 text-sm">Nenhuma mídia cadastrada ainda.</p>
                    </div>
                    @endif

                </div>

                {{-- ABA: DOCUMENTOS --}}
                <div x-show="aba === 'documentos'" x-cloak
                     x-data="{ formAberto: false }">

                    {{-- Lista de documentos --}}
                    @if($imovel->documentos->count() > 0)
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-4">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Documento</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Tipo</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Emissão / Vencimento</th>
                                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($imovel->documentos as $doc)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            {{-- Miniatura clicável para imagens, ícone para outros --}}
                                            @if($doc->isImage())
                                            <img src="{{ Storage::url($doc->path) }}"
                                                 alt="{{ $doc->nome_original }}"
                                                 class="h-12 w-12 object-cover rounded border border-gray-200 shrink-0 cursor-pointer"
                                                 @click="$dispatch('open-doc-viewer', {
                                                     url: '{{ route('imoveis.documentos.visualizar', $doc) }}',
                                                     downloadUrl: '{{ route('imoveis.documentos.download', $doc) }}',
                                                     tipo: 'imagem',
                                                     nome: '{{ addslashes($doc->nome_original) }}'
                                                 })">
                                            @else
                                            <span class="text-xl shrink-0">{{ $doc->icone() }}</span>
                                            @endif

                                            <div class="min-w-0">
                                                <p class="font-medium text-gray-900 truncate">
                                                    {{ $doc->titulo ?: $doc->nome_original }}
                                                </p>
                                                @if($doc->numero_documento)
                                                <p class="text-xs text-gray-400">Nº {{ $doc->numero_documento }}</p>
                                                @endif
                                                @if($doc->orgao_emissor)
                                                <p class="text-xs text-gray-400">{{ $doc->orgao_emissor }}</p>
                                                @endif
                                                <p class="text-xs text-gray-300">{{ $doc->tamanhoFormatado() }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 hidden sm:table-cell">
                                        <span class="inline-block bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">
                                            {{ $doc->tipoLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 hidden md:table-cell text-xs text-gray-500">
                                        @if($doc->data_emissao)
                                        <span>{{ $doc->data_emissao->format('d/m/Y') }}</span>
                                        @endif
                                        @if($doc->data_vencimento)
                                        <br>
                                        <span class="{{ $doc->vencido() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                            Vence: {{ $doc->data_vencimento->format('d/m/Y') }}
                                            @if($doc->vencido()) ⚠️@endif
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            {{-- Botão Ver (PDF ou imagem) --}}
                                            @if($doc->isPdf() || $doc->isImage())
                                            <button type="button"
                                                    @click="$dispatch('open-doc-viewer', {
                                                        url: '{{ route('imoveis.documentos.visualizar', $doc) }}',
                                                        downloadUrl: '{{ route('imoveis.documentos.download', $doc) }}',
                                                        tipo: '{{ $doc->isPdf() ? 'pdf' : 'imagem' }}',
                                                        nome: '{{ addslashes($doc->titulo ?: $doc->nome_original) }}'
                                                    })"
                                                    class="text-emerald-600 hover:text-emerald-800 text-xs font-medium">
                                                👁 Ver
                                            </button>
                                            @endif
                                            <a href="{{ route('imoveis.documentos.download', $doc) }}"
                                               class="text-indigo-600 hover:text-indigo-900 text-xs font-medium">
                                                ⬇ Baixar
                                            </a>
                                            @if(auth()->user()->isAdmin())
                                            <form action="{{ route('imoveis.documentos.destroy', $doc) }}" method="POST"
                                                  onsubmit="return confirm('Remover este documento?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-400 hover:text-red-700 text-xs font-medium">
                                                    ✕
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="bg-white shadow-sm rounded-lg p-10 text-center mb-4">
                        <div class="text-5xl mb-3">📁</div>
                        <p class="text-gray-500 text-sm">Nenhum documento cadastrado ainda.</p>
                    </div>
                    @endif

                    {{-- Upload (admin) --}}
                    @if(auth()->user()->isAdmin())
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <button type="button" @click="formAberto = !formAberto"
                                class="w-full flex items-center justify-between px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                            <span>+ Adicionar Documento</span>
                            <span x-text="formAberto ? '▲' : '▼'" class="text-gray-400 text-xs"></span>
                        </button>

                        <div x-show="formAberto" x-transition class="border-t border-gray-100">
                            <form action="{{ route('imoveis.documentos.store', $imovel) }}" method="POST"
                                  enctype="multipart/form-data" class="p-5 space-y-4">
                                @csrf

                                {{-- Arquivo --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Arquivo <span class="text-red-400">*</span></label>
                                    <input type="file" name="arquivo"
                                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx"
                                           class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG, DOC, XLS — máx. 20MB</p>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    {{-- Tipo --}}
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Tipo</label>
                                        <select name="tipo" class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Selecione...</option>
                                            @foreach([
                                                'matricula'         => 'Matrícula',
                                                'escritura'         => 'Escritura',
                                                'iptu'              => 'IPTU',
                                                'certidao_negativa' => 'Certidão Negativa',
                                                'habite_se'         => 'Habite-se',
                                                'planta'            => 'Planta Baixa',
                                                'condominio'        => 'Doc. Condomínio',
                                                'procuracao'        => 'Procuração',
                                                'contrato'          => 'Contrato',
                                                'outro'             => 'Outro',
                                            ] as $val => $lbl)
                                            <option value="{{ $val }}">{{ $lbl }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Título --}}
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Título / Descrição</label>
                                        <input type="text" name="titulo" placeholder="Ex: Escritura de Compra e Venda 2023"
                                               class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    {{-- Número do documento --}}
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Nº do Documento</label>
                                        <input type="text" name="numero_documento" placeholder="Ex: 12345/2023"
                                               class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    {{-- Órgão emissor --}}
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Órgão Emissor</label>
                                        <input type="text" name="orgao_emissor" placeholder="Ex: Cartório do 1º Ofício"
                                               class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    {{-- Data de emissão --}}
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Data de Emissão</label>
                                        <input type="date" name="data_emissao"
                                               class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    {{-- Data de vencimento --}}
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Data de Vencimento</label>
                                        <input type="date" name="data_vencimento"
                                               class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                </div>

                                <div class="flex justify-end pt-2">
                                    <button type="submit"
                                            class="px-5 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-700">
                                        Enviar Documento
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif

                </div>

                {{-- ABA: HISTÓRICO --}}
                <div x-show="aba === 'historico'" x-cloak
                     x-data="{ formAberto: false, editandoId: null }">

                    @php
                        $historicos = $imovel->historicos->sortBy('data');
                        $valorizacao = $imovel->historicos
                            ->whereIn('tipo', ['compra','venda','avaliacao','permuta'])
                            ->whereNotNull('valor')
                            ->sortBy('data')
                            ->values();
                    @endphp

                    {{-- Gráfico de valorização --}}
                    @if($valorizacao->count() >= 2)
                    <div class="bg-white shadow-sm rounded-lg p-5 mb-4" id="chart-valorizacao">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">📈 Evolução de Valor</p>
                        @php
                            $maxVal = $valorizacao->max('valor');
                            $minVal = $valorizacao->min('valor');
                            $range  = $maxVal - $minVal ?: 1;
                            $primeiro = $valorizacao->first()->valor;
                            $ultimo   = $valorizacao->last()->valor;
                            $variacao = $primeiro > 0 ? (($ultimo - $primeiro) / $primeiro) * 100 : 0;
                        @endphp
                        <div class="flex items-end gap-3 overflow-x-auto pb-2" style="min-height: 100px;">
                            @foreach($valorizacao as $v)
                            @php
                                $pct = $range > 0 ? (($v->valor - $minVal) / $range) : 0.5;
                                $barH = max(20, intval($pct * 80)) + 20;
                                $cor = in_array($v->tipo, ['venda','avaliacao']) ? 'indigo' : 'blue';
                            @endphp
                            <div class="flex flex-col items-center gap-1 min-w-16 flex-shrink-0">
                                <span class="text-xs font-semibold text-{{ $cor }}-700 whitespace-nowrap">
                                    R$ {{ number_format($v->valor / 1000, 0, ',', '.') }}k
                                </span>
                                <div class="w-12 bg-{{ $cor }}-400 rounded-t hover:bg-{{ $cor }}-500 transition-colors"
                                     style="height: {{ $barH }}px"
                                     title="{{ $v->tipoLabel() }}: R$ {{ number_format($v->valor, 2, ',', '.') }}"></div>
                                <span class="text-xs text-gray-400 whitespace-nowrap">{{ $v->data?->format('m/Y') }}</span>
                                <span class="text-xs text-gray-300">{{ $v->tipoLabel() }}</span>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-100 flex gap-6 text-xs text-gray-500">
                            <span>Primeiro registro: <strong class="text-gray-700">R$ {{ number_format($primeiro, 2, ',', '.') }}</strong></span>
                            <span>Último registro: <strong class="text-gray-700">R$ {{ number_format($ultimo, 2, ',', '.') }}</strong></span>
                            <span class="{{ $variacao >= 0 ? 'text-green-600' : 'text-red-500' }} font-semibold">
                                {{ $variacao >= 0 ? '▲' : '▼' }} {{ number_format(abs($variacao), 1) }}% no período
                            </span>
                        </div>
                    </div>
                    @endif

                    {{-- Timeline --}}
                    @if($historicos->count() > 0)
                    <div class="bg-white shadow-sm rounded-lg p-6 mb-4">
                        <ol class="relative border-l-2 border-gray-200 ml-3 space-y-8">
                            @foreach($historicos as $hist)
                            @php $cor = $hist->tipoCor(); @endphp
                            <li class="ml-7">
                                <span class="absolute -left-[17px] flex items-center justify-center w-8 h-8 bg-white border-2 border-{{ $cor }}-300 rounded-full text-base shadow-sm">
                                    {{ $hist->tipoIcone() }}
                                </span>

                                <div class="bg-gray-50 border border-gray-100 rounded-lg p-4">
                                    {{-- Cabeçalho --}}
                                    <div class="flex justify-between items-start flex-wrap gap-2 mb-2">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="inline-block bg-{{ $cor }}-100 text-{{ $cor }}-800 text-xs font-semibold px-2 py-0.5 rounded-full">
                                                {{ $hist->tipoLabel() }}
                                            </span>
                                            @if($hist->valor)
                                            <span class="text-sm font-bold text-indigo-700">
                                                R$ {{ number_format($hist->valor, 2, ',', '.') }}
                                            </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-3">
                                            @if($hist->data)
                                            <span class="text-xs text-gray-400">{{ $hist->data->format('d/m/Y') }}</span>
                                            @endif
                                            @if(auth()->user()->isAdmin())
                                            <div class="flex gap-2">
                                                <button type="button"
                                                        @click="editandoId = editandoId === {{ $hist->id }} ? null : {{ $hist->id }}"
                                                        class="text-xs text-indigo-500 hover:text-indigo-700 font-medium">
                                                    ✏️ Editar
                                                </button>
                                                <form action="{{ route('imoveis.historicos.destroy', $hist) }}" method="POST"
                                                      onsubmit="return confirm('Remover este evento do histórico?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-xs text-red-400 hover:text-red-600 font-medium">
                                                        ✕
                                                    </button>
                                                </form>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Proprietários --}}
                                    @if($hist->proprietario_anterior || $hist->proprietario_atual)
                                    <div class="flex items-center gap-2 text-xs text-gray-600 mb-2 flex-wrap">
                                        @if($hist->proprietario_anterior)
                                        <span class="bg-white border border-gray-200 rounded px-2 py-0.5">
                                            {{ $hist->proprietario_anterior }}
                                            @if($hist->cpf_cnpj_anterior) <span class="text-gray-400">· {{ $hist->cpf_cnpj_anterior }}</span>@endif
                                        </span>
                                        @endif
                                        @if($hist->proprietario_anterior && $hist->proprietario_atual)
                                        <span class="text-gray-400">→</span>
                                        @endif
                                        @if($hist->proprietario_atual)
                                        <span class="bg-white border border-gray-200 rounded px-2 py-0.5">
                                            {{ $hist->proprietario_atual }}
                                            @if($hist->cpf_cnpj_atual) <span class="text-gray-400">· {{ $hist->cpf_cnpj_atual }}</span>@endif
                                        </span>
                                        @endif
                                    </div>
                                    @endif

                                    {{-- Descrição --}}
                                    @if($hist->descricao)
                                    <p class="text-sm text-gray-700 mb-2">{{ $hist->descricao }}</p>
                                    @endif

                                    {{-- Metadados --}}
                                    @if($hist->cartorio || $hist->numero_escritura || $hist->numero_registro || $hist->corretor)
                                    <div class="flex flex-wrap gap-3 text-xs text-gray-400 border-t border-gray-200 pt-2 mt-2">
                                        @if($hist->cartorio)<span>🏛 {{ $hist->cartorio }}</span>@endif
                                        @if($hist->numero_escritura)<span>📜 Escritura nº {{ $hist->numero_escritura }}</span>@endif
                                        @if($hist->numero_registro)<span>📋 Reg. {{ $hist->numero_registro }}</span>@endif
                                        @if($hist->corretor)<span>🤝 {{ $hist->corretor }}</span>@endif
                                    </div>
                                    @endif

                                    @if($hist->observacoes)
                                    <p class="text-xs text-gray-400 italic mt-2 border-t border-gray-200 pt-2">{{ $hist->observacoes }}</p>
                                    @endif

                                    {{-- Form de edição inline --}}
                                    @if(auth()->user()->isAdmin())
                                    <div x-show="editandoId === {{ $hist->id }}" x-transition class="mt-4 pt-4 border-t border-gray-200">
                                        <form action="{{ route('imoveis.historicos.update', $hist) }}" method="POST" class="space-y-3">
                                            @csrf @method('PUT')
                                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Tipo</label>
                                                    <select name="tipo" class="block w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                        @foreach(['compra'=>'Compra','venda'=>'Venda','avaliacao'=>'Avaliação','reforma'=>'Reforma','locacao'=>'Locação','permuta'=>'Permuta','inventario'=>'Inventário','outro'=>'Outro'] as $v => $l)
                                                        <option value="{{ $v }}" @selected($hist->tipo === $v)>{{ $l }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Data</label>
                                                    <input type="date" name="data" value="{{ $hist->data?->format('Y-m-d') }}"
                                                           class="block w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Valor (R$)</label>
                                                    <input type="number" name="valor" step="0.01" value="{{ $hist->valor }}"
                                                           placeholder="0,00"
                                                           class="block w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Proprietário Anterior</label>
                                                    <input type="text" name="proprietario_anterior" value="{{ $hist->proprietario_anterior }}"
                                                           class="block w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">CPF/CNPJ Anterior</label>
                                                    <input type="text" name="cpf_cnpj_anterior" value="{{ $hist->cpf_cnpj_anterior }}"
                                                           class="block w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Proprietário Atual</label>
                                                    <input type="text" name="proprietario_atual" value="{{ $hist->proprietario_atual }}"
                                                           class="block w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">CPF/CNPJ Atual</label>
                                                    <input type="text" name="cpf_cnpj_atual" value="{{ $hist->cpf_cnpj_atual }}"
                                                           class="block w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Cartório</label>
                                                    <input type="text" name="cartorio" value="{{ $hist->cartorio }}"
                                                           class="block w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Nº Escritura</label>
                                                    <input type="text" name="numero_escritura" value="{{ $hist->numero_escritura }}"
                                                           class="block w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Nº Registro</label>
                                                    <input type="text" name="numero_registro" value="{{ $hist->numero_registro }}"
                                                           class="block w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Corretor</label>
                                                    <input type="text" name="corretor" value="{{ $hist->corretor }}"
                                                           class="block w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Descrição</label>
                                                <textarea name="descricao" rows="2"
                                                          class="block w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $hist->descricao }}</textarea>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Observações</label>
                                                <textarea name="observacoes" rows="2"
                                                          class="block w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $hist->observacoes }}</textarea>
                                            </div>
                                            <div class="flex justify-end gap-2">
                                                <button type="button" @click="editandoId = null"
                                                        class="px-3 py-1.5 border border-gray-300 rounded-md text-xs text-gray-600 hover:bg-gray-50">
                                                    Cancelar
                                                </button>
                                                <button type="submit"
                                                        class="px-4 py-1.5 bg-indigo-600 text-white rounded-md text-xs font-semibold hover:bg-indigo-700">
                                                    Salvar
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    @endif
                                </div>
                            </li>
                            @endforeach
                        </ol>
                    </div>
                    @else
                    <div class="bg-white shadow-sm rounded-lg p-10 text-center mb-4">
                        <div class="text-5xl mb-3">📅</div>
                        <p class="text-gray-500 text-sm">Nenhum evento histórico registrado ainda.</p>
                    </div>
                    @endif

                    {{-- Formulário de novo evento (admin) --}}
                    @if(auth()->user()->isAdmin())
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <button type="button" @click="formAberto = !formAberto"
                                class="w-full flex items-center justify-between px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                            <span>+ Registrar Evento</span>
                            <span x-text="formAberto ? '▲' : '▼'" class="text-gray-400 text-xs"></span>
                        </button>

                        <div x-show="formAberto" x-transition class="border-t border-gray-100">
                            <form action="{{ route('imoveis.historicos.store', $imovel) }}" method="POST" class="p-5 space-y-4">
                                @csrf

                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Tipo</label>
                                        <select name="tipo" class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Selecione...</option>
                                            @foreach(['compra'=>'Compra','venda'=>'Venda','avaliacao'=>'Avaliação','reforma'=>'Reforma','locacao'=>'Locação','permuta'=>'Permuta','inventario'=>'Inventário','outro'=>'Outro'] as $v => $l)
                                            <option value="{{ $v }}">{{ $l }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Data do Evento</label>
                                        <input type="date" name="data"
                                               class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Valor (R$)</label>
                                        <input type="number" name="valor" step="0.01" min="0" placeholder="0,00"
                                               class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Proprietário Anterior</label>
                                        <input type="text" name="proprietario_anterior" placeholder="Nome completo"
                                               class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">CPF/CNPJ Anterior</label>
                                        <input type="text" name="cpf_cnpj_anterior" placeholder="000.000.000-00"
                                               class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Proprietário Atual</label>
                                        <input type="text" name="proprietario_atual" placeholder="Nome completo"
                                               class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">CPF/CNPJ Atual</label>
                                        <input type="text" name="cpf_cnpj_atual" placeholder="000.000.000-00"
                                               class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Cartório</label>
                                        <input type="text" name="cartorio" placeholder="Ex: Cartório do 1º Ofício"
                                               class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Nº Escritura</label>
                                        <input type="text" name="numero_escritura" placeholder="Ex: 12345"
                                               class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Nº Registro</label>
                                        <input type="text" name="numero_registro" placeholder="Ex: R-1/12345"
                                               class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Corretor</label>
                                        <input type="text" name="corretor" placeholder="Nome do corretor"
                                               class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Descrição</label>
                                    <textarea name="descricao" rows="2" placeholder="Descreva o evento..."
                                              class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Observações internas</label>
                                    <textarea name="observacoes" rows="2" placeholder="Anotações adicionais (uso interno)"
                                              class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>

                                <div class="flex justify-end pt-2">
                                    <button type="submit"
                                            class="px-5 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-700">
                                        Registrar Evento
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif

                </div>

                {{-- ABA: SYNC SITE --}}
                <div x-show="aba === 'sync'" x-cloak>

                    {{-- Badge status sync --}}
                    <div class="mb-4 flex flex-wrap items-center gap-3">
                        @if($imovel->site_imovel_id)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-green-100 text-green-800 text-sm font-medium">
                            ✅ Sincronizado com site · ID #{{ $imovel->site_imovel_id }}
                            @if($imovel->site_sincronizado_em)
                            <span class="text-green-600 font-normal">· {{ $imovel->site_sincronizado_em->format('d/m/Y H:i') }}</span>
                            @endif
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-gray-100 text-gray-500 text-sm">
                            ⏸ Nunca sincronizado com o site
                        </span>
                        @endif
                    </div>

                    @if(auth()->user()->isAdmin())
                    {{-- Botão Sync --}}
                    <div class="mb-4 flex items-center justify-between">
                        <p class="text-sm text-gray-500">Histórico de sincronizações automáticas e manuais com o site.</p>
                        <a href="{{ route('imoveis.sync.preview', $imovel) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                            🔄 Sincronizar com o Site
                        </a>
                    </div>
                    @endif

                    @php $syncLogs = $imovel->syncLogs()->limit(50)->get(); @endphp

                    @if($syncLogs->isEmpty())
                    <div class="bg-white shadow-sm rounded-lg p-10 text-center">
                        <div class="text-5xl mb-3">🔄</div>
                        <p class="text-gray-500 text-sm">Nenhuma sincronização registrada ainda.</p>
                        <p class="text-gray-400 text-xs mt-1">O sync ocorre automaticamente ao salvar ou deletar o imóvel ou suas mídias.</p>
                    </div>
                    @else
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Data/Hora</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Evento</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Resposta</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($syncLogs as $log)
                                @php $cor = $log->statusCor(); @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-block text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-700">
                                            {{ $log->eventoLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-block text-xs px-2 py-0.5 rounded-full bg-{{ $cor }}-100 text-{{ $cor }}-700 font-semibold capitalize">
                                            {{ $log->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-500 max-w-xs truncate" title="{{ $log->resposta }}">
                                        {{ $log->resposta ? Str::limit($log->resposta, 80) : '—' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                </div>

            </div>{{-- /x-data abas --}}

            {{-- Rodapé: link de volta ao lote --}}
            <div class="mt-6 flex items-center justify-between text-sm text-gray-400">
                <a href="{{ route('lotes.show', $imovel->lote) }}" class="hover:text-indigo-600">
                    ← Voltar ao Lote {{ $imovel->lote->numero }}
                </a>
                <a href="{{ route('condominios.mapa', $imovel->lote->quadra->condominio) }}" class="hover:text-indigo-600">
                    Ver no Mapa →
                </a>
            </div>

        </div>
    </div>

    {{-- Modal visualizador de documentos --}}
    @include('partials._doc_viewer_modal')

</x-app-layout>
