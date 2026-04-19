{{-- Partial: _form.blade.php — usado em create e edit --}}
@php $pessoa ??= null; $end = $pessoa?->endereco; @endphp

{{-- ── DADOS PESSOAIS ─────────────────────────────────────────── --}}
<div class="bg-white shadow-sm rounded-lg divide-y divide-gray-100">
    <div class="px-5 py-4">
        <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Dados Pessoais</h3>
    </div>

    <div class="px-5 py-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

        {{-- Nome --}}
        <div class="lg:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nome completo <span class="text-red-500">*</span></label>
            <input type="text" name="nome" value="{{ old('nome', $pessoa?->nome) }}" required
                   class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('nome') border-red-400 @enderror">
            @error('nome')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Tipo --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo <span class="text-red-500">*</span></label>
            <select name="tipo" required
                    class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="lead"     {{ old('tipo', $pessoa?->tipo) === 'lead'     ? 'selected' : '' }}>Lead</option>
                <option value="prospect" {{ old('tipo', $pessoa?->tipo) === 'prospect' ? 'selected' : '' }}>Prospect</option>
                <option value="cliente"  {{ old('tipo', $pessoa?->tipo) === 'cliente'  ? 'selected' : '' }}>Cliente</option>
            </select>
        </div>

        {{-- CPF/CNPJ --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">CPF / CNPJ</label>
            <input type="text" name="cpf_cnpj" value="{{ old('cpf_cnpj', $pessoa?->cpf_cnpj) }}"
                   placeholder="000.000.000-00"
                   class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('cpf_cnpj') border-red-400 @enderror">
            @error('cpf_cnpj')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- RG --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">RG</label>
            <input type="text" name="rg" value="{{ old('rg', $pessoa?->rg) }}"
                   class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        {{-- Órgão Emissor RG --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Órgão Emissor (RG)</label>
            <input type="text" name="orgao_emissor_rg" value="{{ old('orgao_emissor_rg', $pessoa?->orgao_emissor_rg) }}"
                   placeholder="SSP/SP"
                   class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        {{-- Data de Nascimento --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Data de Nascimento</label>
            <input type="date" name="data_nascimento"
                   value="{{ old('data_nascimento', $pessoa?->data_nascimento?->format('Y-m-d')) }}"
                   class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        {{-- Estado Civil --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Estado Civil</label>
            <select name="estado_civil"
                    class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Não informado</option>
                @foreach(['solteiro'=>'Solteiro(a)','casado'=>'Casado(a)','divorciado'=>'Divorciado(a)','viuvo'=>'Viúvo(a)','uniao_estavel'=>'União Estável','separado'=>'Separado(a)'] as $val => $label)
                <option value="{{ $val }}" {{ old('estado_civil', $pessoa?->estado_civil) === $val ? 'selected' : '' }}>
                    {{ $label }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Profissão --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Profissão</label>
            <input type="text" name="profissao" value="{{ old('profissao', $pessoa?->profissao) }}"
                   class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        {{-- Nacionalidade --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nacionalidade</label>
            <input type="text" name="nacionalidade" value="{{ old('nacionalidade', $pessoa?->nacionalidade ?? 'Brasileiro(a)') }}"
                   class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

    </div>
</div>

{{-- ── CONTATO ─────────────────────────────────────────────────── --}}
<div class="bg-white shadow-sm rounded-lg divide-y divide-gray-100 mt-4">
    <div class="px-5 py-4">
        <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Contato</h3>
    </div>
    <div class="px-5 py-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

        {{-- Celular --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Celular</label>
            <input type="text" name="celular" value="{{ old('celular', $pessoa?->celular) }}"
                   placeholder="(00) 00000-0000"
                   class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        {{-- Telefone --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
            <input type="text" name="telefone" value="{{ old('telefone', $pessoa?->telefone) }}"
                   placeholder="(00) 0000-0000"
                   class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        {{-- E-mail --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
            <input type="email" name="email" value="{{ old('email', $pessoa?->email) }}"
                   class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-400 @enderror">
            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Origem --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Como chegou até nós</label>
            <input type="text" name="origem" value="{{ old('origem', $pessoa?->origem) }}"
                   placeholder="Indicação, site, evento..."
                   class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

    </div>
</div>

{{-- ── ENDEREÇO ─────────────────────────────────────────────────── --}}
<div class="bg-white shadow-sm rounded-lg divide-y divide-gray-100 mt-4">
    <div class="px-5 py-4">
        <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Endereço</h3>
    </div>
    <div class="px-5 py-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

        {{-- CEP --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
            <input type="text" name="cep" id="cep" value="{{ old('cep', $end?->cep) }}"
                   placeholder="00000-000" maxlength="9"
                   class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        {{-- Logradouro --}}
        <div class="lg:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Logradouro</label>
            <input type="text" name="logradouro" id="logradouro" value="{{ old('logradouro', $end?->logradouro) }}"
                   class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        {{-- Número --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Número</label>
            <input type="text" name="numero" value="{{ old('numero', $end?->numero) }}"
                   class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        {{-- Complemento --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
            <input type="text" name="complemento" value="{{ old('complemento', $end?->complemento) }}"
                   class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        {{-- Bairro --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
            <input type="text" name="bairro" id="bairro" value="{{ old('bairro', $end?->bairro) }}"
                   class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        {{-- Cidade --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
            <input type="text" name="cidade" id="cidade" value="{{ old('cidade', $end?->cidade) }}"
                   class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        {{-- Estado --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Estado (UF)</label>
            <input type="text" name="estado" id="estado" value="{{ old('estado', $end?->estado) }}"
                   placeholder="SP" maxlength="2"
                   class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500 uppercase">
        </div>

    </div>
</div>

{{-- ── OBSERVAÇÕES ─────────────────────────────────────────────── --}}
<div class="bg-white shadow-sm rounded-lg divide-y divide-gray-100 mt-4">
    <div class="px-5 py-4">
        <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">Observações</h3>
    </div>
    <div class="px-5 py-4">
        <textarea name="obs" rows="3"
                  class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
                  placeholder="Anotações gerais sobre esta pessoa...">{{ old('obs', $pessoa?->obs) }}</textarea>
    </div>
</div>

{{-- Auto-fill CEP via ViaCEP --}}
<script>
document.getElementById('cep')?.addEventListener('blur', function () {
    const cep = this.value.replace(/\D/g, '');
    if (cep.length !== 8) return;
    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(r => r.json())
        .then(d => {
            if (d.erro) return;
            document.getElementById('logradouro').value = d.logradouro ?? '';
            document.getElementById('bairro').value     = d.bairro     ?? '';
            document.getElementById('cidade').value     = d.localidade  ?? '';
            document.getElementById('estado').value     = d.uf          ?? '';
        })
        .catch(() => {});
});
</script>
