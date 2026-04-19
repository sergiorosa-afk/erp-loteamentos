<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-2 text-gray-500 text-sm mb-1">
                    <a href="{{ route('condominios.index') }}" class="hover:text-indigo-600">Condomínios</a>
                    <span>/</span>
                    <a href="{{ route('condominios.show', $quadra->condominio) }}" class="hover:text-indigo-600">{{ $quadra->condominio->nome }}</a>
                    <span>/</span>
                    <span class="text-gray-900">Quadra {{ $quadra->codigo }}</span>
                </div>
                <h2 class="font-semibold text-xl text-gray-800">Quadra {{ $quadra->codigo }}</h2>
                @if($quadra->area_total)
                    <p class="text-sm text-gray-500">{{ number_format($quadra->area_total, 2, ',', '.') }} m²</p>
                @endif
            </div>
            <div class="flex gap-2">
                <a href="{{ route('quadras.edit', $quadra) }}"
                   class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                    Editar Quadra
                </a>
                <a href="{{ route('quadras.lotes.create', $quadra) }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                    + Novo Lote
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="loteManager()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Ação de unificação --}}
            <div x-show="selected.length >= 2" x-cloak
                 class="bg-indigo-50 border border-indigo-200 rounded-lg px-5 py-3 flex items-center gap-4">
                <span class="text-sm text-indigo-700 font-medium">
                    <span x-text="selected.length"></span> lotes selecionados
                </span>
                <button @click="showUnifyModal = true"
                        class="px-4 py-1.5 bg-indigo-600 text-white rounded text-sm font-medium hover:bg-indigo-700">
                    Unificar Selecionados
                </button>
                <button @click="selected = []" class="text-sm text-indigo-500 hover:text-indigo-700">
                    Cancelar seleção
                </button>
            </div>

            {{-- Tabela de lotes --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-base font-semibold text-gray-900">
                        Lotes
                        <span class="ml-1 text-sm font-normal text-gray-500">({{ $quadra->lotes->count() }})</span>
                    </h3>
                    <p class="text-xs text-gray-400">Selecione 2+ lotes para unificar</p>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="pl-4 pr-2 py-3 w-8">
                                <input type="checkbox" @change="toggleAll($event)"
                                       class="rounded border-gray-300 text-indigo-600">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lote</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Situação</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proprietário</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Área (m²)</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor Tabela</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Imóvel</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mapa</th>
                            <th class="relative px-4 py-3"><span class="sr-only">Ações</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($quadra->lotes as $lote)
                            <tr class="hover:bg-gray-50" :class="selected.includes({{ $lote->id }}) ? 'bg-indigo-50' : ''">
                                <td class="pl-4 pr-2 py-3">
                                    @if(!$lote->unificado)
                                    <input type="checkbox"
                                           :value="{{ $lote->id }}"
                                           x-model="selected"
                                           :disabled="{{ $lote->unificado ? 'true' : 'false' }}"
                                           class="rounded border-gray-300 text-indigo-600">
                                    @else
                                        <span class="text-gray-300 text-lg" title="Lote unificado">⊞</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('lotes.show', $lote) }}"
                                       class="font-medium text-indigo-600 hover:text-indigo-900">
                                        Lote {{ $lote->numero }}
                                        @if($lote->unificado)
                                            <span class="ml-1 text-xs bg-indigo-100 text-indigo-700 px-1.5 py-0.5 rounded">unificado</span>
                                        @endif
                                    </a>
                                    @if($lote->codigo_interno)
                                        <div class="text-xs text-gray-400">{{ $lote->codigo_interno }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if(auth()->user()->isAdmin())
                                    <form action="{{ route('lotes.situacao', $lote) }}" method="POST">
                                        @csrf @method('PATCH')
                                        @php
                                            $situacaoColors = [
                                                'disponivel' => 'bg-green-100 text-green-800',
                                                'reservado'  => 'bg-yellow-100 text-yellow-800',
                                                'vendido'    => 'bg-red-100 text-red-800',
                                                'permutado'  => 'bg-purple-100 text-purple-800',
                                            ];
                                        @endphp
                                        <select name="situacao" onchange="this.form.submit()"
                                                class="text-xs px-2 py-1 rounded-full font-semibold border-0 cursor-pointer {{ $situacaoColors[$lote->situacao] ?? '' }}">
                                            @foreach(['disponivel','reservado','vendido','permutado'] as $s)
                                                <option value="{{ $s }}" {{ $lote->situacao === $s ? 'selected' : '' }}>
                                                    {{ ucfirst($s) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                    @else
                                    @php $situacaoColors = ['disponivel'=>'bg-green-100 text-green-800','reservado'=>'bg-yellow-100 text-yellow-800','vendido'=>'bg-red-100 text-red-800','permutado'=>'bg-purple-100 text-purple-800']; @endphp
                                    <span class="text-xs px-2 py-1 rounded-full font-semibold {{ $situacaoColors[$lote->situacao] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($lote->situacao) }}</span>
                                    @endif
                                </td>
                                {{-- Coluna Proprietário --}}
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    @if($lote->proprietario_nome)
                                    <span class="font-medium text-gray-800">{{ $lote->proprietario_nome }}</span>
                                    @else
                                    <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $lote->area ? number_format($lote->area, 2, ',', '.') : '—' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $lote->valor_tabela ? 'R$ ' . number_format($lote->valor_tabela, 2, ',', '.') : '—' }}
                                </td>
                                {{-- Coluna Imóvel --}}
                                <td class="px-4 py-3 text-sm">
                                    @if($lote->imovel)
                                        <a href="{{ route('imoveis.show', $lote->imovel) }}"
                                           class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-900 font-medium">
                                            🏠 {{ $lote->imovel->tipoLabel() }}
                                        </a>
                                    @elseif(auth()->user()->isAdmin())
                                        <a href="{{ route('imoveis.create', $lote) }}"
                                           class="text-xs text-gray-400 hover:text-indigo-600 border border-dashed border-gray-300 hover:border-indigo-400 px-2 py-0.5 rounded transition-colors">
                                            + Cadastrar
                                        </a>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($lote->poligono && count($lote->poligono) >= 3)
                                        <span class="text-green-500" title="Mapeado">●</span>
                                    @else
                                        <span class="text-gray-300" title="Sem polígono">○</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm space-x-2">
                                    @if(auth()->user()->isAdmin())
                                    <a href="{{ route('lotes.edit', $lote) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                    @if($lote->unificado)
                                        <form action="{{ route('lotes.desunificar', $lote) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Desfazer unificação e restaurar lotes originais?')">
                                            @csrf
                                            <button type="submit" class="text-orange-500 hover:text-orange-700">Desunificar</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('lotes.destroy', $lote) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Excluir lote?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-10 text-center text-gray-400">
                                    Nenhum lote cadastrado.
                                    <a href="{{ route('quadras.lotes.create', $quadra) }}" class="text-indigo-600 hover:underline">Adicionar lote</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        {{-- Modal de unificação --}}
        <div x-show="showUnifyModal" x-cloak
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
             @keydown.escape.window="showUnifyModal = false">
            <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md" @click.stop>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Unificar Lotes</h3>

                <form action="{{ route('quadras.unificar', $quadra) }}" method="POST" class="space-y-4">
                    @csrf

                    <template x-for="id in selected">
                        <input type="hidden" name="lote_ids[]" :value="id">
                    </template>

                    <div class="bg-indigo-50 rounded-lg p-3 text-sm text-indigo-700">
                        <strong x-text="selected.length"></strong> lotes serão unificados.
                        O polígono resultante será calculado automaticamente (pode ser refinado no editor).
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número do Lote Unificado *</label>
                        <input type="text" name="numero" required placeholder="Ex: 01-02, U1..."
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Área Total (m²)</label>
                            <input type="number" step="0.01" name="area"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Valor Tabela (R$)</label>
                            <input type="number" step="0.01" name="valor_tabela"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Situação</label>
                        <select name="situacao" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="disponivel">Disponível</option>
                            <option value="reservado">Reservado</option>
                            <option value="vendido">Vendido</option>
                            <option value="permutado">Permutado</option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showUnifyModal = false"
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-700">
                            Confirmar Unificação
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
    function loteManager() {
        return {
            selected: [],
            showUnifyModal: false,
            toggleAll(e) {
                if (e.target.checked) {
                    this.selected = @json($quadra->lotes->where('unificado', false)->pluck('id'));
                } else {
                    this.selected = [];
                }
            }
        }
    }
    </script>
    @endpush
</x-app-layout>
