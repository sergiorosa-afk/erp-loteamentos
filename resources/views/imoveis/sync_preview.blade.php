<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('imoveis.show', $imovel) }}" class="text-gray-400 hover:text-gray-600">
                ← Voltar ao Imóvel
            </a>
            <span class="text-gray-300">/</span>
            <h2 class="font-semibold text-gray-800">
                Sincronização com o Site — {{ $imovel->nome ?? $imovel->tipoLabel() }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Banner de contexto --}}
            @if($erroSite)
            <div class="bg-orange-50 border border-orange-200 rounded-lg px-4 py-3 text-sm text-orange-800">
                <p class="font-semibold">⚠️ Não foi possível buscar os dados atuais do site.</p>
                <p class="mt-1 text-orange-700 text-xs">{{ $erroSite }}</p>
                <p class="mt-2">Você pode sincronizar mesmo assim — o ERP enviará os dados atuais e o site será atualizado.</p>
            </div>
            @elseif($comparacao)
            @php $totalDiffs = collect($comparacao)->where('diff', true)->count(); @endphp
            @if($totalDiffs > 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-3 text-sm text-yellow-800">
                🔍 Encontradas <strong>{{ $totalDiffs }} diferença(s)</strong> entre o ERP e o site. As linhas em amarelo serão atualizadas ao confirmar.
            </div>
            @else
            <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-3 text-sm text-green-800">
                ✅ ERP e site estão <strong>em sincronia</strong> — nenhuma diferença nos campos de texto. Você pode sincronizar novamente para atualizar as mídias.
            </div>
            @endif
            @else
            <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-sm text-blue-800">
                🔄 Primeira sincronização — os dados abaixo serão <strong>criados</strong> no portal de imóveis.
            </div>
            @endif

            {{-- Tabela de campos --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800 text-sm">
                        @if($comparacao) Comparação ERP × Site @else Dados que serão enviados ao site @endif
                    </h3>
                </div>

                @if($comparacao)
                {{-- Modo comparação: 3 colunas --}}
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-5 py-2.5 text-xs font-semibold text-gray-500 uppercase w-1/4">Campo</th>
                            <th class="text-left px-5 py-2.5 text-xs font-semibold text-indigo-600 uppercase w-[37.5%]">ERP (fonte de verdade)</th>
                            <th class="text-left px-5 py-2.5 text-xs font-semibold text-gray-400 uppercase w-[37.5%]">Site (atual)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($comparacao as $linha)
                        @php
                            $erpVal  = $linha['erp']  !== null && $linha['erp']  !== '' ? $linha['erp']  : null;
                            $siteVal = $linha['site'] !== null && $linha['site'] !== '' ? $linha['site'] : null;
                        @endphp
                        <tr class="{{ $linha['diff'] ? 'bg-yellow-50' : 'hover:bg-gray-50' }}">
                            <td class="px-5 py-2.5 text-gray-500 font-medium text-xs">
                                {{ $linha['label'] }}
                                @if($linha['diff'])
                                <span class="ml-1 inline-block w-1.5 h-1.5 rounded-full bg-yellow-400 align-middle"></span>
                                @endif
                            </td>
                            <td class="px-5 py-2.5 text-gray-900 font-medium">
                                @if($erpVal !== null)
                                    <span class="break-words">{{ \Illuminate\Support\Str::limit((string)$erpVal, 80) }}</span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-2.5 text-gray-500">
                                @if($siteVal !== null)
                                    <span class="break-words {{ $linha['diff'] ? 'line-through text-gray-400' : '' }}">{{ \Illuminate\Support\Str::limit((string)$siteVal, 80) }}</span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                {{-- Modo preview simples: 2 colunas --}}
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-5 py-2.5 text-xs font-semibold text-gray-500 uppercase w-1/3">Campo</th>
                            <th class="text-left px-5 py-2.5 text-xs font-semibold text-gray-500 uppercase">Valor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $situacaoMap = [
                                'disponivel' => 'available',
                                'reservado'  => 'reserved',
                                'vendido'    => 'sold',
                                'permutado'  => 'sold',
                            ];
                            $campos = [
                                'Código de referência'  => 'erp_' . $imovel->id,
                                'Título'                => $imovel->nome ?? $imovel->tipoLabel(),
                                'Tipo'                  => $imovel->tipoLabel(),
                                'Status'                => $situacaoMap[$imovel->lote?->situacao ?? 'disponivel'] ?? 'available',
                                'Descrição'             => $imovel->descricao ? \Illuminate\Support\Str::limit($imovel->descricao, 120) : null,
                                'Condomínio'            => $imovel->lote?->quadra?->condominio?->nome,
                                'Área total (m²)'       => $imovel->area_total ? number_format($imovel->area_total, 2, ',', '.') : null,
                                'Área construída (m²)'  => $imovel->area_construida ? number_format($imovel->area_construida, 2, ',', '.') : null,
                                'Quartos'               => $imovel->quartos,
                                'Suítes'                => $imovel->suites,
                                'Banheiros'             => $imovel->banheiros,
                                'Vagas de garagem'      => $imovel->vagas_garagem,
                                'Andares'               => $imovel->andares,
                                'Ano de construção'     => $imovel->ano_construcao,
                                'Valor de mercado'      => $imovel->valor_mercado ? 'R$ ' . number_format($imovel->valor_mercado, 2, ',', '.') : null,
                                'Cidade'                => $imovel->cidade,
                                'Estado'                => $imovel->estado,
                                'CEP'                   => $imovel->cep,
                            ];
                        @endphp
                        @foreach($campos as $label => $valor)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-2.5 text-gray-500 font-medium">{{ $label }}</td>
                            <td class="px-5 py-2.5 text-gray-800">
                                @if($valor === null || $valor === '')
                                    <span class="text-gray-300">—</span>
                                @else
                                    {{ $valor }}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>

            {{-- Seleção de mídias --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800 text-sm">Mídias a sincronizar</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Somente imagens e vídeos. Documentos PDF nunca são enviados ao site.</p>
                </div>

                @if($midiasSincronizaveis->isEmpty())
                <div class="px-5 py-10 text-center text-gray-400 text-sm">
                    Nenhuma imagem ou vídeo cadastrado neste imóvel.
                </div>
                @else
                <div class="p-5"
                     x-data="{ total: {{ $midiasSincronizaveis->count() }}, selecionadas: {{ $midiasSincronizaveis->count() }} }"
                     @midia-change.window="selecionadas = document.querySelectorAll('#midia-grid input:checked').length">

                    <div class="flex items-center gap-4 mb-4 text-xs text-gray-500">
                        <button type="button" @click="$dispatch('set-all-midias', true)" class="hover:text-indigo-600 font-medium">Selecionar todas</button>
                        <span>·</span>
                        <button type="button" @click="$dispatch('set-all-midias', false)" class="hover:text-red-500 font-medium">Desmarcar todas</button>
                        <span class="ml-auto" x-text="selecionadas + ' de ' + total + ' selecionadas'"></span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3" id="midia-grid">
                        @foreach($midiasSincronizaveis as $midia)
                        <div x-data="{ checked: true }"
                             class="cursor-pointer"
                             @click="checked = !checked; $dispatch('midia-change')"
                             @set-all-midias.window="checked = $event.detail; $dispatch('midia-change')">
                            <div class="relative rounded-lg overflow-hidden border-2 transition-all"
                                 :class="checked ? 'border-indigo-500' : 'border-transparent opacity-50'">
                                @if($midia->tipo === 'imagem')
                                    <img src="{{ Storage::url($midia->path) }}"
                                         alt="{{ $midia->titulo ?? $midia->nome_original }}"
                                         class="w-full h-28 object-cover bg-gray-100">
                                @else
                                    <div class="w-full h-28 bg-gray-800 flex flex-col items-center justify-center gap-1">
                                        <span class="text-3xl">🎬</span>
                                        <span class="text-white text-xs">Vídeo</span>
                                    </div>
                                @endif
                                <div class="absolute top-1.5 right-1.5">
                                    <div class="w-5 h-5 rounded-full flex items-center justify-center shadow-sm transition-colors"
                                         :class="checked ? 'bg-indigo-600 border-2 border-indigo-600' : 'bg-white border-2 border-gray-300'">
                                        <svg x-show="checked" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                @if($midia->capa)
                                <div class="absolute bottom-1.5 left-1.5">
                                    <span class="bg-yellow-400 text-yellow-900 text-[10px] font-bold px-1.5 py-0.5 rounded">CAPA</span>
                                </div>
                                @endif
                                <input type="checkbox"
                                       name="midia_ids[]"
                                       value="{{ $midia->id }}"
                                       form="form-sync"
                                       x-model="checked"
                                       class="sr-only">
                            </div>
                            <p class="mt-1 text-xs text-gray-500 truncate">{{ $midia->titulo ?? $midia->nome_original }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Botões de ação --}}
            <form id="form-sync" action="{{ route('imoveis.sync.forcar', $imovel) }}" method="POST">
                @csrf
                <div class="flex items-center justify-between">
                    <a href="{{ route('imoveis.show', $imovel) }}"
                       class="px-5 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                        @if($comparacao && collect($comparacao)->where('diff', true)->count() === 0)
                            🔄 Sincronizar Mídias
                        @elseif($erroSite)
                            🔄 Sincronizar Mesmo Assim
                        @else
                            🔄 Confirmar Sincronização
                        @endif
                    </button>
                </div>
            </form>

        </div>
    </div>

</x-app-layout>
