<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Relatórios</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <p class="text-sm text-gray-500">Selecione um condomínio para gerar o relatório.</p>
                </div>
                @if($condominios->isEmpty())
                    <p class="px-6 py-8 text-center text-gray-400 text-sm">Nenhum condomínio cadastrado.</p>
                @else
                <ul class="divide-y divide-gray-100">
                    @foreach($condominios as $condominio)
                    <li class="flex items-center justify-between px-6 py-4 hover:bg-gray-50">
                        <div>
                            <p class="font-medium text-gray-900">{{ $condominio->nome }}</p>
                            @if($condominio->cidade || $condominio->estado)
                            <p class="text-xs text-gray-400">{{ implode(' – ', array_filter([$condominio->cidade, $condominio->estado])) }}</p>
                            @endif
                        </div>
                        <a href="{{ route('relatorios.condominio', $condominio) }}"
                           class="px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                            Ver Relatório
                        </a>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
