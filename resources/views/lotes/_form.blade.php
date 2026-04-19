<div class="bg-white shadow-sm rounded-lg p-6 space-y-4">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-gray-700">Número do Lote *</label>
            <input type="text" name="numero" value="{{ old('numero', $lote->numero ?? '') }}" required
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('numero') border-red-500 @enderror">
            @error('numero') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Código Interno</label>
            <input type="text" name="codigo_interno" value="{{ old('codigo_interno', $lote->codigo_interno ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Área (m²)</label>
            <input type="number" step="0.01" name="area" value="{{ old('area', $lote->area ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Testada (m frente)</label>
            <input type="number" step="0.01" name="testada" value="{{ old('testada', $lote->testada ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Comprimento (m)</label>
            <input type="number" step="0.01" name="comprimento" value="{{ old('comprimento', $lote->comprimento ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Valor de Tabela (R$)</label>
            <input type="number" step="0.01" name="valor_tabela" value="{{ old('valor_tabela', $lote->valor_tabela ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Situação</label>
            <select name="situacao" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                @foreach(['disponivel','reservado','vendido','permutado'] as $s)
                    <option value="{{ $s }}" {{ old('situacao', $lote->situacao ?? 'disponivel') === $s ? 'selected' : '' }}>
                        {{ ucfirst($s) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Restrições</label>
            <textarea name="restricoes" rows="2"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('restricoes', $lote->restricoes ?? '') }}</textarea>
        </div>
    </div>
</div>
