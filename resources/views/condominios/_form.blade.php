@php $editing = isset($condominio) && $condominio->exists; @endphp

{{-- Identificação --}}
<div class="bg-white shadow-sm rounded-lg p-6">
    <h3 class="text-base font-semibold text-gray-900 mb-4">Identificação</h3>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Nome *</label>
            <input type="text" name="nome" value="{{ old('nome', $condominio->nome ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('nome') border-red-500 @enderror"
                   required>
            @error('nome') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">CNPJ</label>
            <input type="text" name="cnpj" value="{{ old('cnpj', $condominio->cnpj ?? '') }}"
                   placeholder="00.000.000/0000-00"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('cnpj') border-red-500 @enderror">
            @error('cnpj') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Matrícula no Cartório</label>
            <input type="text" name="matricula_cartorio" value="{{ old('matricula_cartorio', $condominio->matricula_cartorio ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Número de Registro</label>
            <input type="text" name="numero_registro" value="{{ old('numero_registro', $condominio->numero_registro ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
    </div>
</div>

{{-- Endereço --}}
<div class="bg-white shadow-sm rounded-lg p-6">
    <h3 class="text-base font-semibold text-gray-900 mb-4">Endereço</h3>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-6">
        <div class="sm:col-span-4">
            <label class="block text-sm font-medium text-gray-700">Logradouro</label>
            <input type="text" name="logradouro" value="{{ old('logradouro', $condominio->logradouro ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700">CEP</label>
            <input type="text" name="cep" value="{{ old('cep', $condominio->cep ?? '') }}"
                   placeholder="00000-000"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Bairro</label>
            <input type="text" name="bairro" value="{{ old('bairro', $condominio->bairro ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <div class="sm:col-span-3">
            <label class="block text-sm font-medium text-gray-700">Cidade</label>
            <input type="text" name="cidade" value="{{ old('cidade', $condominio->cidade ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <div class="sm:col-span-1">
            <label class="block text-sm font-medium text-gray-700">UF</label>
            <input type="text" name="estado" value="{{ old('estado', $condominio->estado ?? '') }}"
                   maxlength="2" placeholder="SP"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm uppercase">
        </div>
        <div class="sm:col-span-3">
            <label class="block text-sm font-medium text-gray-700">Município do Registro</label>
            <input type="text" name="municipio_registro" value="{{ old('municipio_registro', $condominio->municipio_registro ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
    </div>
</div>

{{-- Características físicas --}}
<div class="bg-white shadow-sm rounded-lg p-6">
    <h3 class="text-base font-semibold text-gray-900 mb-4">Características Físicas</h3>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div>
            <label class="block text-sm font-medium text-gray-700">Área Total (m²)</label>
            <input type="number" step="0.01" name="area_total" value="{{ old('area_total', $condominio->area_total ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Área Verde (m²)</label>
            <input type="number" step="0.01" name="area_verde" value="{{ old('area_verde', $condominio->area_verde ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Área de Vias (m²)</label>
            <input type="number" step="0.01" name="area_vias" value="{{ old('area_vias', $condominio->area_vias ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Total de Quadras</label>
            <input type="number" name="total_quadras" value="{{ old('total_quadras', $condominio->total_quadras ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Total de Lotes</label>
            <input type="number" name="total_lotes" value="{{ old('total_lotes', $condominio->total_lotes ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Zoneamento</label>
            <select name="zoneamento"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @foreach(['residencial', 'comercial', 'misto'] as $zone)
                    <option value="{{ $zone }}" {{ old('zoneamento', $condominio->zoneamento ?? 'residencial') === $zone ? 'selected' : '' }}>
                        {{ ucfirst($zone) }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

{{-- Documentação --}}
<div class="bg-white shadow-sm rounded-lg p-6">
    <h3 class="text-base font-semibold text-gray-900 mb-4">Documentação</h3>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-gray-700">Data de Aprovação (Prefeitura)</label>
            <input type="date" name="data_aprovacao_prefeitura"
                   value="{{ old('data_aprovacao_prefeitura', isset($condominio->data_aprovacao_prefeitura) ? $condominio->data_aprovacao_prefeitura->format('Y-m-d') : '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Data de Registro (Cartório)</label>
            <input type="date" name="data_registro_cartorio"
                   value="{{ old('data_registro_cartorio', isset($condominio->data_registro_cartorio) ? $condominio->data_registro_cartorio->format('Y-m-d') : '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
    </div>
</div>

{{-- Contato/Responsável --}}
<div class="bg-white shadow-sm rounded-lg p-6">
    <h3 class="text-base font-semibold text-gray-900 mb-4">Contato e Responsável</h3>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-gray-700">Incorporadora/Construtora</label>
            <input type="text" name="incorporadora" value="{{ old('incorporadora', $condominio->incorporadora ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Síndico</label>
            <input type="text" name="sindico" value="{{ old('sindico', $condominio->sindico ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Administradora</label>
            <input type="text" name="administradora" value="{{ old('administradora', $condominio->administradora ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Telefone</label>
            <input type="text" name="telefone" value="{{ old('telefone', $condominio->telefone ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700">E-mail</label>
            <input type="email" name="email" value="{{ old('email', $condominio->email ?? '') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
    </div>
</div>

{{-- Planta / Mapa --}}
<div class="bg-white shadow-sm rounded-lg p-6">
    <h3 class="text-base font-semibold text-gray-900 mb-4">Planta / Mapa do Loteamento</h3>

    @if($editing && ($condominio->planta_path ?? null))
        <div class="mb-4">
            <p class="text-sm text-gray-500 mb-2">Planta atual:</p>
            @php $ext = strtolower(pathinfo($condominio->planta_nome_original ?? '', PATHINFO_EXTENSION)); @endphp
            @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg']))
                <img src="{{ $condominio->plantaUrl() }}"
                     alt="Planta do loteamento"
                     class="max-h-64 rounded-lg border border-gray-200 object-contain bg-gray-50">
            @else
                <a href="{{ $condominio->plantaUrl() }}" target="_blank"
                   class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    {{ $condominio->planta_nome_original }}
                </a>
            @endif
            <label class="flex items-center gap-2 mt-3 text-sm text-red-600 cursor-pointer">
                <input type="checkbox" name="remover_planta" value="1" class="rounded border-gray-300 text-red-600">
                Remover planta atual
            </label>
        </div>
    @endif

    <div x-data="plantaUpload()" class="space-y-3">
        <label class="block text-sm font-medium text-gray-700">
            {{ ($editing && ($condominio->planta_path ?? null)) ? 'Substituir por nova planta' : 'Selecionar arquivo' }}
        </label>

        <div @dragover.prevent="dragging = true"
             @dragleave.prevent="dragging = false"
             @drop.prevent="handleDrop($event)"
             :class="dragging ? 'border-indigo-400 bg-indigo-50' : 'border-gray-300 bg-gray-50'"
             class="flex flex-col items-center justify-center border-2 border-dashed rounded-lg p-8 transition cursor-pointer"
             @click="$refs.fileInput.click()">

            <svg class="w-10 h-10 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>

            <template x-if="!preview && !fileName">
                <div class="text-center">
                    <p class="text-sm text-gray-600">Arraste a planta aqui ou <span class="text-indigo-600 font-medium">clique para selecionar</span></p>
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, PDF, SVG — máx. 20 MB</p>
                </div>
            </template>

            <template x-if="preview">
                <img :src="preview" alt="Preview" class="max-h-48 rounded object-contain">
            </template>

            <template x-if="!preview && fileName">
                <p class="text-sm text-gray-700 font-medium" x-text="fileName"></p>
            </template>

            <input type="file" name="planta" x-ref="fileInput" class="hidden"
                   accept=".jpg,.jpeg,.png,.gif,.pdf,.svg"
                   @change="handleFile($event)">
        </div>

        <template x-if="fileName">
            <p class="text-xs text-gray-500">Arquivo selecionado: <span class="font-medium" x-text="fileName"></span>
                <button type="button" @click="clear()" class="ml-2 text-red-500 hover:text-red-700">remover</button>
            </p>
        </template>
    </div>
</div>

@push('scripts')
<script>
function plantaUpload() {
    return {
        dragging: false,
        preview: null,
        fileName: null,
        handleFile(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.fileName = file.name;
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (ev) => this.preview = ev.target.result;
                reader.readAsDataURL(file);
            } else {
                this.preview = null;
            }
        },
        handleDrop(e) {
            this.dragging = false;
            const file = e.dataTransfer.files[0];
            if (!file) return;
            this.$refs.fileInput.files = e.dataTransfer.files;
            this.fileName = file.name;
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (ev) => this.preview = ev.target.result;
                reader.readAsDataURL(file);
            } else {
                this.preview = null;
            }
        },
        clear() {
            this.$refs.fileInput.value = '';
            this.fileName = null;
            this.preview = null;
        }
    }
}
</script>
@endpush
