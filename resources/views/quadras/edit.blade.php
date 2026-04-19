<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-600 text-sm">
            <a href="{{ route('condominios.index') }}" class="hover:text-indigo-600">Condomínios</a>
            <span>/</span>
            <a href="{{ route('condominios.show', $condominio) }}" class="hover:text-indigo-600">{{ $condominio->nome }}</a>
            <span>/</span>
            <span class="text-gray-900 font-semibold">Quadra {{ $quadra->codigo }}</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('quadras.update', $quadra) }}" method="POST" class="space-y-6">
                @csrf @method('PUT')
                <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Código da Quadra *</label>
                        <input type="text" name="codigo" value="{{ old('codigo', $quadra->codigo) }}" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('codigo') border-red-500 @enderror">
                        @error('codigo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Área Total (m²)</label>
                        <input type="number" step="0.01" name="area_total" value="{{ old('area_total', $quadra->area_total) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Observações</label>
                        <textarea name="observacoes" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('observacoes', $quadra->observacoes) }}</textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <a href="{{ route('condominios.show', $condominio) }}"
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">Cancelar</a>
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                        Atualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
