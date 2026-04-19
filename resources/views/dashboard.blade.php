<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Painel Principal
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @php
                $totalCondominios = \App\Models\Condominio::count();
                $totalQuadras = \App\Models\Quadra::count();
                $totalLotes = \App\Models\Lote::count();
                $lotesDisponiveis = \App\Models\Lote::where('situacao', 'disponivel')->count();
            @endphp

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-indigo-500">
                    <p class="text-sm text-gray-500">Condomínios</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalCondominios }}</p>
                    <a href="{{ route('condominios.index') }}" class="text-xs text-indigo-600 hover:underline">Ver todos</a>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                    <p class="text-sm text-gray-500">Quadras</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalQuadras }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                    <p class="text-sm text-gray-500">Lotes Disponíveis</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $lotesDisponiveis }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-gray-400">
                    <p class="text-sm text-gray-500">Total de Lotes</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalLotes }}</p>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Condomínios Recentes</h3>
                @php $condominios = \App\Models\Condominio::latest()->limit(5)->get(); @endphp
                @if($condominios->isEmpty())
                    <p class="text-sm text-gray-500">Nenhum condomínio cadastrado ainda.
                        <a href="{{ route('condominios.create') }}" class="text-indigo-600 hover:underline">Cadastrar agora</a>
                    </p>
                @else
                    <ul class="divide-y divide-gray-100">
                        @foreach($condominios as $c)
                            <li class="py-2 flex justify-between items-center">
                                <a href="{{ route('condominios.show', $c) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    {{ $c->nome }}
                                </a>
                                <span class="text-xs text-gray-400">{{ $c->cidade }}{{ $c->estado ? '/' . $c->estado : '' }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
