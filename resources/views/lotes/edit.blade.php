<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-600 text-sm">
            <a href="{{ route('condominios.index') }}" class="hover:text-indigo-600">Condomínios</a>
            <span>/</span>
            <a href="{{ route('condominios.show', $lote->quadra->condominio) }}" class="hover:text-indigo-600">{{ $lote->quadra->condominio->nome }}</a>
            <span>/</span>
            <a href="{{ route('quadras.show', $lote->quadra) }}" class="hover:text-indigo-600">Quadra {{ $lote->quadra->codigo }}</a>
            <span>/</span>
            <span class="text-gray-900 font-semibold">Lote {{ $lote->numero }}</span>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('lotes.update', $lote) }}" method="POST" class="space-y-6">
                @csrf @method('PUT')
                @include('lotes._form')
                <div class="flex justify-end gap-3">
                    <a href="{{ route('quadras.show', $lote->quadra) }}"
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">Cancelar</a>
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-700">Atualizar</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
