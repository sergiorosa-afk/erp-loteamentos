<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mapa — {{ $condominio->nome }}</title>
    @vite(['resources/css/app.css'])
    <script src="https://unpkg.com/konva@9/konva.min.js"></script>
    <style>
        body { margin:0; background:#0f172a; color:#f8fafc; }
        #canvas-container { cursor: grab; }
        #canvas-container.grabbing { cursor: grabbing; }
        .filter-btn { padding:6px 14px; border-radius:9999px; font-size:12px; font-weight:600; cursor:pointer; border:2px solid transparent; transition:all .15s; }
        .filter-btn.active { border-color: white; }
        .stat-bar { height:6px; border-radius:3px; transition: width .4s; }

        /* Painel lateral */
        #lote-panel {
            position:absolute; right:0; top:0; bottom:0; width:280px;
            background:#1e293b; border-left:1px solid #334155;
            display:flex; flex-direction:column;
            transform: translateX(100%);
            transition: transform .25s cubic-bezier(.4,0,.2,1);
            z-index:20;
        }
        #lote-panel.open { transform: translateX(0); }
        #lote-panel .panel-header {
            padding:14px 16px 12px;
            border-bottom:1px solid #334155;
            display:flex; align-items:center; justify-content:space-between;
        }
        #lote-panel .panel-body { padding:16px; overflow-y:auto; flex:1; }
        #lote-panel .panel-footer {
            padding:12px 16px;
            border-top:1px solid #334155;
            display:flex; flex-direction:column; gap:8px;
        }
        .panel-btn {
            display:block; width:100%; padding:9px 14px;
            border-radius:7px; font-size:13px; font-weight:600;
            text-align:center; text-decoration:none; cursor:pointer;
            transition: opacity .15s;
        }
        .panel-btn:hover { opacity:.85; }
        .panel-btn-primary { background:#4f46e5; color:white; }
        .panel-btn-secondary { background:#334155; color:#cbd5e1; }
        .panel-btn-ghost { background:transparent; border:1px solid #334155; color:#94a3b8; }
    </style>
</head>
<body class="h-screen flex flex-col overflow-hidden" style="background:#0f172a">

{{-- TOP BAR --}}
<div style="background:#1e293b;border-bottom:1px solid #334155;" class="flex items-center gap-3 px-5 py-3 shrink-0 flex-wrap">
    <a href="{{ route('condominios.show', $condominio) }}"
       style="background:#334155;color:#94a3b8;" class="px-3 py-1.5 rounded text-sm hover:bg-slate-600 transition">
        ← Voltar
    </a>
    <div class="flex flex-col">
        <span class="text-white font-semibold text-sm leading-none">{{ $condominio->nome }}</span>
        @if($condominio->cidade)
        <span class="text-slate-400 text-xs mt-0.5">{{ $condominio->cidade }}{{ $condominio->estado ? '/' . $condominio->estado : '' }}</span>
        @endif
    </div>

    <div style="width:1px;height:28px;background:#334155;" class="mx-1"></div>

    {{-- Filtros por situação --}}
    <div class="flex items-center gap-2 flex-wrap">
        <button class="filter-btn active" data-filter="all"
                style="background:#334155;color:#94a3b8">Todos ({{ $stats['total'] }})</button>
        <button class="filter-btn" data-filter="disponivel"
                style="background:#14532d;color:#86efac">Disponível ({{ $stats['disponivel'] }})</button>
        <button class="filter-btn" data-filter="reservado"
                style="background:#713f12;color:#fde68a">Reservado ({{ $stats['reservado'] }})</button>
        <button class="filter-btn" data-filter="vendido"
                style="background:#7f1d1d;color:#fca5a5">Vendido ({{ $stats['vendido'] }})</button>
        <button class="filter-btn" data-filter="permutado"
                style="background:#4a044e;color:#e9d5ff">Permutado ({{ $stats['permutado'] }})</button>
        @if($stats['com_imovel'] > 0)
        <button class="filter-btn" data-filter="com_imovel"
                style="background:#1e3a5f;color:#93c5fd">🏠 Com Imóvel ({{ $stats['com_imovel'] }})</button>
        @endif
    </div>

    <div class="flex-1"></div>

    <button id="btn-fit" style="background:#334155;color:#94a3b8;"
            class="px-3 py-1.5 rounded text-sm hover:bg-slate-600 transition">⊞ Fit</button>
    @if($condominio->planta_path)
    <a href="{{ route('condominios.editor', $condominio) }}"
       style="background:#4f46e5;color:white;" class="px-4 py-1.5 rounded text-sm font-semibold hover:bg-indigo-500 transition">
        ✏ Editor
    </a>
    @endif
</div>

{{-- MAIN --}}
<div class="flex flex-1 overflow-hidden">

    {{-- SIDEBAR STATS --}}
    <div style="width:240px;background:#1e293b;border-right:1px solid #334155;" class="flex flex-col p-4 gap-4 overflow-y-auto shrink-0">

        <div>
            <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-3">Resumo</p>
            @php
                $pct = fn($n) => $stats['total'] > 0 ? round($n / $stats['total'] * 100) : 0;
            @endphp

            @foreach([
                ['disponivel', 'Disponível', '#4ade80', '#14532d'],
                ['reservado',  'Reservado',  '#facc15', '#713f12'],
                ['vendido',    'Vendido',    '#f87171', '#7f1d1d'],
                ['permutado',  'Permutado',  '#c084fc', '#4a044e'],
            ] as [$key, $label, $color, $bg])
            <div class="mb-3">
                <div class="flex justify-between items-center mb-1">
                    <span class="text-xs text-slate-300">{{ $label }}</span>
                    <span class="text-sm font-bold" style="color:{{ $color }}">{{ $stats[$key] }}</span>
                </div>
                <div style="background:#334155;border-radius:3px;height:6px;">
                    <div class="stat-bar" style="width:{{ $pct($stats[$key]) }}%;background:{{ $color }};"></div>
                </div>
                <p class="text-right text-xs text-slate-500 mt-0.5">{{ $pct($stats[$key]) }}%</p>
            </div>
            @endforeach

            <div style="border-top:1px solid #334155;" class="pt-3 mt-1">
                <div class="flex justify-between">
                    <span class="text-xs text-slate-400">Total de lotes</span>
                    <span class="text-sm font-bold text-white">{{ $stats['total'] }}</span>
                </div>
                <div class="flex justify-between mt-1">
                    <span class="text-xs text-slate-400">Com imóvel</span>
                    <span class="text-sm font-bold" style="color:#93c5fd">{{ $stats['com_imovel'] }}</span>
                </div>
                <div class="flex justify-between mt-1">
                    <span class="text-xs text-slate-400">Sem polígono</span>
                    <span class="text-sm font-bold" style="color:{{ $stats['sem_mapa'] > 0 ? '#f87171' : '#4ade80' }}">{{ $stats['sem_mapa'] }}</span>
                </div>
            </div>
        </div>

        {{-- Lista de quadras --}}
        <div>
            <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-2">Quadras</p>
            <ul class="space-y-1">
                @foreach($condominio->quadras as $quadra)
                <li>
                    <button data-quadra="{{ $quadra->id }}"
                            class="quadra-btn w-full text-left px-3 py-2 rounded text-xs text-slate-300 hover:bg-slate-700 transition flex justify-between">
                        <span>Quadra {{ $quadra->codigo }}</span>
                        <span style="color:#818cf8">{{ $quadra->lotes->count() }}</span>
                    </button>
                </li>
                @endforeach
            </ul>
        </div>

        {{-- Legenda imóvel --}}
        <div style="border-top:1px solid #334155;" class="pt-3">
            <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold mb-2">Legenda</p>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#60a5fa;border:2px solid white;"></span>
                Com imóvel cadastrado
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-400">
                <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#334155;border:2px solid #475569;"></span>
                Sem imóvel
            </div>
        </div>

    </div>

    {{-- CANVAS + PANEL --}}
    <div class="flex-1 relative overflow-hidden">
        <div id="canvas-container" class="w-full h-full"></div>

        {{-- Tooltip --}}
        <div id="tooltip" style="display:none;position:absolute;pointer-events:none;z-index:10;
             background:rgba(15,23,42,.95);border:1px solid #334155;border-radius:8px;padding:10px 14px;min-width:160px;">
        </div>

        {{-- Painel lateral de lote selecionado --}}
        <div id="lote-panel">
            <div class="panel-header">
                <span id="panel-title" style="font-weight:700;font-size:15px;color:white;"></span>
                <button id="close-panel" style="color:#64748b;font-size:18px;line-height:1;background:none;border:none;cursor:pointer;">✕</button>
            </div>
            <div class="panel-body" id="panel-body"></div>
            <div class="panel-footer" id="panel-footer"></div>
        </div>
    </div>

</div>

{{-- STATUS BAR --}}
<div style="background:#1e293b;border-top:1px solid #334155;" class="flex items-center px-5 py-1 gap-4 shrink-0">
    <span id="status-text" class="text-xs text-slate-400">Pronto</span>
    <span class="ml-auto text-xs text-slate-600" id="zoom-text">100%</span>
</div>

<script>
// ══════════════════════════════════════════════════════
// ERP Loteamentos — Visualizador de Mapa
// ══════════════════════════════════════════════════════

const DATA = {
    plantaUrl: @json($condominio->plantaUrl()),
    editorUrl: @json(route('condominios.editor', $condominio)),
    quadras: @json($mapaData),
    isAdmin: @json($isAdmin),
};

const COLORS = {
    quadra:     { fill:'rgba(99,102,241,0.12)', stroke:'#6366f1' },
    disponivel: { fill:'rgba(74,222,128,0.3)',  stroke:'#4ade80' },
    reservado:  { fill:'rgba(250,204,21,0.3)',  stroke:'#facc15' },
    vendido:    { fill:'rgba(248,113,113,0.3)', stroke:'#f87171' },
    permutado:  { fill:'rgba(192,132,252,0.3)', stroke:'#c084fc' },
};

// Overlay para lotes com imóvel: stroke mais brilhante/grosso
const IMOVEL_STROKE_EXTRA = 1.5; // adicional ao strokeWidth normal

let activeFilter  = 'all';
let selectedLote  = null;
let highlightedQuadraId = null;
let imgNW = 1, imgNH = 1;

// ── Konva setup ──────────────────────────────────────
const container = document.getElementById('canvas-container');
const stage = new Konva.Stage({
    container: 'canvas-container',
    width: container.offsetWidth,
    height: container.offsetHeight,
});

const bgLayer       = new Konva.Layer();
const quadrasLayer  = new Konva.Layer();
const lotesLayer    = new Konva.Layer();
const markersLayer  = new Konva.Layer(); // marcadores de imóvel
stage.add(bgLayer, quadrasLayer, lotesLayer, markersLayer);

// ── Image ───────────────────────────────────────────
function loadImage() {
    const img = new window.Image();
    img.crossOrigin = 'anonymous';
    img.onload = () => {
        imgNW = img.naturalWidth; imgNH = img.naturalHeight;
        const node = new Konva.Image({ image: img, x:0, y:0, width:imgNW, height:imgNH, listening:false });
        bgLayer.add(node); bgLayer.batchDraw();
        fitToCanvas();
        renderAll();
        setStatus('Passe o mouse nos lotes para ver detalhes. Clique para abrir o painel.');
    };
    img.src = DATA.plantaUrl;
}

function fitToCanvas() {
    const s = Math.min(stage.width() / imgNW, stage.height() / imgNH) * 0.92;
    stage.scale({ x:s, y:s });
    stage.position({ x:(stage.width() - imgNW * s) / 2, y:(stage.height() - imgNH * s) / 2 });
    updateZoom();
}

// ── Zoom ────────────────────────────────────────────
stage.on('wheel', e => {
    e.evt.preventDefault();
    const by = 1.12, old = stage.scaleX(), ptr = stage.getPointerPosition();
    const anchor = { x:(ptr.x - stage.x()) / old, y:(ptr.y - stage.y()) / old };
    const ns = Math.min(Math.max(e.evt.deltaY < 0 ? old * by : old / by, 0.02), 25);
    stage.scale({ x:ns, y:ns });
    stage.position({ x: ptr.x - anchor.x * ns, y: ptr.y - anchor.y * ns });
    updateZoom();
});
stage.draggable(true);
stage.on('dragstart', () => container.classList.add('grabbing'));
stage.on('dragend',   () => container.classList.remove('grabbing'));

// Clique fora de lotes → fecha painel
stage.on('click', e => {
    if (e.target === stage) closePanel();
});

function updateZoom() {
    document.getElementById('zoom-text').textContent = Math.round(stage.scaleX() * 100) + '%';
}

// ── Render ───────────────────────────────────────────
function renderAll() { renderQuadras(); renderLotes(); }

function renderQuadras() {
    quadrasLayer.destroyChildren();
    DATA.quadras.forEach(q => {
        if (!q.poligono || q.poligono.length < 3) return;
        const isHL = highlightedQuadraId === q.id;
        const poly = new Konva.Line({
            points: q.poligono.flatMap(p => [p[0], p[1]]),
            closed: true,
            fill:        isHL ? 'rgba(99,102,241,0.25)' : COLORS.quadra.fill,
            stroke:      COLORS.quadra.stroke,
            strokeWidth: isHL ? 3 : 1.5,
            listening: false,
        });
        quadrasLayer.add(poly);

        const b = bounds(q.poligono);
        const t = new Konva.Text({
            x: b.cx, y: b.cy, text: `Q${q.codigo}`,
            fontSize: 14, fill: '#818cf8', fontStyle: 'bold',
            shadowColor: '#000', shadowBlur: 4, shadowOpacity: .8, listening: false,
        });
        t.offsetX(t.width() / 2); t.offsetY(t.height() / 2);
        quadrasLayer.add(t);
    });
    quadrasLayer.batchDraw();
}

function renderLotes() {
    lotesLayer.destroyChildren();
    markersLayer.destroyChildren();

    DATA.quadras.forEach(q => {
        (q.lotes || []).forEach(l => {
            if (!l.poligono || l.poligono.length < 3) return;

            // Filtro situação
            if (activeFilter !== 'all' && activeFilter !== 'com_imovel' && l.situacao !== activeFilter) return;
            // Filtro com imóvel
            if (activeFilter === 'com_imovel' && !l.tem_imovel) return;

            const c         = COLORS[l.situacao] || COLORS.disponivel;
            const isSelected = selectedLote && selectedLote.id === l.id;

            const poly = new Konva.Line({
                points: l.poligono.flatMap(p => [p[0], p[1]]),
                closed: true,
                fill:        isSelected ? 'rgba(255,255,255,0.15)' : c.fill,
                stroke:      isSelected ? '#ffffff' : (l.tem_imovel ? '#60a5fa' : c.stroke),
                strokeWidth: isSelected ? 3 : (l.tem_imovel ? 2.5 : 1.5),
                id: `lote-${l.id}`,
                dash: l.unificado ? [8, 4] : null,
            });

            poly.on('mouseover', e => showTooltip(e, l, q));
            poly.on('mousemove', e => moveTooltip(e));
            poly.on('mouseout',  () => hideTooltip());
            poly.on('click', e => {
                e.cancelBubble = true;
                panToPolygon(l.poligono);
                openPanel(l, q);
            });

            lotesLayer.add(poly);

            // Número do lote
            const b = bounds(l.poligono);
            const hasOwner = l.proprietario_nome && l.proprietario_nome.length > 0;
            const t = new Konva.Text({
                x: b.cx, y: b.cy - (hasOwner ? 7 : 0), text: l.numero,
                fontSize: 11, fill: '#f1f5f9', fontStyle: 'bold',
                shadowColor: '#000', shadowBlur: 3, shadowOpacity: .9, listening: false,
            });
            t.offsetX(t.width() / 2); t.offsetY(t.height() / 2);
            lotesLayer.add(t);

            // Nome do proprietário (se houver)
            if (hasOwner) {
                const pt = new Konva.Text({
                    x: b.cx, y: b.cy + 5,
                    text: l.proprietario_nome,
                    fontSize: 8, fill: '#93c5fd', fontStyle: 'bold',
                    shadowColor: '#000', shadowBlur: 3, shadowOpacity: .95,
                    listening: false, ellipsis: true, width: 110, align: 'center',
                });
                pt.offsetX(pt.width() / 2);
                lotesLayer.add(pt);
            }

            // Marcador de imóvel — círculo azul no canto superior do centróide
            if (l.tem_imovel) {
                const dot = new Konva.Circle({
                    x: b.cx + 8, y: b.cy - 10,
                    radius: 5,
                    fill: '#60a5fa',
                    stroke: '#1e3a5f',
                    strokeWidth: 1.5,
                    listening: false,
                });
                markersLayer.add(dot);
            }
        });
    });

    lotesLayer.batchDraw();
    markersLayer.batchDraw();
}

// ── Painel lateral ───────────────────────────────────
const panel      = document.getElementById('lote-panel');
const panelTitle = document.getElementById('panel-title');
const panelBody  = document.getElementById('panel-body');
const panelFooter = document.getElementById('panel-footer');

function openPanel(lote, quadra) {
    selectedLote = lote;
    renderLotes(); // re-render para highlight

    const situacaoLabel = { disponivel:'Disponível', reservado:'Reservado', vendido:'Vendido', permutado:'Permutado' };
    const situacaoColor = { disponivel:'#4ade80', reservado:'#facc15', vendido:'#f87171', permutado:'#c084fc' };

    panelTitle.textContent = `Lote ${lote.numero}`;

    // Body
    let body = `
        <div style="font-size:12px;color:#64748b;margin-bottom:12px;">
            Quadra ${quadra.codigo}
            ${lote.unificado ? '<span style="background:#3730a3;color:#a5b4fc;padding:1px 6px;border-radius:4px;font-size:10px;margin-left:6px">unificado</span>' : ''}
        </div>
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:14px;">
            <span style="width:10px;height:10px;border-radius:50%;background:${situacaoColor[lote.situacao]};display:inline-block;flex-shrink:0;"></span>
            <span style="font-size:13px;font-weight:700;color:${situacaoColor[lote.situacao]}">${situacaoLabel[lote.situacao]}</span>
        </div>
    `;

    // Detalhes do lote
    const rows = [];
    if (lote.area)         rows.push(['Área', parseFloat(lote.area).toLocaleString('pt-BR') + ' m²']);
    if (lote.valor_tabela) rows.push(['Valor tabela', 'R$ ' + parseFloat(lote.valor_tabela).toLocaleString('pt-BR', {minimumFractionDigits:2})]);
    if (lote.codigo_interno) rows.push(['Cód. interno', lote.codigo_interno]);

    if (rows.length) {
        body += `<div style="background:#0f172a;border-radius:8px;padding:10px;margin-bottom:14px;">`;
        rows.forEach(([k, v]) => {
            body += `<div style="display:flex;justify-content:space-between;align-items:center;padding:3px 0;font-size:12px;">
                <span style="color:#64748b">${k}</span>
                <span style="color:#e2e8f0;font-weight:600">${v}</span>
            </div>`;
        });
        body += `</div>`;
    }

    // Imóvel
    if (lote.tem_imovel) {
        body += `
            <div style="background:#1e3a5f;border:1px solid #1e40af;border-radius:8px;padding:12px;margin-bottom:6px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                    <span style="font-size:20px;">🏠</span>
                    <div>
                        <p style="font-size:13px;font-weight:700;color:#93c5fd">Imóvel Cadastrado</p>
                        ${lote.imovel_tipo ? `<p style="font-size:11px;color:#60a5fa">${lote.imovel_tipo}</p>` : ''}
                    </div>
                </div>
                ${lote.imovel_valor ? `
                <div style="font-size:12px;color:#7dd3fc;text-align:center;background:rgba(0,0,0,.3);border-radius:6px;padding:6px;">
                    <span style="font-size:10px;display:block;color:#4b94c4;margin-bottom:2px;">Valor de Mercado</span>
                    <strong>R$ ${parseFloat(lote.imovel_valor).toLocaleString('pt-BR', {minimumFractionDigits:2})}</strong>
                </div>` : ''}
            </div>
        `;
    } else {
        body += `
            <div style="background:#0f172a;border:1px solid #334155;border-radius:8px;padding:12px;text-align:center;margin-bottom:6px;">
                <p style="font-size:28px;margin-bottom:6px;">🏗️</p>
                <p style="font-size:12px;color:#64748b">Sem imóvel cadastrado</p>
            </div>
        `;
    }

    panelBody.innerHTML = body;

    // Footer — botões de ação
    let footer = '';

    if (lote.tem_imovel && lote.imovel_url) {
        footer += `<a href="${lote.imovel_url}" class="panel-btn panel-btn-primary">🏠 Ver Imóvel</a>`;
    }

    if (!lote.tem_imovel && DATA.isAdmin && lote.imovel_create_url) {
        footer += `<a href="${lote.imovel_create_url}" class="panel-btn panel-btn-primary">+ Cadastrar Imóvel</a>`;
    }

    footer += `<a href="${lote.lote_url}" class="panel-btn panel-btn-secondary">📋 Ver Ficha do Lote</a>`;

    panelFooter.innerHTML = footer;

    panel.classList.add('open');
}

function closePanel() {
    panel.classList.remove('open');
    selectedLote = null;
    renderLotes();
}

document.getElementById('close-panel').addEventListener('click', closePanel);

// ── Tooltip ──────────────────────────────────────────
const tooltip = document.getElementById('tooltip');

function showTooltip(e, lote, quadra) {
    const situacaoLabel = { disponivel:'Disponível', reservado:'Reservado', vendido:'Vendido', permutado:'Permutado' };
    const situacaoColor = { disponivel:'#4ade80', reservado:'#facc15', vendido:'#f87171', permutado:'#c084fc' };
    tooltip.innerHTML = `
        <div style="font-weight:700;font-size:14px;color:white;margin-bottom:4px">
            Lote ${lote.numero}${lote.unificado ? ' <span style="font-size:10px;background:#3730a3;padding:1px 5px;border-radius:4px">unificado</span>' : ''}
        </div>
        <div style="font-size:12px;color:#94a3b8;margin-bottom:6px">Quadra ${quadra.codigo}</div>
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px">
            <span style="width:8px;height:8px;border-radius:50%;background:${situacaoColor[lote.situacao]};display:inline-block"></span>
            <span style="font-size:12px;color:${situacaoColor[lote.situacao]};font-weight:600">${situacaoLabel[lote.situacao]}</span>
        </div>
        ${lote.area ? `<div style="font-size:11px;color:#64748b">Área: ${parseFloat(lote.area).toLocaleString('pt-BR')} m²</div>` : ''}
        ${lote.valor_tabela ? `<div style="font-size:11px;color:#64748b">Valor: R$ ${parseFloat(lote.valor_tabela).toLocaleString('pt-BR',{minimumFractionDigits:2})}</div>` : ''}
        ${lote.tem_imovel ? `<div style="font-size:11px;color:#60a5fa;margin-top:5px;display:flex;align-items:center;gap:4px">
            <span style="width:7px;height:7px;border-radius:50%;background:#60a5fa;display:inline-block;"></span>
            🏠 ${lote.imovel_tipo || 'Imóvel cadastrado'}
            ${lote.imovel_valor ? ` · R$ ${parseFloat(lote.imovel_valor).toLocaleString('pt-BR',{minimumFractionDigits:2})}` : ''}
        </div>` : ''}
        <div style="font-size:10px;color:#475569;margin-top:6px;border-top:1px solid #1e293b;padding-top:5px;">Clique para abrir</div>
    `;
    moveTooltip(e);
    tooltip.style.display = 'block';
}

function moveTooltip(e) {
    const rect = container.getBoundingClientRect();
    const x = e.evt.clientX - rect.left + 14;
    const y = e.evt.clientY - rect.top  - 10;
    const tw = tooltip.offsetWidth  || 200;
    const th = tooltip.offsetHeight || 100;
    tooltip.style.left = (x + tw > rect.width  ? x - tw - 28 : x) + 'px';
    tooltip.style.top  = (y + th > rect.height ? y - th + 20 : y) + 'px';
}

function hideTooltip() { tooltip.style.display = 'none'; }

// ── Pan to polygon ────────────────────────────────────
function panToPolygon(pts) {
    if (!pts || !pts.length) return;
    const b = bounds(pts);
    const s = stage.scaleX();
    // Se painel vai abrir, deslocar para a esquerda
    const panelOffset = 140; // metade de 280px de painel
    stage.position({
        x: stage.width()  / 2 - b.cx * s - panelOffset,
        y: stage.height() / 2 - b.cy * s,
    });
}

// ── Helpers ──────────────────────────────────────────
function bounds(pts) {
    const xs = pts.map(p => p[0]), ys = pts.map(p => p[1]);
    return {
        cx: (Math.min(...xs) + Math.max(...xs)) / 2,
        cy: (Math.min(...ys) + Math.max(...ys)) / 2,
    };
}

function setStatus(msg) {
    document.getElementById('status-text').textContent = msg;
}

// ── Filters ──────────────────────────────────────────
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        activeFilter = btn.dataset.filter;
        closePanel();
        renderLotes();
        const label = btn.textContent.trim();
        setStatus(activeFilter === 'all' ? 'Mostrando todos os lotes.' : `Filtrando: ${label}`);
    });
});

// ── Quadra highlight ────────────────────────────────
document.querySelectorAll('.quadra-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = parseInt(btn.dataset.quadra);
        highlightedQuadraId = highlightedQuadraId === id ? null : id;
        document.querySelectorAll('.quadra-btn').forEach(b => b.style.background = '');
        if (highlightedQuadraId) btn.style.background = '#3730a3';
        renderAll();
        const q = DATA.quadras.find(q => q.id === highlightedQuadraId);
        if (q?.poligono) panToPolygon(q.poligono);
    });
});

// ── Buttons ──────────────────────────────────────────
document.getElementById('btn-fit').addEventListener('click', fitToCanvas);

// ── Resize ───────────────────────────────────────────
window.addEventListener('resize', () => {
    stage.width(container.offsetWidth);
    stage.height(container.offsetHeight);
});

// ── Init ─────────────────────────────────────────────
loadImage();
</script>
</body>
</html>
