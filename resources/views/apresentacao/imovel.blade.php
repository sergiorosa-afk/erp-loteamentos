<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $imovel->tipoLabel() }} — Apresentação</title>
    @vite(['resources/css/app.css'])
    <style>
        * { -webkit-tap-highlight-color: transparent; box-sizing: border-box; }
        body { background:#0f172a; color:#f8fafc; font-family:system-ui,sans-serif; min-height:100vh; }

        /* Galeria de fotos */
        .gallery-main {
            width:100%; aspect-ratio:16/9; object-fit:cover;
            border-radius:0; background:#1e293b;
        }
        .thumb-rail { display:flex; gap:8px; padding:8px 16px; overflow-x:auto;
            scrollbar-width:none; -webkit-overflow-scrolling:touch; }
        .thumb-rail::-webkit-scrollbar { display:none; }
        .thumb { width:80px; height:55px; object-fit:cover; border-radius:8px;
            cursor:pointer; border:2px solid transparent; flex-shrink:0;
            transition:border-color .15s; }
        .thumb.active { border-color:#6366f1; }
        .thumb:active { opacity:.75; }

        /* Dados */
        .card { background:#1e293b; border:1px solid #334155; border-radius:16px; padding:20px; }
        .stat-item { display:flex; flex-direction:column; align-items:center;
            background:#0f172a; border-radius:10px; padding:12px 8px; text-align:center; }

        /* Toque */
        .touch-btn {
            display:inline-flex; align-items:center; justify-content:center;
            min-height:48px; border-radius:12px; padding:12px 20px;
            font-size:15px; font-weight:700; cursor:pointer;
            text-decoration:none; transition:opacity .15s; border:none;
        }
        .touch-btn:active { opacity:.75; }
        .touch-btn-ghost { background:#334155; color:#cbd5e1; }
    </style>
</head>
<body>

{{-- TOP BAR --}}
<div style="background:#1e293b;border-bottom:1px solid #334155;padding:12px 16px;"
     class="flex items-center gap-3">
    @if($imovel->lote?->quadra?->condominio)
    <a href="{{ route('apresentacao.mapa', $imovel->lote->quadra->condominio) }}"
       class="touch-btn touch-btn-ghost text-sm px-3 py-2" style="min-height:44px;">
        ← Mapa
    </a>
    @endif
    <div class="flex-1 min-w-0">
        <div class="text-white font-bold text-base leading-tight truncate">
            {{ $imovel->tipoLabel() }}
            @if($imovel->nome) · {{ $imovel->nome }} @endif
        </div>
        @if($imovel->lote?->quadra)
        <div class="text-slate-400 text-xs mt-0.5">
            Quadra {{ $imovel->lote->quadra->codigo }} — Lote {{ $imovel->lote->numero }}
        </div>
        @endif
    </div>
</div>

<div class="max-w-4xl mx-auto pb-12">

    {{-- Galeria de fotos --}}
    @php $midias = $imovel->midias->where('tipo', 'imagem'); @endphp
    @if($midias->isNotEmpty())
    <div x-data="galeria()" class="mb-0">
        <img :src="atual" alt="Foto principal"
             class="gallery-main w-full"
             style="max-height:480px;object-fit:cover;">

        @if($midias->count() > 1)
        <div class="thumb-rail">
            @foreach($midias as $i => $midia)
            <img src="{{ $midia->url() }}"
                 @click="trocar('{{ $midia->url() }}', {{ $i }})"
                 :class="idx === {{ $i }} ? 'active' : ''"
                 class="thumb" alt="">
            @endforeach
        </div>
        @endif
    </div>
    <script>
    function galeria() {
        return {
            atual: @json($midias->first()?->url()),
            idx: 0,
            trocar(url, i) { this.atual = url; this.idx = i; },
        };
    }
    </script>
    @else
    <div style="width:100%;aspect-ratio:16/9;background:#1e293b;display:flex;align-items:center;justify-content:center;font-size:80px;">
        🏠
    </div>
    @endif

    <div class="p-4 space-y-5">

        {{-- Proprietário(s) --}}
        @php $proprietarios = $imovel->pessoas->filter(fn($p) => $p->pivot->papel === 'proprietario'); @endphp
        @if($proprietarios->isNotEmpty())
        <div class="card">
            <h2 class="text-slate-400 text-xs font-semibold uppercase tracking-wider mb-3">Proprietário</h2>
            @foreach($proprietarios as $prop)
            <div class="flex items-center gap-3">
                <div style="width:44px;height:44px;border-radius:50%;background:#1e3a5f;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:800;color:#93c5fd;flex-shrink:0;">
                    {{ mb_substr($prop->nome, 0, 1) }}
                </div>
                <div>
                    <p class="text-white font-bold text-base">{{ $prop->nome }}</p>
                    @if($prop->celular)
                    <p class="text-slate-400 text-sm">📱 {{ $prop->celular }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Stats rápidas --}}
        <div class="card">
            <h2 class="text-slate-400 text-xs font-semibold uppercase tracking-wider mb-4">Características</h2>
            <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                @if($imovel->area_construida)
                <div class="stat-item">
                    <span class="text-2xl font-bold text-white">{{ number_format($imovel->area_construida, 0, ',', '.') }}</span>
                    <span class="text-xs text-slate-400 mt-1">m² construído</span>
                </div>
                @endif
                @if($imovel->area_total)
                <div class="stat-item">
                    <span class="text-2xl font-bold text-white">{{ number_format($imovel->area_total, 0, ',', '.') }}</span>
                    <span class="text-xs text-slate-400 mt-1">m² total</span>
                </div>
                @endif
                @if($imovel->quartos)
                <div class="stat-item">
                    <span class="text-2xl font-bold text-white">{{ $imovel->quartos }}</span>
                    <span class="text-xs text-slate-400 mt-1">quartos</span>
                </div>
                @endif
                @if($imovel->suites)
                <div class="stat-item">
                    <span class="text-2xl font-bold text-indigo-400">{{ $imovel->suites }}</span>
                    <span class="text-xs text-slate-400 mt-1">suítes</span>
                </div>
                @endif
                @if($imovel->banheiros)
                <div class="stat-item">
                    <span class="text-2xl font-bold text-white">{{ $imovel->banheiros }}</span>
                    <span class="text-xs text-slate-400 mt-1">banheiros</span>
                </div>
                @endif
                @if($imovel->vagas_garagem)
                <div class="stat-item">
                    <span class="text-2xl font-bold text-white">{{ $imovel->vagas_garagem }}</span>
                    <span class="text-xs text-slate-400 mt-1">vagas</span>
                </div>
                @endif
                @if($imovel->ano_construcao)
                <div class="stat-item">
                    <span class="text-2xl font-bold text-white">{{ $imovel->ano_construcao }}</span>
                    <span class="text-xs text-slate-400 mt-1">ano</span>
                </div>
                @endif
                @if($imovel->padrao_acabamento)
                <div class="stat-item">
                    <span class="text-lg font-bold text-yellow-400 capitalize">{{ $imovel->padrao_acabamento }}</span>
                    <span class="text-xs text-slate-400 mt-1">padrão</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Descrição --}}
        @if($imovel->descricao)
        <div class="card">
            <h2 class="text-slate-400 text-xs font-semibold uppercase tracking-wider mb-3">Descrição</h2>
            <p class="text-slate-200 text-sm leading-relaxed">{{ $imovel->descricao }}</p>
        </div>
        @endif

        {{-- Evolução de Valores --}}
        @php
            $valorizacao = $imovel->historicos
                ->whereIn('tipo', ['compra','venda','avaliacao','permuta'])
                ->whereNotNull('valor')
                ->sortBy('data')
                ->values();
        @endphp
        @if($valorizacao->isNotEmpty())
        <div class="card">
            <h2 class="text-slate-400 text-xs font-semibold uppercase tracking-wider mb-4">📈 Evolução de Valor</h2>

            @if($valorizacao->count() >= 2)
            @php
                $maxVal   = $valorizacao->max('valor');
                $minVal   = $valorizacao->min('valor');
                $range    = $maxVal - $minVal ?: 1;
                $primeiro = $valorizacao->first()->valor;
                $ultimo   = $valorizacao->last()->valor;
                $variacao = $primeiro > 0 ? (($ultimo - $primeiro) / $primeiro) * 100 : 0;
            @endphp
            {{-- Gráfico de barras --}}
            <div class="flex items-end gap-3 overflow-x-auto pb-2 mb-4" style="min-height:110px;">
                @foreach($valorizacao as $v)
                @php
                    $pct  = $range > 0 ? (($v->valor - $minVal) / $range) : 0.5;
                    $barH = max(20, intval($pct * 70)) + 24;
                    $isLast = $loop->last;
                @endphp
                <div class="flex flex-col items-center gap-1 min-w-16 flex-shrink-0">
                    <span class="text-xs font-bold {{ $isLast ? 'text-indigo-300' : 'text-slate-400' }} whitespace-nowrap">
                        R$ {{ number_format($v->valor / 1000, 0, ',', '.') }}k
                    </span>
                    <div class="w-12 rounded-t {{ $isLast ? 'bg-indigo-500' : 'bg-slate-600' }} transition-all"
                         style="height:{{ $barH }}px;"
                         title="{{ $v->tipoLabel() }}: R$ {{ number_format($v->valor, 2, ',', '.') }}">
                    </div>
                    <span class="text-xs text-slate-500 whitespace-nowrap">{{ $v->data?->format('m/Y') }}</span>
                </div>
                @endforeach
            </div>

            {{-- Resumo variação --}}
            <div class="flex gap-4 text-xs text-slate-400 border-t border-slate-700 pt-3 mb-4">
                <span>Primeiro: <strong class="text-slate-200">R$ {{ number_format($primeiro, 0, ',', '.') }}</strong></span>
                <span>Atual: <strong class="text-indigo-300">R$ {{ number_format($ultimo, 0, ',', '.') }}</strong></span>
                <span class="{{ $variacao >= 0 ? 'text-green-400' : 'text-red-400' }} font-semibold">
                    {{ $variacao >= 0 ? '▲' : '▼' }} {{ number_format(abs($variacao), 1) }}%
                </span>
            </div>
            @endif

            {{-- Lista de eventos --}}
            <h3 class="text-slate-400 text-xs font-semibold uppercase tracking-wider mb-3">Eventos registrados</h3>
            <div class="space-y-2">
                @foreach($imovel->historicos->sortBy('data') as $hist)
                @php
                    $cores = [
                        'compra'    => ['dot' => 'bg-blue-500',   'label' => 'text-blue-400'],
                        'venda'     => ['dot' => 'bg-green-500',  'label' => 'text-green-400'],
                        'avaliacao' => ['dot' => 'bg-purple-500', 'label' => 'text-purple-400'],
                        'reforma'   => ['dot' => 'bg-orange-500', 'label' => 'text-orange-400'],
                        'locacao'   => ['dot' => 'bg-yellow-500', 'label' => 'text-yellow-400'],
                        'permuta'   => ['dot' => 'bg-teal-500',   'label' => 'text-teal-400'],
                        'inventario'=> ['dot' => 'bg-gray-500',   'label' => 'text-gray-400'],
                    ];
                    $c = $cores[$hist->tipo] ?? ['dot' => 'bg-slate-500', 'label' => 'text-slate-400'];
                @endphp
                <div class="flex items-start gap-3 py-2 border-b border-slate-700 last:border-0">
                    <div class="mt-1.5 w-2 h-2 rounded-full {{ $c['dot'] }} flex-shrink-0"></div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-semibold {{ $c['label'] }} uppercase">
                                {{ $hist->tipoLabel() }}
                            </span>
                            @if($hist->valor)
                            <span class="text-white font-bold text-sm">
                                R$ {{ number_format($hist->valor, 0, ',', '.') }}
                            </span>
                            @endif
                            @if($hist->data)
                            <span class="text-slate-500 text-xs">{{ $hist->data->format('d/m/Y') }}</span>
                            @endif
                        </div>
                        @if($hist->descricao)
                        <p class="text-slate-400 text-xs mt-0.5">{{ $hist->descricao }}</p>
                        @endif
                        @if($hist->proprietario_anterior || $hist->proprietario_atual)
                        <p class="text-slate-500 text-xs mt-0.5">
                            @if($hist->proprietario_anterior){{ $hist->proprietario_anterior }} @endif
                            @if($hist->proprietario_anterior && $hist->proprietario_atual) → @endif
                            @if($hist->proprietario_atual)<span class="text-slate-300">{{ $hist->proprietario_atual }}</span>@endif
                        </p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Localização --}}
        @if($imovel->cidade || $imovel->logradouro)
        <div class="card">
            <h2 class="text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2">Localização</h2>
            <p class="text-white text-sm">
                @if($imovel->logradouro) {{ $imovel->logradouro }}{{ $imovel->numero_endereco ? ', ' . $imovel->numero_endereco : '' }} — @endif
                {{ $imovel->cidade }}{{ $imovel->estado ? '/' . $imovel->estado : '' }}
            </p>
        </div>
        @endif

        {{-- Botão voltar grande --}}
        @if($imovel->lote?->quadra?->condominio)
        <a href="{{ route('apresentacao.mapa', $imovel->lote->quadra->condominio) }}"
           class="touch-btn touch-btn-ghost w-full justify-center" style="width:100%;">
            ← Voltar ao Mapa
        </a>
        @endif

    </div>
</div>

{{-- Alpine.js para galeria --}}
<script defer src="https://unpkg.com/alpinejs@3/dist/cdn.min.js"></script>

</body>
</html>
