<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Apresentação — ERP Loteamentos</title>
    @vite(['resources/css/app.css'])
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body { background: #0f172a; color: #f8fafc; font-family: system-ui, sans-serif; min-height: 100vh; }
        .cond-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 16px;
            overflow: hidden;
            cursor: pointer;
            transition: transform .15s, box-shadow .15s;
            touch-action: manipulation;
        }
        .cond-card:active { transform: scale(.98); }
        .cond-card:hover  { box-shadow: 0 0 0 2px #6366f1; }
        .cond-img {
            width: 100%; aspect-ratio: 16/9;
            object-fit: cover; background: #334155;
        }
        .cond-placeholder {
            width: 100%; aspect-ratio: 16/9;
            background: linear-gradient(135deg, #1e3a5f, #334155);
            display: flex; align-items: center; justify-content: center;
            font-size: 56px;
        }
        .tap-btn {
            min-height: 44px; min-width: 44px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 10px; font-size: 15px; font-weight: 600;
            cursor: pointer; transition: opacity .15s; text-decoration: none;
        }
        .tap-btn:active { opacity: .75; }
    </style>
</head>
<body>

{{-- TOP BAR --}}
<div style="background:#1e293b;border-bottom:1px solid #334155;padding:14px 20px;"
     class="flex items-center justify-between">
    <div>
        <h1 class="text-white font-bold text-xl leading-none">🏡 Apresentação</h1>
        <p class="text-slate-400 text-sm mt-0.5">Selecione o condomínio</p>
    </div>
    <a href="{{ route('dashboard') }}"
       class="tap-btn px-4 py-2 text-slate-400 hover:text-white"
       style="background:#334155;">
        ← Painel
    </a>
</div>

@if(session('error'))
<div class="m-4 bg-red-900/50 border border-red-500 text-red-300 text-sm rounded-xl px-4 py-3">
    {{ session('error') }}
</div>
@endif

{{-- GRADE DE CONDOMÍNIOS --}}
<div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 max-w-screen-xl mx-auto">

    @forelse($condominios as $cond)
    <a href="{{ route('apresentacao.mapa', $cond) }}" class="cond-card block">

        @if($cond->planta_path)
        <img src="{{ $cond->plantaUrl() }}" alt="{{ $cond->nome }}"
             class="cond-img" loading="lazy">
        @else
        <div class="cond-placeholder">🗺️</div>
        @endif

        <div class="p-4">
            <h2 class="text-white font-bold text-lg leading-tight">{{ $cond->nome }}</h2>
            @if($cond->cidade)
            <p class="text-slate-400 text-sm mt-0.5">
                📍 {{ $cond->cidade }}{{ $cond->estado ? '/' . $cond->estado : '' }}
            </p>
            @endif
            <div class="mt-3 flex items-center gap-3 text-xs text-slate-400">
                <span>{{ $cond->quadras_count ?? 0 }} quadras</span>
                @if(! $cond->planta_path)
                <span class="text-yellow-500">⚠ sem planta</span>
                @endif
            </div>
        </div>

        {{-- Indicador de toque --}}
        <div style="border-top:1px solid #334155;padding:10px 16px;text-align:center;">
            <span class="text-indigo-400 text-sm font-semibold">Toque para abrir o mapa →</span>
        </div>
    </a>
    @empty
    <div class="col-span-full text-center py-20 text-slate-500">
        <div class="text-6xl mb-3">🏘️</div>
        <p>Nenhum condomínio cadastrado.</p>
    </div>
    @endforelse

</div>

</body>
</html>
