<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="flex items-center gap-2 text-gray-500 text-sm mb-1">
                <a href="{{ route('condominios.index') }}" class="hover:text-indigo-600">Condomínios</a>
                <span>/</span>
                <a href="{{ route('condominios.show', $lote->quadra->condominio) }}" class="hover:text-indigo-600">{{ $lote->quadra->condominio->nome }}</a>
                <span>/</span>
                <a href="{{ route('quadras.show', $lote->quadra) }}" class="hover:text-indigo-600">Quadra {{ $lote->quadra->codigo }}</a>
                <span>/</span>
                <a href="{{ route('lotes.show', $lote) }}" class="hover:text-indigo-600">Lote {{ $lote->numero }}</a>
                <span>/</span>
                <span class="text-gray-900 font-semibold">Cadastrar Imóvel</span>
            </div>
            <h2 class="font-semibold text-xl text-gray-800">Cadastrar Imóvel — Lote {{ $lote->numero }}</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-sm font-semibold text-red-800 mb-1">Corrija os erros abaixo:</p>
                <ul class="text-sm text-red-700 list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('imoveis.store', $lote) }}" method="POST"
                  class="bg-white shadow-sm rounded-lg p-6 space-y-6">
                @csrf

                @include('imoveis._form', ['imovel' => null])

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('lotes.show', $lote) }}"
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-5 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-700">
                        Salvar Imóvel
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
