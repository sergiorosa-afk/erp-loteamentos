<x-app-layout>
    <x-slot name="header">
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
                <a href="{{ route('imoveis.show', $imovel) }}" class="hover:text-indigo-600">Imóvel</a>
                <span>/</span>
                <span class="text-gray-900 font-semibold">Editar</span>
            </div>
            <h2 class="font-semibold text-xl text-gray-800">
                Editar Imóvel — {{ $imovel->tipoLabel() }}
                @if($imovel->nome) <span class="text-gray-500 font-normal text-base">· {{ $imovel->nome }}</span> @endif
            </h2>
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

            <form action="{{ route('imoveis.update', $imovel) }}" method="POST"
                  class="bg-white shadow-sm rounded-lg p-6 space-y-6">
                @csrf
                @method('PUT')

                @include('imoveis._form')

                <div class="flex justify-end items-center gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('imoveis.show', $imovel) }}"
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-5 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-700">
                        Salvar Alterações
                    </button>
                </div>
            </form>

            {{-- Danger zone — formulário SEPARADO, nunca aninhado --}}
            <div class="mt-4 border border-red-200 bg-red-50 rounded-lg px-5 py-3 flex items-center justify-between">
                <p class="text-sm text-red-700">Remover permanentemente este cadastro de imóvel.</p>
                <form action="{{ route('imoveis.destroy', $imovel) }}" method="POST"
                      onsubmit="return confirm('Remover o cadastro deste imóvel? Esta ação não pode ser desfeita.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="text-sm text-red-600 hover:text-red-800 font-medium border border-red-300 px-3 py-1.5 rounded hover:bg-red-100">
                        Remover Imóvel
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
