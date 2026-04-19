{{--
  Partial reutilizável — Vínculos de Pessoas
  Parâmetros:
    $pessoas    — Collection de pessoas já vinculadas (com pivot)
    $papeis     — array ['papel' => 'Label'] disponíveis para o select
    $storeRoute — nome da rota para vincular (ex: 'lotes.pessoas.store', $model)
    $storeModel — o model pai (Lote ou Imovel)
    $destroyRouteName — nome da rota base para desvincular
    $titulo     — título da seção (default: 'Pessoas Vinculadas')
--}}
@php
    $titulo ??= 'Pessoas Vinculadas';
    $papelLabels = [
        'proprietario' => '👑 Proprietário',
        'comprador'    => '🛒 Comprador',
        'interessado'  => '🔍 Interessado',
        'locatario'    => '🏠 Locatário',
    ];
    $papelCores = [
        'proprietario' => 'green',
        'comprador'    => 'blue',
        'interessado'  => 'yellow',
        'locatario'    => 'purple',
    ];
@endphp

<div class="bg-white shadow-sm rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-base font-semibold text-gray-900">
            👥 {{ $titulo }}
            <span class="ml-1 text-sm font-normal text-gray-500">({{ $pessoas->count() }})</span>
        </h3>
    </div>

    {{-- Listagem --}}
    @if($pessoas->isNotEmpty())
    <ul class="divide-y divide-gray-100">
        @foreach($pessoas as $pessoa)
        @php
            $papel = $pessoa->pivot->papel;
            $cor   = $papelCores[$papel] ?? 'gray';
        @endphp
        <li class="flex items-center gap-4 px-6 py-3">
            <div class="w-9 h-9 rounded-full bg-{{ $cor }}-100 flex items-center justify-center shrink-0 text-sm font-bold text-{{ $cor }}-700">
                {{ mb_substr($pessoa->nome, 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <a href="{{ route('pessoas.show', $pessoa) }}"
                       class="text-sm font-medium text-gray-900 hover:text-indigo-600 truncate">
                        {{ $pessoa->nome }}
                    </a>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $cor }}-100 text-{{ $cor }}-700 font-medium">
                        {{ $papelLabels[$papel] ?? $papel }}
                    </span>
                </div>
                <div class="text-xs text-gray-400 flex flex-wrap gap-2 mt-0.5">
                    @if($pessoa->celular)
                    <span>📱 {{ $pessoa->celular }}</span>
                    @endif
                    @if($pessoa->email)
                    <span>✉ {{ $pessoa->email }}</span>
                    @endif
                    @if($pessoa->pivot->data_vinculo)
                    <span>📅 {{ \Carbon\Carbon::parse($pessoa->pivot->data_vinculo)->format('d/m/Y') }}</span>
                    @endif
                    @if($pessoa->pivot->obs)
                    <span class="italic">{{ $pessoa->pivot->obs }}</span>
                    @endif
                </div>
            </div>
            @if(auth()->user()->isAdmin())
            <form method="POST" action="{{ route($destroyRouteName, [$storeModel, $pessoa]) }}"
                  onsubmit="return confirm('Remover vínculo com {{ addslashes($pessoa->nome) }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-400 hover:text-red-600 text-xs">✕</button>
            </form>
            @endif
        </li>
        @endforeach
    </ul>
    @else
    <p class="px-6 py-6 text-center text-gray-400 text-sm">Nenhuma pessoa vinculada.</p>
    @endif

    {{-- Form de vínculo (admin) --}}
    @if(auth()->user()->isAdmin())
    <div class="border-t border-gray-100 px-6 py-4 bg-gray-50"
         x-data="{
             q: '',
             resultado: [],
             buscando: false,
             selecionada: null,
             buscar() {
                 if (this.q.length < 2) { this.resultado = []; return; }
                 this.buscando = true;
                 fetch('/api/pessoas/busca?q=' + encodeURIComponent(this.q))
                     .then(r => r.json())
                     .then(d => { this.resultado = d; this.buscando = false; });
             },
             selecionar(p) {
                 this.selecionada = p;
                 this.q = p.nome;
                 this.resultado = [];
             },
             limpar() {
                 this.selecionada = null;
                 this.q = '';
                 this.resultado = [];
             },
             confirmar() {
                 if (!this.selecionada) { alert('Selecione uma pessoa antes de vincular.'); return false; }
                 return confirm('Vincular ' + this.selecionada.nome + ' a este imóvel?');
             }
         }">

        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-3">Vincular Pessoa</p>

        <form method="POST" action="{{ route($storeRouteName, $storeModel) }}"
              @submit.prevent="if(confirmar()) $el.submit()">
            @csrf

            {{-- ID oculto preenchido ao selecionar --}}
            <input type="hidden" name="pessoa_id" :value="selecionada ? selecionada.id : ''">

            {{-- Campo de busca — visível enquanto nenhuma pessoa foi selecionada --}}
            <div x-show="!selecionada" class="relative mb-3">
                <label class="block text-xs text-gray-500 mb-1">Buscar por nome ou CPF/CNPJ</label>
                <div class="relative">
                    <input type="text" x-model="q"
                           @input.debounce.350ms="buscar()"
                           @keydown.escape="resultado = []"
                           placeholder="Digite nome ou CPF/CNPJ..."
                           autocomplete="off"
                           class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500 pr-20">
                    <span x-show="buscando"
                          class="absolute right-3 top-2 text-xs text-gray-400">
                        buscando…
                    </span>
                </div>

                {{-- Dropdown de resultados --}}
                <div x-show="resultado.length > 0"
                     class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-56 overflow-y-auto text-sm">
                    <template x-for="p in resultado" :key="p.id">
                        <button type="button" @click="selecionar(p)"
                                class="w-full text-left px-4 py-2.5 hover:bg-indigo-50 border-b border-gray-100 last:border-0">
                            <div class="font-medium text-gray-800" x-text="p.nome"></div>
                            <div class="flex flex-wrap gap-3 text-xs text-gray-400 mt-0.5">
                                <span x-text="p.tipo_label"></span>
                                <span x-show="p.cpf_cnpj" x-text="'CPF/CNPJ: ' + p.cpf_cnpj"></span>
                                <span x-show="p.celular" x-text="'📱 ' + p.celular"></span>
                            </div>
                        </button>
                    </template>
                </div>

                <p x-show="q.length >= 2 && !buscando && resultado.length === 0"
                   class="mt-1 text-xs text-gray-400 italic">
                    Nenhuma pessoa encontrada para "<span x-text="q"></span>".
                </p>
            </div>

            {{-- Card de confirmação — aparece após selecionar --}}
            <div x-show="selecionada" x-cloak
                 class="mb-3 p-3 bg-indigo-50 border border-indigo-200 rounded-lg flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold text-indigo-500 uppercase mb-1">Pessoa selecionada</p>
                    <p class="font-semibold text-gray-900 text-sm" x-text="selecionada?.nome"></p>
                    <div class="flex flex-wrap gap-3 text-xs text-gray-500 mt-0.5">
                        <span x-show="selecionada?.tipo_label" x-text="selecionada?.tipo_label"></span>
                        <span x-show="selecionada?.cpf_cnpj" x-text="'CPF/CNPJ: ' + selecionada?.cpf_cnpj"></span>
                        <span x-show="selecionada?.celular" x-text="'📱 ' + selecionada?.celular"></span>
                    </div>
                </div>
                <button type="button" @click="limpar()"
                        class="text-xs text-red-400 hover:text-red-600 font-medium shrink-0 mt-0.5">
                    ✕ Trocar
                </button>
            </div>

            {{-- Campos complementares + botão --}}
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Papel</label>
                    <select name="papel"
                            class="border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($papeis as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs text-gray-500 mb-1">Data vínculo</label>
                    <input type="date" name="data_vinculo"
                           class="border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <button type="submit"
                        :disabled="!selecionada"
                        :class="selecionada
                            ? 'bg-indigo-600 hover:bg-indigo-700 cursor-pointer'
                            : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
                        class="px-4 py-2 text-white rounded-md text-sm font-medium transition-colors">
                    ✔ Confirmar Vínculo
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
