<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, user-scalable=no">
    <title>Mapa — {{ $condominio->nome }}</title>
    @vite(['resources/css/app.css'])
    <script src="https://unpkg.com/konva@9/konva.min.js"></script>
    <style>
        * { -webkit-tap-highlight-color: transparent; box-sizing: border-box; }
        html, body { margin:0; padding:0; height:100%; overflow:hidden;
            background:#0f172a; color:#f8fafc; font-family:system-ui,sans-serif; }
        #canvas-container { cursor:grab; touch-action:none; }
        #canvas-container.grabbing { cursor:grabbing; }

        /* Painel lateral touch-friendly */
        #info-panel {
            position:absolute; left:0; top:0; bottom:0; width:min(360px, 90vw);
            background:#1e293b; border-right:1px solid #334155;
            display:flex; flex-direction:column;
            transform:translateX(-100%);
            transition:transform .28s cubic-bezier(.4,0,.2,1);
            z-index:30; overflow:hidden;
        }
        #info-panel.open { transform:translateX(0); }

        .panel-header {
            padding:16px 20px 14px;
            border-bottom:1px solid #334155;
            display:flex; align-items:flex-start; justify-content:space-between; gap:10px;
        }
        .panel-body { padding:16px 20px; overflow-y:auto; flex:1; -webkit-overflow-scrolling:touch; }
        .panel-footer { padding:14px 20px; border-top:1px solid #334155; display:flex; flex-direction:column; gap:10px; }

        .touch-btn {
            display:block; width:100%; padding:14px 16px;
            border-radius:12px; font-size:15px; font-weight:700;
            text-align:center; text-decoration:none; cursor:pointer;
            transition:opacity .15s; border:none;
            min-height:48px; display:flex; align-items:center; justify-content:center;
        }
        .touch-btn:active { opacity:.75; }
        .touch-btn-primary   { background:#4f46e5; color:white; }
        .touch-btn-secondary { background:#334155; color:#cbd5e1; }

        /* Tooltip */
        #tooltip {
            display:none; position:absolute; pointer-events:none; z-index:20;
            background:rgba(15,23,42,.96); border:1px solid #334155;
            border-radius:12px; padding:12px 16px; min-width:180px; max-width:260px;
        }

        /* Botão Fit */
        #btn-fit {
            position:absolute; bottom:20px; right:20px; z-index:25;
            background:#1e293b; border:1px solid #334155; color:#94a3b8;
            border-radius:12px; padding:10px 16px; font-size:14px; font-weight:600;
            cursor:pointer; min-height:44px; min-width:44px;
            transition:background .15s;
        }
        #btn-fit:active { background:#334155; }

        /* Legenda */
        #legenda {
            position:absolute; top:16px; right:16px; z-index:25;
            background:rgba(30,41,59,.92); border:1px solid #334155;
            border-radius:12px; padding:10px 14px; font-size:12px;
        }
    </style>
</head>
<body>

{{-- TOP BAR --}}
<div style="background:#1e293b;border-bottom:1px solid #334155;padding:12px 16px;z-index:40;position:relative;"
     class="flex items-center gap-3 flex-wrap">
    <a href="{{ route('apresentacao.index') }}"
       style="background:#334155;color:#94a3b8;border-radius:10px;padding:10px 14px;min-height:44px;display:flex;align-items:center;"
       class="font-semibold text-sm">
        ← Voltar
    </a>
    <div>
        <span class="text-white font-bold text-base leading-none">{{ $condominio->nome }}</span>
        @if($condominio->cidade)
        <span class="text-slate-400 text-sm ml-2">
            {{ $condominio->cidade }}{{ $condominio->estado ? '/' . $condominio->estado : '' }}
        </span>
        @endif
    </div>
    <div class="ml-auto text-xs text-slate-500" id="zoom-text">100%</div>
</div>

{{-- CANVAS --}}
<div style="height:calc(100vh - 62px);position:relative;overflow:hidden;">
    <div id="canvas-container" class="w-full h-full"></div>

    {{-- Tooltip --}}
    <div id="tooltip"></div>

    {{-- Legenda --}}
    <div id="legenda">
        <p class="text-slate-400 font-semibold mb-2 uppercase tracking-wide" style="font-size:10px;">Situação</p>
        @foreach(['disponivel'=>['🟢','Disponível'],'reservado'=>['🟡','Reservado'],'vendido'=>['🔴','Vendido'],'permutado'=>['🟣','Permutado']] as $s=>[$e,$l])
        <div class="flex items-center gap-2 mb-1 text-slate-300">
            <span>{{ $e }}</span> <span>{{ $l }}</span>
        </div>
        @endforeach
        <div class="flex items-center gap-2 mt-2 text-blue-400">
            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#60a5fa;border:2px solid #1e3a5f;"></span>
            Com imóvel
        </div>
    </div>

    {{-- Painel de lote selecionado --}}
    <div id="info-panel">
        <div class="panel-header">
            <div>
                <div id="panel-lote" style="font-size:20px;font-weight:800;color:white;line-height:1.2;"></div>
                <div id="panel-prop" style="font-size:13px;color:#94a3b8;margin-top:4px;"></div>
            </div>
            <button id="close-panel" onclick="closePanel()"
                    style="background:#334155;border:none;border-radius:8px;padding:8px 12px;
                           color:#94a3b8;cursor:pointer;font-size:18px;min-width:44px;min-height:44px;">✕</button>
        </div>
        <div class="panel-body" id="panel-body"></div>
        <div class="panel-footer" id="panel-footer"></div>
    </div>

    {{-- Botão Fit --}}
    <button id="btn-fit" onclick="fitToCanvas()">⊞ Fit</button>
</div>

<script>
// ══════════════════════════════════════════════════════════════
// Apresentação — Mapa Konva (touch-friendly)
// ══════════════════════════════════════════════════════════════

const DATA = {
    plantaUrl: @json($condominio->plantaUrl()),
    quadras:   @json($mapaData),
};

const COLORS = {
    quadra:     { fill:'rgba(99,102,241,0.1)', stroke:'#6366f1' },
    disponivel: { fill:'rgba(74,222,128,0.25)',  stroke:'#4ade80' },
    reservado:  { fill:'rgba(250,204,21,0.25)',  stroke:'#facc15' },
    vendido:    { fill:'rgba(248,113,113,0.25)', stroke:'#f87171' },
    permutado:  { fill:'rgba(192,132,252,0.25)', stroke:'#c084fc' },
};
const SITUACAO_COLOR = { disponivel:'#4ade80', reservado:'#facc15', vendido:'#f87171', permutado:'#c084fc' };
const SITUACAO_LABEL = { disponivel:'Disponível', reservado:'Reservado', vendido:'Vendido', permutado:'Permutado' };

let imgNW = 1, imgNH = 1;
let selectedLote = null;
let lastDist = null; // pinch-to-zoom

const container = document.getElementById('canvas-container');
const stage = new Konva.Stage({
    container: 'canvas-container',
    width:  container.offsetWidth,
    height: container.offsetHeight,
});

const bgLayer      = new Konva.Layer();
const quadrasLayer = new Konva.Layer();
const lotesLayer   = new Konva.Layer();
const labelsLayer  = new Konva.Layer(); // proprietario_nome
const markerLayer  = new Konva.Layer();
stage.add(bgLayer, quadrasLayer, lotesLayer, labelsLayer, markerLayer);

// ── Imagem de planta ──────────────────────────────────────────
const img = new window.Image();
img.crossOrigin = 'anonymous';
img.onload = () => {
    imgNW = img.naturalWidth; imgNH = img.naturalHeight;
    const node = new Konva.Image({ image:img, x:0, y:0, width:imgNW, height:imgNH, listening:false });
    bgLayer.add(node); bgLayer.batchDraw();
    fitToCanvas();
    renderAll();
};
img.src = DATA.plantaUrl;

function fitToCanvas() {
    const s = Math.min(stage.width() / imgNW, stage.height() / imgNH) * 0.9;
    stage.scale({ x:s, y:s });
    stage.position({ x:(stage.width() - imgNW*s)/2, y:(stage.height() - imgNH*s)/2 });
    updateZoom();
}

// ── Zoom por roda do mouse ────────────────────────────────────
stage.on('wheel', e => {
    e.evt.preventDefault();
    const by = 1.12, old = stage.scaleX(), ptr = stage.getPointerPosition();
    const anchor = { x:(ptr.x - stage.x())/old, y:(ptr.y - stage.y())/old };
    const ns = Math.min(Math.max(e.evt.deltaY < 0 ? old*by : old/by, 0.02), 30);
    stage.scale({ x:ns, y:ns });
    stage.position({ x:ptr.x - anchor.x*ns, y:ptr.y - anchor.y*ns });
    updateZoom();
});

// ── Pinch-to-zoom (touch) ────────────────────────────────────
stage.on('touchmove', e => {
    const touches = e.evt.touches;
    if (touches.length !== 2) return;
    e.evt.preventDefault();

    const dx = touches[0].clientX - touches[1].clientX;
    const dy = touches[0].clientY - touches[1].clientY;
    const dist = Math.sqrt(dx*dx + dy*dy);

    if (lastDist !== null) {
        const ratio = dist / lastDist;
        const old   = stage.scaleX();
        const ns    = Math.min(Math.max(old * ratio, 0.02), 30);
        const cx    = (touches[0].clientX + touches[1].clientX) / 2;
        const cy    = (touches[0].clientY + touches[1].clientY) / 2;
        const ptr   = { x:cx, y:cy };
        const anchor = { x:(ptr.x - stage.x())/old, y:(ptr.y - stage.y())/old };
        stage.scale({ x:ns, y:ns });
        stage.position({ x:ptr.x - anchor.x*ns, y:ptr.y - anchor.y*ns });
        updateZoom();
    }
    lastDist = dist;
});
stage.on('touchend', () => { lastDist = null; });

stage.draggable(true);
stage.on('dragstart', () => container.classList.add('grabbing'));
stage.on('dragend',   () => container.classList.remove('grabbing'));
stage.on('click tap', e => { if (e.target === stage) closePanel(); });

function updateZoom() {
    document.getElementById('zoom-text').textContent = Math.round(stage.scaleX()*100) + '%';
}

function bounds(pts) {
    const xs = pts.map(p=>p[0]), ys = pts.map(p=>p[1]);
    return { cx:(Math.min(...xs)+Math.max(...xs))/2, cy:(Math.min(...ys)+Math.max(...ys))/2 };
}

// ── Render ────────────────────────────────────────────────────
function renderAll() { renderQuadras(); renderLotes(); }

function renderQuadras() {
    quadrasLayer.destroyChildren();
    DATA.quadras.forEach(q => {
        if (!q.poligono || q.poligono.length < 3) return;
        quadrasLayer.add(new Konva.Line({
            points: q.poligono.flatMap(p=>[p[0],p[1]]),
            closed:true, fill:COLORS.quadra.fill,
            stroke:COLORS.quadra.stroke, strokeWidth:1.5, listening:false,
        }));
        const b = bounds(q.poligono);
        const t = new Konva.Text({
            x:b.cx, y:b.cy, text:`Q${q.codigo}`,
            fontSize:14, fill:'#818cf8', fontStyle:'bold',
            shadowColor:'#000', shadowBlur:4, shadowOpacity:.8, listening:false,
        });
        t.offsetX(t.width()/2); t.offsetY(t.height()/2);
        quadrasLayer.add(t);
    });
    quadrasLayer.batchDraw();
}

function renderLotes() {
    lotesLayer.destroyChildren();
    labelsLayer.destroyChildren();
    markerLayer.destroyChildren();

    DATA.quadras.forEach(q => {
        (q.lotes||[]).forEach(l => {
            if (!l.poligono || l.poligono.length < 3) return;

            const c          = COLORS[l.situacao] || COLORS.disponivel;
            const isSelected = selectedLote && selectedLote.id === l.id;

            const poly = new Konva.Line({
                points: l.poligono.flatMap(p=>[p[0],p[1]]),
                closed:true,
                fill:        isSelected ? 'rgba(255,255,255,0.18)' : c.fill,
                stroke:      isSelected ? '#ffffff' : (l.tem_imovel ? '#60a5fa' : c.stroke),
                strokeWidth: isSelected ? 3.5 : (l.tem_imovel ? 2.5 : 1.5),
                dash: l.unificado ? [8,4] : null,
            });

            poly.on('mouseover', e => showTooltip(e, l, q));
            poly.on('mousemove', e => moveTooltip(e));
            poly.on('mouseout',  () => hideTooltip());
            poly.on('click tap', e => {
                e.cancelBubble = true;
                openPanel(l, q);
            });
            lotesLayer.add(poly);

            const b = bounds(l.poligono);

            // Número do lote
            const numText = new Konva.Text({
                x:b.cx, y:b.cy - (l.proprietario_nome ? 8 : 0),
                text: l.numero, fontSize:12, fill:'#f1f5f9',
                fontStyle:'bold', shadowColor:'#000', shadowBlur:3, shadowOpacity:.9, listening:false,
            });
            numText.offsetX(numText.width()/2); numText.offsetY(numText.height()/2);
            labelsLayer.add(numText);

            // Nome do proprietário sobre o polígono
            if (l.proprietario_nome) {
                const propText = new Konva.Text({
                    x:b.cx, y:b.cy + 8,
                    text: l.proprietario_nome,
                    fontSize:9, fill:'#93c5fd',
                    fontStyle:'bold',
                    shadowColor:'#000', shadowBlur:3, shadowOpacity:.95,
                    listening:false,
                    ellipsis:true,
                    width:120,
                    align:'center',
                });
                propText.offsetX(propText.width()/2);
                labelsLayer.add(propText);
            }

            // Marcador de imóvel
            if (l.tem_imovel) {
                markerLayer.add(new Konva.Circle({
                    x:b.cx + 10, y:b.cy - 12,
                    radius:5, fill:'#60a5fa',
                    stroke:'#1e3a5f', strokeWidth:1.5, listening:false,
                }));
            }
        });
    });

    lotesLayer.batchDraw();
    labelsLayer.batchDraw();
    markerLayer.batchDraw();
}

// ── Tooltip ───────────────────────────────────────────────────
const tooltip = document.getElementById('tooltip');

function showTooltip(e, lote, quadra) {
    tooltip.innerHTML = `
        <div style="font-weight:800;font-size:15px;color:white;margin-bottom:3px">
            Lote ${lote.numero}
        </div>
        <div style="font-size:12px;color:#94a3b8;margin-bottom:6px">Quadra ${quadra.codigo}</div>
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:5px">
            <span style="width:8px;height:8px;border-radius:50%;background:${SITUACAO_COLOR[lote.situacao]};display:inline-block"></span>
            <span style="font-size:13px;color:${SITUACAO_COLOR[lote.situacao]};font-weight:700">${SITUACAO_LABEL[lote.situacao]}</span>
        </div>
        ${lote.proprietario_nome ? `<div style="font-size:12px;color:#93c5fd;font-weight:600">👤 ${lote.proprietario_nome}</div>` : ''}
        ${lote.area ? `<div style="font-size:11px;color:#64748b;margin-top:4px">Área: ${parseFloat(lote.area).toLocaleString('pt-BR')} m²</div>` : ''}
        ${lote.tem_imovel ? `<div style="font-size:11px;color:#60a5fa;margin-top:4px">🏠 ${lote.imovel_tipo||'Imóvel cadastrado'}</div>` : ''}
        <div style="font-size:10px;color:#475569;margin-top:6px;border-top:1px solid #1e293b;padding-top:5px">Toque para abrir</div>
    `;
    moveTooltip(e);
    tooltip.style.display = 'block';
}
function moveTooltip(e) {
    const r = container.getBoundingClientRect();
    const x = e.evt.clientX - r.left + 14;
    const y = e.evt.clientY - r.top  + 14;
    tooltip.style.left = (x + tooltip.offsetWidth  > r.width  ? x - tooltip.offsetWidth  - 20 : x) + 'px';
    tooltip.style.top  = (y + tooltip.offsetHeight > r.height ? y - tooltip.offsetHeight - 20 : y) + 'px';
}
function hideTooltip() { tooltip.style.display = 'none'; }

// ── Painel ────────────────────────────────────────────────────
const panel      = document.getElementById('info-panel');
const panelLote  = document.getElementById('panel-lote');
const panelProp  = document.getElementById('panel-prop');
const panelBody  = document.getElementById('panel-body');
const panelFooter= document.getElementById('panel-footer');

function openPanel(lote, quadra) {
    selectedLote = lote;
    renderLotes();
    hideTooltip();

    panelLote.textContent = `Lote ${lote.numero} — Q${quadra.codigo}`;
    panelProp.textContent  = lote.proprietario_nome ? `👤 ${lote.proprietario_nome}` : '';

    let body = `
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;padding:10px;background:#0f172a;border-radius:10px;">
            <span style="width:12px;height:12px;border-radius:50%;background:${SITUACAO_COLOR[lote.situacao]};display:inline-block;flex-shrink:0;"></span>
            <span style="color:${SITUACAO_COLOR[lote.situacao]};font-weight:700;font-size:14px">${SITUACAO_LABEL[lote.situacao]}</span>
        </div>
    `;

    if (lote.area) {
        body += `
        <div style="background:#0f172a;border-radius:10px;padding:10px;margin-bottom:12px;">
            <div style="display:flex;justify-content:space-between;font-size:13px;">
                <span style="color:#64748b">Área do lote</span>
                <span style="color:#e2e8f0;font-weight:700">${parseFloat(lote.area).toLocaleString('pt-BR')} m²</span>
            </div>
        </div>`;
    }

    if (lote.tem_imovel) {
        body += `
        <div style="background:#1e3a5f;border:1px solid #1e40af;border-radius:10px;padding:14px;margin-bottom:6px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                <span style="font-size:28px">🏠</span>
                <div>
                    <p style="font-size:14px;font-weight:800;color:#93c5fd">${lote.imovel_tipo||'Imóvel'}</p>
                    ${lote.imovel_area ? `<p style="font-size:12px;color:#60a5fa">${parseFloat(lote.imovel_area).toLocaleString('pt-BR')} m²</p>` : ''}
                </div>
            </div>
            ${lote.imovel_quartos ? `<div style="font-size:13px;color:#7dd3fc">🛏 ${lote.imovel_quartos} quartos</div>` : ''}
        </div>`;
    } else {
        body += `
        <div style="background:#0f172a;border:1px solid #334155;border-radius:10px;padding:14px;text-align:center;">
            <div style="font-size:32px;margin-bottom:6px">🏗️</div>
            <p style="font-size:13px;color:#64748b">Sem imóvel cadastrado</p>
        </div>`;
    }

    panelBody.innerHTML = body;

    let footer = '';
    if (lote.tem_imovel && lote.imovel_url) {
        footer += `<a href="${lote.imovel_url}" class="touch-btn touch-btn-primary">🏠 Ver Imóvel Completo</a>`;
    }
    footer += `<button onclick="closePanel()" class="touch-btn touch-btn-secondary">✕ Fechar</button>`;

    panelFooter.innerHTML = footer;
    panel.classList.add('open');
}

function closePanel() {
    panel.classList.remove('open');
    selectedLote = null;
    renderLotes();
}

// Redimensionamento
window.addEventListener('resize', () => {
    stage.width(container.offsetWidth);
    stage.height(container.offsetHeight);
    renderAll();
});
</script>
</body>
</html>
