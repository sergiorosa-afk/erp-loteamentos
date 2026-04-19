<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500 text-sm mb-1">
            <a href="{{ route('pessoas.index') }}" class="hover:text-indigo-600">Pessoas</a>
            <span>/</span>
            <a href="{{ route('pessoas.show', $pessoa) }}" class="hover:text-indigo-600">{{ $pessoa->nome }}</a>
            <span>/</span>
            <span class="text-gray-900">Editar</span>
        </div>
        <h2 class="font-semibold text-xl text-gray-800">Editar — {{ $pessoa->nome }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Form de EDIÇÃO --}}
            <form method="POST" action="{{ route('pessoas.update', $pessoa) }}">
                @csrf
                @method('PUT')

                @include('pessoas._form')

                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('pessoas.show', $pessoa) }}"
                       class="px-4 py-2 border border-gray-300 text-sm rounded-md text-gray-600 hover:bg-gray-50">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                        Salvar Alterações
                    </button>
                </div>
            </form>

            {{-- Form de EXCLUSÃO — separado para evitar nested form --}}
            <div class="mt-6 border-t border-red-100 pt-6">
                <details class="group">
                    <summary class="cursor-pointer text-sm text-red-600 hover:text-red-800 font-medium select-none">
                        ⚠️ Zona de Perigo — Remover pessoa
                    </summary>
                    <div class="mt-3 bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-sm text-red-700 mb-3">
                            Esta ação é reversível (soft delete). A pessoa será ocultada do sistema mas pode ser restaurada.
                        </p>
                        <form method="POST" action="{{ route('pessoas.destroy', $pessoa) }}"
                              onsubmit="return confirm('Confirma remoção de {{ addslashes($pessoa->nome) }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">
                                Remover Pessoa
                            </button>
                        </form>
                    </div>
                </details>
            </div>

        </div>
    </div>
</x-app-layout>
