<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-2 text-gray-500 text-sm mb-1">
                    <a href="{{ route('condominios.index') }}" class="hover:text-indigo-600">Condomínios</a>
                    <span>/</span>
                    <a href="{{ route('condominios.show', $lote->quadra->condominio) }}" class="hover:text-indigo-600">{{ $lote->quadra->condominio->nome }}</a>
                    <span>/</span>
                    <a href="{{ route('quadras.show', $lote->quadra) }}" class="hover:text-indigo-600">Quadra {{ $lote->quadra->codigo }}</a>
                    <span>/</span>
                    <span class="text-gray-900">Lote {{ $lote->numero }}</span>
                </div>
                <h2 class="font-semibold text-xl text-gray-800 flex items-center gap-2">
                    Lote {{ $lote->numero }}
                    @if($lote->unificado)
                        <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-normal">unificado</span>
                    @endif
                    @php $colors = ['disponivel'=>'green','reservado'=>'yellow','vendido'=>'red','permutado'=>'purple']; $c = $colors[$lote->situacao] ?? 'gray'; @endphp
                    <span class="text-xs bg-{{ $c }}-100 text-{{ $c }}-800 px-2 py-0.5 rounded-full font-normal capitalize">{{ $lote->situacao }}</span>
                </h2>
            </div>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('lotes.edit', $lote) }}"
               class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                Editar Lote
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Detalhes do lote --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Informações do Lote</h3>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-3 sm:grid-cols-3 text-sm">
                    <div><dt class="text-gray-500">Quadra</dt><dd class="font-medium text-gray-900">{{ $lote->quadra->codigo }}</dd></div>
                    <div><dt class="text-gray-500">Número</dt><dd class="font-medium text-gray-900">{{ $lote->numero }}</dd></div>
                    <div><dt class="text-gray-500">Código Interno</dt><dd class="font-medium text-gray-900">{{ $lote->codigo_interno ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Área</dt><dd class="font-medium text-gray-900">{{ $lote->area ? number_format($lote->area, 2, ',', '.') . ' m²' : '—' }}</dd></div>
                    <div><dt class="text-gray-500">Testada</dt><dd class="font-medium text-gray-900">{{ $lote->testada ? number_format($lote->testada, 2, ',', '.') . ' m' : '—' }}</dd></div>
                    <div><dt class="text-gray-500">Comprimento</dt><dd class="font-medium text-gray-900">{{ $lote->comprimento ? number_format($lote->comprimento, 2, ',', '.') . ' m' : '—' }}</dd></div>
                    <div><dt class="text-gray-500">Valor de Tabela</dt><dd class="font-medium text-gray-900">{{ $lote->valor_tabela ? 'R$ ' . number_format($lote->valor_tabela, 2, ',', '.') : '—' }}</dd></div>
                    <div><dt class="text-gray-500">Polígono mapeado</dt><dd class="font-medium">{{ $lote->poligono ? '✅ Sim' : '⚪ Não' }}</dd></div>
                    @if($lote->restricoes)
                    <div class="col-span-3"><dt class="text-gray-500">Restrições</dt><dd class="text-gray-900">{{ $lote->restricoes }}</dd></div>
                    @endif
                    @if($lote->unificado && $lote->lotes_originais)
                    <div class="col-span-3">
                        <dt class="text-gray-500">Lotes Originais</dt>
                        <dd class="text-gray-900">IDs: {{ implode(', ', $lote->lotes_originais) }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Imóvel vinculado --}}
            @php $imovel = $lote->imovel; @endphp
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-base font-semibold text-gray-900">🏠 Imóvel</h3>
                    @if(!$imovel && auth()->user()->isAdmin())
                    <a href="{{ route('imoveis.create', $lote) }}"
                       class="px-3 py-1.5 bg-indigo-600 text-white rounded text-xs font-semibold hover:bg-indigo-700">
                        + Cadastrar Imóvel
                    </a>
                    @elseif($imovel)
                    <a href="{{ route('imoveis.show', $imovel) }}"
                       class="text-sm text-indigo-600 hover:text-indigo-900">
                        Ver Cadastro Completo →
                    </a>
                    @endif
                </div>

                @if($imovel)
                <div class="px-6 py-4 flex flex-wrap gap-5 items-center">
                    @php $capa = $imovel->capa(); @endphp
                    @if($capa)
                    <img src="{{ $capa->url() }}" alt="Foto"
                         class="h-20 w-32 object-cover rounded-lg border border-gray-200 shrink-0">
                    @endif
                    <div class="flex-1 grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-2 text-sm">
                        @if($imovel->tipo)
                        <div><span class="text-gray-400 text-xs block">Tipo</span><span class="font-medium">{{ $imovel->tipoLabel() }}</span></div>
                        @endif
                        @if($imovel->area_construida)
                        <div><span class="text-gray-400 text-xs block">Área Construída</span><span class="font-medium">{{ number_format($imovel->area_construida, 2, ',', '.') }} m²</span></div>
                        @endif
                        @if($imovel->quartos)
                        <div><span class="text-gray-400 text-xs block">Quartos</span><span class="font-medium">{{ $imovel->quartos }}</span></div>
                        @endif
                        @if($imovel->valor_mercado)
                        <div><span class="text-gray-400 text-xs block">Valor de Mercado</span><span class="font-medium text-indigo-700">R$ {{ number_format($imovel->valor_mercado, 2, ',', '.') }}</span></div>
                        @endif
                        @if($imovel->matricula_imovel)
                        <div><span class="text-gray-400 text-xs block">Matrícula</span><span class="font-medium">{{ $imovel->matricula_imovel }}</span></div>
                        @endif
                    </div>
                </div>
                @else
                <p class="px-6 py-8 text-center text-gray-400 text-sm">Nenhum imóvel cadastrado para este lote.</p>
                @endif
            </div>

            {{-- Pessoas vinculadas ao Lote --}}
            @include('partials._vinculos_pessoa', [
                'pessoas'          => $lote->pessoas,
                'papeis'           => ['proprietario' => '👑 Proprietário', 'comprador' => '🛒 Comprador', 'interessado' => '🔍 Interessado'],
                'storeRouteName'   => 'lotes.pessoas.store',
                'destroyRouteName' => 'lotes.pessoas.destroy',
                'storeModel'       => $lote,
                'titulo'           => 'Pessoas Vinculadas ao Lote',
            ])

            {{-- Documentos --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-base font-semibold text-gray-900">
                        Documentos
                        <span class="ml-1 text-sm font-normal text-gray-500">({{ $lote->documentos->count() }})</span>
                    </h3>
                </div>

                @if($lote->documentos->isNotEmpty())
                <ul class="divide-y divide-gray-100">
                    @foreach($lote->documentos as $doc)
                    <li class="flex items-center gap-4 px-6 py-3">
                        {{-- Miniatura --}}
                        @if($doc->isImage())
                        <img src="{{ asset('storage/' . $doc->path) }}"
                             alt="{{ $doc->nome_original }}"
                             class="h-12 w-12 object-cover rounded border border-gray-200 shrink-0 cursor-pointer"
                             @click="$dispatch('open-doc-viewer', {
                                 url: '{{ route('documentos.visualizar', $doc) }}',
                                 downloadUrl: '{{ route('documentos.download', $doc) }}',
                                 tipo: 'imagem',
                                 nome: '{{ addslashes($doc->nome_original) }}'
                             })">
                        @else
                        <span class="text-2xl shrink-0">{{ $doc->icone() }}</span>
                        @endif

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $doc->nome_original }}</p>
                            <p class="text-xs text-gray-400">
                                {{ ucfirst($doc->tipo) }} · {{ $doc->tamanhoFormatado() }}
                                @if($doc->uploader) · por {{ $doc->uploader->name }} @endif
                                · {{ $doc->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>

                        {{-- Botão Ver (PDF ou imagem) --}}
                        @if($doc->isPdf() || $doc->isImage())
                        <button type="button"
                                @click="$dispatch('open-doc-viewer', {
                                    url: '{{ route('documentos.visualizar', $doc) }}',
                                    downloadUrl: '{{ route('documentos.download', $doc) }}',
                                    tipo: '{{ $doc->isPdf() ? 'pdf' : 'imagem' }}',
                                    nome: '{{ addslashes($doc->nome_original) }}'
                                })"
                                class="text-sm text-emerald-600 hover:text-emerald-800 shrink-0 font-medium">
                            👁 Ver
                        </button>
                        @endif

                        <a href="{{ route('documentos.download', $doc) }}"
                           class="text-sm text-indigo-600 hover:text-indigo-900 shrink-0">
                            ⬇ Baixar
                        </a>
                        @auth
                        @if(auth()->user()->isAdmin())
                        <form action="{{ route('documentos.destroy', $doc) }}" method="POST"
                              onsubmit="return confirm('Remover documento?')">
                            @csrf @method('DELETE')
                            <button class="text-sm text-red-500 hover:text-red-700">Remover</button>
                        </form>
                        @endif
                        @endauth
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="px-6 py-8 text-center text-gray-400 text-sm">Nenhum documento enviado ainda.</p>
                @endif

                {{-- Upload form (admin only) --}}
                @auth
                @if(auth()->user()->isAdmin())
                <div class="border-t border-gray-100 px-6 py-4 bg-gray-50">
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-3">Enviar novo documento</p>
                    <form action="{{ route('documentos.store', $lote) }}" method="POST" enctype="multipart/form-data"
                          class="flex flex-wrap items-end gap-3">
                        @csrf
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Tipo</label>
                            <select name="tipo" class="border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach(['escritura','planta','memorial','contrato','procuracao','outro'] as $t)
                                    <option value="{{ $t }}">{{ ucfirst($t) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1 min-w-48">
                            <label class="block text-xs text-gray-600 mb-1">Arquivo (PDF, imagem, Word, Excel — máx. 20MB)</label>
                            <input type="file" name="arquivo" required
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx"
                                   class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-sm file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded text-sm font-medium hover:bg-indigo-700">
                            Enviar
                        </button>
                    </form>
                    @error('arquivo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                @endif
                @endauth
            </div>

        </div>
    </div>

    {{-- Modal visualizador de documentos --}}
    @include('partials._doc_viewer_modal')

</x-app-layout>
