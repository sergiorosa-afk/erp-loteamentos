<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-2 text-gray-500 text-sm mb-1">
                    <a href="{{ route('condominios.index') }}" class="hover:text-indigo-600">Condomínios</a>
                    <span>/</span>
                    <span class="text-gray-900">{{ $condominio->nome }}</span>
                </div>
                <h2 class="font-semibold text-xl text-gray-800">{{ $condominio->nome }}</h2>
                @if($condominio->cidade)
                    <p class="text-sm text-gray-500">{{ $condominio->cidade }}{{ $condominio->estado ? '/' . $condominio->estado : '' }}</p>
                @endif
            </div>
            <div class="flex gap-2">
                <a href="{{ route('condominios.edit', $condominio) }}"
                   class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                    Editar
                </a>
                @if($condominio->planta_path)
                <a href="{{ route('condominios.mapa', $condominio) }}"
                   class="px-4 py-2 bg-slate-700 text-white rounded-md text-sm font-medium hover:bg-slate-600 flex items-center gap-1">
                    🗺 Ver Mapa
                </a>
                <a href="{{ route('condominios.editor', $condominio) }}"
                   class="px-4 py-2 bg-emerald-600 text-white rounded-md text-sm font-medium hover:bg-emerald-700 flex items-center gap-1">
                    ✏ Editor
                </a>
                @endif
                <a href="{{ route('condominios.quadras.create', $condominio) }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                    + Nova Quadra
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Stats --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-sm text-gray-500">Área Total</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $condominio->area_total ? number_format($condominio->area_total, 0, ',', '.') . ' m²' : '-' }}
                    </p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-sm text-gray-500">Quadras</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $condominio->quadras->count() }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-sm text-gray-500">Total de Lotes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $condominio->total_lotes ?? '-' }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-sm text-gray-500">Zoneamento</p>
                    <p class="text-2xl font-bold text-gray-900 capitalize">{{ $condominio->zoneamento }}</p>
                </div>
            </div>

            {{-- Quadras --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-base font-semibold text-gray-900">Quadras</h3>
                    <a href="{{ route('condominios.quadras.create', $condominio) }}" class="text-sm text-indigo-600 hover:text-indigo-900">+ Adicionar Quadra</a>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Área (m²)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lotes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Observações</th>
                            <th class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($condominio->quadras as $quadra)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('quadras.show', $quadra) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                        Quadra {{ $quadra->codigo }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $quadra->area_total ? number_format($quadra->area_total, 2, ',', '.') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $quadra->lotes->count() }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                    {{ $quadra->observacoes ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                                    <a href="{{ route('quadras.edit', $quadra) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                    <form action="{{ route('quadras.destroy', $quadra) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Confirmar exclusão?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    Nenhuma quadra cadastrada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Planta --}}
            @if($condominio->planta_path)
                @php $ext = strtolower(pathinfo($condominio->planta_nome_original ?? '', PATHINFO_EXTENSION)); @endphp
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-base font-semibold text-gray-900">Planta do Loteamento</h3>
                        <a href="{{ $condominio->plantaUrl() }}" download="{{ $condominio->planta_nome_original }}"
                           class="text-sm text-indigo-600 hover:text-indigo-900 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download
                        </a>
                    </div>
                    <div class="p-4">
                        @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg']))
                            <div x-data="{ zoom: false }" class="relative">
                                <img src="{{ $condominio->plantaUrl() }}"
                                     alt="Planta do loteamento"
                                     @click="zoom = !zoom"
                                     :class="zoom ? 'cursor-zoom-out max-h-none' : 'cursor-zoom-in max-h-96'"
                                     class="w-full object-contain rounded-lg border border-gray-200 bg-gray-50 transition-all">
                                <p class="text-xs text-gray-400 mt-2 text-center">Clique na imagem para ampliar</p>
                            </div>
                        @elseif($ext === 'pdf')
                            <iframe src="{{ $condominio->plantaUrl() }}"
                                    class="w-full h-96 rounded border border-gray-200"></iframe>
                        @else
                            <a href="{{ $condominio->plantaUrl() }}" target="_blank"
                               class="flex items-center gap-2 text-indigo-600 hover:underline text-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                {{ $condominio->planta_nome_original }}
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Detalhes --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-3">Identificação</h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-500">CNPJ</dt><dd class="text-gray-900">{{ $condominio->cnpj ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Matrícula</dt><dd class="text-gray-900">{{ $condominio->matricula_cartorio ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Nº Registro</dt><dd class="text-gray-900">{{ $condominio->numero_registro ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Município Registro</dt><dd class="text-gray-900">{{ $condominio->municipio_registro ?? '-' }}</dd></div>
                    </dl>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-base font-semibold text-gray-900 mb-3">Contato</h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-500">Incorporadora</dt><dd class="text-gray-900">{{ $condominio->incorporadora ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Síndico</dt><dd class="text-gray-900">{{ $condominio->sindico ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Administradora</dt><dd class="text-gray-900">{{ $condominio->administradora ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Telefone</dt><dd class="text-gray-900">{{ $condominio->telefone ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">E-mail</dt><dd class="text-gray-900">{{ $condominio->email ?? '-' }}</dd></div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
