<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500 text-sm mb-1">
            <a href="{{ route('pessoas.index') }}" class="hover:text-indigo-600">Pessoas</a>
            <span>/</span>
            <span class="text-gray-900">Nova Pessoa</span>
        </div>
        <h2 class="font-semibold text-xl text-gray-800">Cadastrar Pessoa</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('pessoas.store') }}">
                @csrf

                @include('pessoas._form')

                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('pessoas.index') }}"
                       class="px-4 py-2 border border-gray-300 text-sm rounded-md text-gray-600 hover:bg-gray-50">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                        Salvar Pessoa
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
