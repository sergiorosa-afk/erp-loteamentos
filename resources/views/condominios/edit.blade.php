<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-600 text-sm">
            <a href="{{ route('condominios.index') }}" class="hover:text-indigo-600">Condomínios</a>
            <span>/</span>
            <a href="{{ route('condominios.show', $condominio) }}" class="hover:text-indigo-600">{{ $condominio->nome }}</a>
            <span>/</span>
            <span class="text-gray-900 font-semibold">Editar</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('condominios.update', $condominio) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf @method('PUT')
                @include('condominios._form')
                <div class="flex justify-end gap-3">
                    <a href="{{ route('condominios.show', $condominio) }}"
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                        Atualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
