<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Pessoas</h2>
                <p class="text-sm text-gray-500 mt-0.5">Leads, Prospects e Clientes</p>
            </div>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('pessoas.create') }}"
               class="inline-flex items-center gap-1 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition">
                + Nova Pessoa
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3">
                {{ session('success') }}
            </div>
            @endif

            {{-- Cards de totais --}}
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-3 flex items-center gap-3">
                    <span class="text-2xl font-bold text-yellow-700">{{ $totais['lead'] }}</span>
                    <span class="text-sm text-yellow-600 font-medium">Leads</span>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 flex items-center gap-3">
                    <span class="text-2xl font-bold text-blue-700">{{ $totais['prospect'] }}</span>
                    <span class="text-sm text-blue-600 font-medium">Prospects</span>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-3 flex items-center gap-3">
                    <span class="text-2xl font-bold text-green-700">{{ $totais['cliente'] }}</span>
                    <span class="text-sm text-green-600 font-medium">Clientes</span>
                </div>
            </div>

            {{-- Filtros --}}
            <form method="GET" action="{{ route('pessoas.index') }}"
                  class="bg-white shadow-sm rounded-lg p-4 mb-4 flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs text-gray-500 mb-1">Buscar</label>
                    <input type="text" name="busca" value="{{ request('busca') }}"
                           placeholder="Nome, CPF/CNPJ, e-mail, celular..."
                           class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-xs text-gray-500 mb-1">Tipo</label>
                    <select name="tipo" class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todos</option>
                        <option value="lead"     {{ request('tipo') === 'lead'     ? 'selected' : '' }}>Lead</option>
                        <option value="prospect" {{ request('tipo') === 'prospect' ? 'selected' : '' }}>Prospect</option>
                        <option value="cliente"  {{ request('tipo') === 'cliente'  ? 'selected' : '' }}>Cliente</option>
                    </select>
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-xs text-gray-500 mb-1">Origem</label>
                    <select name="origem" class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todas</option>
                        <option value="site" {{ request('origem') === 'site' ? 'selected' : '' }}>🌐 Via Site</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                        Filtrar
                    </button>
                    @if(request('busca') || request('tipo') || request('origem'))
                    <a href="{{ route('pessoas.index') }}"
                       class="px-4 py-2 border border-gray-300 text-sm rounded-md text-gray-600 hover:bg-gray-50">
                        Limpar
                    </a>
                    @endif
                </div>
            </form>

            {{-- Tabela --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                @if($pessoas->isEmpty())
                <div class="text-center py-16 text-gray-400">
                    <div class="text-5xl mb-3">👤</div>
                    <p class="text-sm">Nenhuma pessoa encontrada.</p>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('pessoas.create') }}" class="mt-3 inline-block text-indigo-600 text-sm hover:underline">
                        + Cadastrar primeira pessoa
                    </a>
                    @endif
                </div>
                @else
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">CPF/CNPJ</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contato</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cidade</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pessoas as $pessoa)
                        @php $cor = $pessoa->tipoCor(); @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-medium text-gray-900">
                                <a href="{{ route('pessoas.show', $pessoa) }}" class="hover:text-indigo-600">
                                    {{ $pessoa->nome }}
                                </a>
                                @if($pessoa->origem === 'site')
                                <span class="ml-1 inline-block text-xs bg-teal-100 text-teal-700 px-1.5 py-0.5 rounded font-medium" title="Lead recebido via webhook do site">🌐 Site</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full
                                    bg-{{ $cor }}-100 text-{{ $cor }}-800">
                                    {{ $pessoa->tipoLabel() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $pessoa->cpfCnpjFormatado() ?: '—' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                @if($pessoa->celular)
                                    <div>{{ $pessoa->celular }}</div>
                                @endif
                                @if($pessoa->email)
                                    <div class="text-xs text-gray-400">{{ $pessoa->email }}</div>
                                @endif
                                @if(! $pessoa->celular && ! $pessoa->email)
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $pessoa->endereco?->cidade ?: '—' }}
                                @if($pessoa->endereco?->estado)
                                    /{{ $pessoa->endereco->estado }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('pessoas.show', $pessoa) }}"
                                   class="text-indigo-600 hover:underline text-xs">Ver</a>
                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('pessoas.edit', $pessoa) }}"
                                   class="ml-3 text-gray-500 hover:text-gray-700 text-xs">Editar</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Paginação --}}
                @if($pessoas->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $pessoas->links() }}
                </div>
                @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
