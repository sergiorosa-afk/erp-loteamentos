<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editor — {{ $condominio->nome }}</title>
    @vite(['resources/css/app.css'])
    <script src="https://unpkg.com/konva@9/konva.min.js"></script>
    <style>
        body { margin: 0; background: #111827; color: #f9fafb; }
        #canvas-container { cursor: grab; }
        #canvas-container.tool-crosshair { cursor: crosshair; }
        #canvas-container.tool-default { cursor: default; }
        .tool-btn { @apply px-3 py-1.5 rounded text-sm font-medium transition-colors; background: #374151; color: #d1d5db; }
        .tool-btn:hover { background: #4b5563; }
        .tool-btn.active { background: #4f46e5; color: white; }
        .sidebar-item { padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 13px; color: #d1d5db; display: flex; justify-content: space-between; align-items: center; transition: background 0.15s; }
        .sidebar-item:hover { background: #374151; }
        .sidebar-item.active { background: #3730a3; color: white; }
        .badge { font-size: 10px; padding: 2px 6px; border-radius: 9999px; }
    </style>
</head>
<body class="h-screen flex flex-col overflow-hidden" style="background:#111827">

{{-- ═══ TOP TOOLBAR ═══ --}}
<div style="background:#1f2937;border-bottom:1px solid #374151;" class="flex items-center gap-3 px-4 py-2 shrink-0">
    <a href="{{ route('condominios.show', $condominio) }}"
       style="background:#374151;color:#d1d5db;" class="px-3 py-1.5 rounded text-sm hover:bg-gray-600 transition flex items-center gap-1">
        ← Voltar
    </a>

    <div style="width:1px;height:24px;background:#374151;"></div>

    <span class="text-sm font-semibold text-white truncate max-w-xs">{{ $condominio->nome }}</span>

    <div style="width:1px;height:24px;background:#374151;"></div>

    {{-- Tool buttons --}}
    <button data-tool="pan" class="tool-btn active" title="Pan — arrastar o mapa (P)">
        ✋ Pan
    </button>
    <button data-tool="select" class="tool-btn" title="Selecionar polígono (S)">
        ↖ Selecionar
    </button>
    <button data-tool="draw-quadra" class="tool-btn" title="Desenhar Quadra (Q)">
        ▭ Quadra
    </button>
    <button data-tool="draw-lote" class="tool-btn" title="Desenhar Lote (L) — selecione uma quadra primeiro">
        □ Lote
    </button>

    <div style="width:1px;height:24px;background:#374151;"></div>

    <button id="btn-fit" style="background:#374151;color:#d1d5db;" class="px-3 py-1.5 rounded text-sm hover:bg-gray-600 transition" title="Ajustar à tela">
        ⊞ Fit
    </button>

    <div class="flex-1"></div>

    <span id="unsaved-indicator" class="text-xs text-yellow-400 hidden">● Alterações não salvas</span>

    <button id="btn-save"
            style="background:#4f46e5;color:white;" class="px-4 py-1.5 rounded text-sm font-semibold hover:bg-indigo-500 transition">
        💾 Salvar
    </button>
</div>

{{-- ═══ MAIN CONTENT ═══ --}}
<div class="flex flex-1 overflow-hidden">

    {{-- ═══ LEFT SIDEBAR ═══ --}}
    <div style="width:260px;background:#1f2937;border-right:1px solid #374151;" class="flex flex-col overflow-hidden shrink-0">

        {{-- Quadras --}}
        <div style="border-bottom:1px solid #374151;" class="p-3 shrink-0">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Quadras</span>
                <button id="btn-nova-quadra" style="background:#374151;color:#a5b4fc;"
                        class="text-xs px-2 py-1 rounded hover:bg-indigo-800 transition">+ Nova</button>
            </div>
            <ul id="sidebar-quadras" class="space-y-0.5 max-h-48 overflow-y-auto"></ul>
        </div>

        {{-- Lotes --}}
        <div id="sidebar-lotes-panel" class="p-3 flex-1 overflow-hidden flex flex-col" style="display:none!important">
            <div class="flex justify-between items-center mb-2">
                <span id="sidebar-lotes-title" class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Lotes</span>
                <button id="btn-novo-lote" style="background:#374151;color:#86efac;"
                        class="text-xs px-2 py-1 rounded hover:bg-green-900 transition">+ Novo</button>
            </div>
            <ul id="sidebar-lotes" class="space-y-0.5 overflow-y-auto flex-1"></ul>
        </div>

        {{-- Legend --}}
        <div style="border-top:1px solid #374151;" class="p-3 shrink-0">
            <p class="text-xs text-gray-500 mb-2 font-semibold uppercase tracking-wider">Legenda</p>
            <div class="space-y-1">
                <div class="flex items-center gap-2"><span style="width:12px;height:12px;background:#4ade80;border-radius:2px;display:inline-block;"></span><span class="text-xs text-gray-400">Disponível</span></div>
                <div class="flex items-center gap-2"><span style="width:12px;height:12px;background:#facc15;border-radius:2px;display:inline-block;"></span><span class="text-xs text-gray-400">Reservado</span></div>
                <div class="flex items-center gap-2"><span style="width:12px;height:12px;background:#f87171;border-radius:2px;display:inline-block;"></span><span class="text-xs text-gray-400">Vendido</span></div>
                <div class="flex items-center gap-2"><span style="width:12px;height:12px;background:#c084fc;border-radius:2px;display:inline-block;"></span><span class="text-xs text-gray-400">Permutado</span></div>
                <div class="flex items-center gap-2"><span style="width:12px;height:12px;background:#818cf8;border-radius:2px;display:inline-block;"></span><span class="text-xs text-gray-400">Quadra</span></div>
            </div>
        </div>
    </div>

    {{-- ═══ CANVAS ═══ --}}
    <div id="canvas-container" class="flex-1 overflow-hidden"></div>

</div>

{{-- ═══ STATUS BAR ═══ --}}
<div style="background:#1f2937;border-top:1px solid #374151;" class="flex items-center px-4 py-1 shrink-0 gap-4">
    <span id="status-text" class="text-xs text-gray-400">Pronto</span>
    <span id="coords-text" class="text-xs text-gray-600 ml-auto"></span>
    <span id="zoom-text" class="text-xs text-gray-600">100%</span>
</div>

{{-- ═══ DIALOG OVERLAY ═══ --}}
<div id="dialog-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:50;align-items:center;justify-content:center;">
    <div style="background:#1f2937;border:1px solid #374151;border-radius:12px;width:360px;padding:24px;" id="dialog-box">
        <h3 id="dialog-title" class="text-base font-semibold text-white mb-4"></h3>
        <div id="dialog-body" class="mb-5"></div>
        <div class="flex justify-end gap-2">
            <button id="dialog-cancel" style="background:#374151;color:#d1d5db;" class="px-4 py-2 rounded text-sm hover:bg-gray-600 transition">Cancelar</button>
            <button id="dialog-confirm" style="background:#4f46e5;color:white;" class="px-4 py-2 rounded text-sm font-semibold hover:bg-indigo-500 transition">Confirmar</button>
        </div>
    </div>
</div>

<script>
// ╔══════════════════════════════════════════════════════════╗
// ║           ERP Loteamentos — Editor de Planta             ║
// ╚══════════════════════════════════════════════════════════╝

const DATA = {
    condominioId:  {{ $condominio->id }},
    condominioNome: @json($condominio->nome),
    plantaUrl:     @json($condominio->plantaUrl()),
    csrf:          @json(csrf_token()),
    routes: {
        salvar:      @json(route('condominios.editor.salvar', $condominio)),
        criarQuadra: @json(route('condominios.editor.quadra', $condominio)),
        criarLote:   @json(route('condominios.editor.lote', $condominio)),
    },
    quadras: @json($editorData),
};

// ─── Colors ────────────────────────────────────────────────
const COLORS = {
    quadra:    { fill: 'rgba(99,102,241,0.18)', stroke: '#818cf8', sel_fill: 'rgba(99,102,241,0.38)', sel_stroke: '#6366f1' },
    disponivel: { fill: 'rgba(74,222,128,0.25)', stroke: '#4ade80' },
    reservado:  { fill: 'rgba(250,204,21,0.25)', stroke: '#facc15' },
    vendido:    { fill: 'rgba(248,113,113,0.25)', stroke: '#f87171' },
    permutado:  { fill: 'rgba(192,132,252,0.25)', stroke: '#c084fc' },
};

// ─── State ──────────────────────────────────────────────────
let tool            = 'pan';
let selectedQuadraId = null;
let selectedLoteId   = null;
let drawingPoints    = [];
let isSaved          = true;
let imgNW = 1, imgNH = 1;

// ─── Stage setup ────────────────────────────────────────────
const container = document.getElementById('canvas-container');
const stage = new Konva.Stage({
    container: 'canvas-container',
    width: container.offsetWidth,
    height: container.offsetHeight,
});

const bgLayer      = new Konva.Layer();
const quadrasLayer = new Konva.Layer();
const lotesLayer   = new Konva.Layer();
const drawLayer    = new Konva.Layer();
stage.add(bgLayer, quadrasLayer, lotesLayer, drawLayer);

// ─── Image loading ──────────────────────────────────────────
function loadImage() {
    const img = new window.Image();
    img.crossOrigin = 'anonymous';
    img.onload = () => {
        imgNW = img.naturalWidth;
        imgNH = img.naturalHeight;
        const node = new Konva.Image({ image: img, x: 0, y: 0, width: imgNW, height: imgNH, listening: false });
        bgLayer.add(node);
        bgLayer.batchDraw();
        fitToCanvas();
        renderAll();
        setStatus('Planta carregada. Use as ferramentas para mapear quadras e lotes.', 'info');
    };
    img.onerror = () => setStatus('Erro ao carregar a imagem da planta.', 'error');
    img.src = DATA.plantaUrl;
}

function fitToCanvas() {
    const scaleX = stage.width()  / imgNW;
    const scaleY = stage.height() / imgNH;
    const scale  = Math.min(scaleX, scaleY) * 0.9;
    stage.scale({ x: scale, y: scale });
    stage.position({
        x: (stage.width()  - imgNW * scale) / 2,
        y: (stage.height() - imgNH * scale) / 2,
    });
    updateZoomText();
}

// ─── Zoom ───────────────────────────────────────────────────
stage.on('wheel', e => {
    e.evt.preventDefault();
    const scaleBy  = 1.12;
    const oldScale = stage.scaleX();
    const pointer  = stage.getPointerPosition();
    const anchor   = {
        x: (pointer.x - stage.x()) / oldScale,
        y: (pointer.y - stage.y()) / oldScale,
    };
    const dir      = e.evt.deltaY < 0 ? 1 : -1;
    const newScale = Math.min(Math.max(dir > 0 ? oldScale * scaleBy : oldScale / scaleBy, 0.02), 30);
    stage.scale({ x: newScale, y: newScale });
    stage.position({
        x: pointer.x - anchor.x * newScale,
        y: pointer.y - anchor.y * newScale,
    });
    updateZoomText();
    updateDrawLineWidths();
});

function updateZoomText() {
    document.getElementById('zoom-text').textContent = Math.round(stage.scaleX() * 100) + '%';
}

// ─── Helpers ────────────────────────────────────────────────
function getImagePos() {
    const pos = stage.getPointerPosition();
    const s   = stage.scaleX();
    const p   = stage.position();
    return { x: (pos.x - p.x) / s, y: (pos.y - p.y) / s };
}

function getBounds(pts) {
    const xs = pts.map(p => p[0]), ys = pts.map(p => p[1]);
    return {
        cx: (Math.min(...xs) + Math.max(...xs)) / 2,
        cy: (Math.min(...ys) + Math.max(...ys)) / 2,
    };
}

function stageToScreen(x, y) {
    const s = stage.scaleX(), p = stage.position();
    return { x: x * s + p.x, y: y * s + p.y };
}

// ─── Rendering ──────────────────────────────────────────────
function renderAll() { renderQuadras(); renderLotes(); }

function renderQuadras() {
    quadrasLayer.destroyChildren();
    DATA.quadras.forEach(q => {
        if (!q.poligono || q.poligono.length < 3) return;
        const isSel   = selectedQuadraId === q.id;
        const c       = COLORS.quadra;
        const flat    = q.poligono.flatMap(p => [p[0], p[1]]);
        const poly    = new Konva.Line({
            points: flat, closed: true,
            fill:        isSel ? c.sel_fill   : c.fill,
            stroke:      isSel ? c.sel_stroke : c.stroke,
            strokeWidth: isSel ? 3 : 2,
            id: `q-${q.id}`,
        });
        poly.on('click', e => { e.cancelBubble = true; selectQuadra(q.id); });
        poly.on('mouseover', () => { if (tool === 'select') { poly.opacity(0.8); quadrasLayer.batchDraw(); } });
        poly.on('mouseout',  () => { poly.opacity(1); quadrasLayer.batchDraw(); });
        quadrasLayer.add(poly);
        addLabel(quadrasLayer, q.poligono, `Q${q.codigo}`, '#a5b4fc');
    });
    quadrasLayer.batchDraw();
}

function renderLotes() {
    lotesLayer.destroyChildren();
    DATA.quadras.forEach(q => {
        (q.lotes || []).forEach(l => {
            if (!l.poligono || l.poligono.length < 3) return;
            const isSel = selectedLoteId === l.id;
            const c     = COLORS[l.situacao] || COLORS.disponivel;
            const flat  = l.poligono.flatMap(p => [p[0], p[1]]);
            const poly  = new Konva.Line({
                points: flat, closed: true,
                fill:        c.fill,
                stroke:      c.stroke,
                strokeWidth: isSel ? 3 : 1.5,
                opacity:     isSel ? 1 : 0.9,
                id: `l-${l.id}`,
            });
            poly.on('click', e => { e.cancelBubble = true; selectLote(l.id); });
            poly.on('mouseover', () => { if (tool === 'select') { poly.opacity(1); poly.strokeWidth(3); lotesLayer.batchDraw(); } });
            poly.on('mouseout',  () => { poly.opacity(isSel ? 1 : 0.9); poly.strokeWidth(isSel ? 3 : 1.5); lotesLayer.batchDraw(); });
            lotesLayer.add(poly);
            addLabel(lotesLayer, l.poligono, l.numero, '#f9fafb');
        });
    });
    lotesLayer.batchDraw();
}

function addLabel(layer, pts, text, color) {
    if (!pts || pts.length < 3) return;
    const b = getBounds(pts);
    const t = new Konva.Text({
        x: b.cx, y: b.cy, text, fontSize: 13, fill: color,
        fontStyle: 'bold', listening: false, shadowColor: '#000', shadowBlur: 3, shadowOpacity: 0.7,
    });
    t.offsetX(t.width() / 2);
    t.offsetY(t.height() / 2);
    layer.add(t);
}

// ─── Selection ──────────────────────────────────────────────
function selectQuadra(id) {
    selectedQuadraId = id;
    selectedLoteId   = null;
    renderAll();
    updateSidebar();
    const q = DATA.quadras.find(q => q.id === id);
    setStatus(`Quadra ${q?.codigo} selecionada. DEL para remover polígono.`, 'info');
}

function selectLote(id) {
    selectedLoteId = id;
    renderAll();
    let found;
    DATA.quadras.forEach(q => { const l = (q.lotes||[]).find(l => l.id === id); if (l) found = l; });
    if (found) setStatus(`Lote ${found.numero} selecionado (${found.situacao}). DEL para remover polígono.`, 'info');
}

// ─── Drawing ────────────────────────────────────────────────
let drawLineNode = null, tempLineNode = null, dotNodes = [];

function cancelDraw() {
    drawingPoints = [];
    drawLayer.destroyChildren();
    drawLayer.batchDraw();
    drawLineNode = null;
    tempLineNode = null;
    dotNodes     = [];
}

function addDrawPoint(x, y) {
    // Close if near first point (within 15 screen px)
    if (drawingPoints.length >= 3) {
        const fp  = drawingPoints[0];
        const s   = stage.scaleX();
        const dx  = (x - fp[0]) * s;
        const dy  = (y - fp[1]) * s;
        if (Math.sqrt(dx * dx + dy * dy) < 15) { finishDrawing(); return; }
    }

    drawingPoints.push([x, y]);

    const r = 5 / stage.scaleX();
    const dot = new Konva.Circle({ x, y, radius: r, fill: '#ef4444', listening: false });
    drawLayer.add(dot);
    dotNodes.push(dot);
    updateDrawLine();
}

function updateDrawLine() {
    if (drawLineNode) drawLineNode.destroy();
    if (drawingPoints.length < 2) { drawLayer.batchDraw(); return; }
    const sw = 2 / stage.scaleX();
    drawLineNode = new Konva.Line({
        points: drawingPoints.flatMap(p => [p[0], p[1]]),
        stroke: '#ef4444', strokeWidth: sw,
        dash: [10 / stage.scaleX(), 5 / stage.scaleX()],
        listening: false,
    });
    drawLayer.add(drawLineNode);
    drawLayer.batchDraw();
}

function updateDrawLineWidths() {
    if (!drawLineNode) return;
    const sw = 2 / stage.scaleX();
    drawLineNode.strokeWidth(sw);
    drawLineNode.dash([10 / stage.scaleX(), 5 / stage.scaleX()]);
    dotNodes.forEach(d => d.radius(5 / stage.scaleX()));
    drawLayer.batchDraw();
}

function finishDrawing() {
    if (drawingPoints.length < 3) {
        setStatus('Desenhe pelo menos 3 pontos. ESC para cancelar.', 'warning');
        return;
    }
    const points = [...drawingPoints];
    cancelDraw();

    if (tool === 'draw-quadra') {
        promptNewQuadra(points);
    } else if (tool === 'draw-lote') {
        if (!selectedQuadraId) { setStatus('Selecione uma quadra no painel antes de desenhar um lote.', 'warning'); return; }
        promptNewLote(points);
    }
}

// ─── Stage events ────────────────────────────────────────────
stage.on('click', e => {
    if (tool !== 'draw-quadra' && tool !== 'draw-lote') {
        // Deselect when clicking empty area in select mode
        if (tool === 'select' && (e.target === stage || e.target instanceof Konva.Image)) {
            selectedQuadraId = null;
            selectedLoteId   = null;
            renderAll();
            updateSidebar();
        }
        return;
    }
    if (e.target !== stage && !(e.target instanceof Konva.Image)) return;
    const pos = getImagePos();
    addDrawPoint(pos.x, pos.y);
});

stage.on('dblclick', e => {
    if (tool !== 'draw-quadra' && tool !== 'draw-lote') return;
    // Remove the extra point added by the second click of the dblclick
    if (drawingPoints.length > 0) drawingPoints.pop();
    if (drawLineNode) { drawLineNode.destroy(); drawLineNode = null; }
    if (dotNodes.length > 0) { dotNodes.pop()?.destroy(); }
    finishDrawing();
});

stage.on('mousemove', () => {
    if ((tool !== 'draw-quadra' && tool !== 'draw-lote') || drawingPoints.length === 0) {
        if (tempLineNode) { tempLineNode.destroy(); tempLineNode = null; drawLayer.batchDraw(); }
        return;
    }
    if (tempLineNode) { tempLineNode.destroy(); tempLineNode = null; }
    const pos  = getImagePos();
    const last = drawingPoints[drawingPoints.length - 1];
    tempLineNode = new Konva.Line({
        points: [last[0], last[1], pos.x, pos.y],
        stroke: '#ef4444', strokeWidth: 1.5 / stage.scaleX(), opacity: 0.5, listening: false,
    });
    drawLayer.add(tempLineNode);
    drawLayer.batchDraw();

    // Update coords
    document.getElementById('coords-text').textContent =
        `x: ${Math.round(pos.x)}, y: ${Math.round(pos.y)}`;
});

// ─── Keyboard shortcuts ──────────────────────────────────────
document.addEventListener('keydown', e => {
    const tag = document.activeElement?.tagName;
    if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT') return;

    if (e.key === 'Escape')  { cancelDraw(); setStatus('Cancelado.', 'info'); }
    if (e.key === 'Enter' && (tool === 'draw-quadra' || tool === 'draw-lote')) finishDrawing();

    if (e.key === 'p' || e.key === 'P') setTool('pan');
    if (e.key === 's' || e.key === 'S') setTool('select');
    if (e.key === 'q' || e.key === 'Q') setTool('draw-quadra');
    if (e.key === 'l' || e.key === 'L') setTool('draw-lote');

    if ((e.key === 'Delete' || e.key === 'Backspace')) {
        if (selectedQuadraId) { deletePolygon('quadra', selectedQuadraId); }
        else if (selectedLoteId) { deletePolygon('lote', selectedLoteId); }
    }
});

// ─── Delete polygon ──────────────────────────────────────────
function deletePolygon(type, id) {
    if (!confirm('Remover o polígono desenhado? (o registro não será excluído)')) return;
    if (type === 'quadra') {
        const q = DATA.quadras.find(q => q.id === id);
        if (q) { q.poligono = null; renderQuadras(); markUnsaved(); }
    } else {
        DATA.quadras.forEach(q => {
            const l = (q.lotes || []).find(l => l.id === id);
            if (l) { l.poligono = null; renderLotes(); markUnsaved(); }
        });
    }
}

// ─── Tool management ─────────────────────────────────────────
function setTool(t) {
    tool = t;
    stage.draggable(t === 'pan');
    if (t !== 'draw-quadra' && t !== 'draw-lote') cancelDraw();

    const cursors = { pan: '', 'draw-quadra': 'tool-crosshair', 'draw-lote': 'tool-crosshair', select: 'tool-default' };
    container.className = 'flex-1 overflow-hidden ' + (cursors[t] || '');

    document.querySelectorAll('[data-tool]').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.tool === t);
    });

    const hints = {
        pan:          'Pan: arraste para mover o mapa. Scroll para zoom.',
        select:       'Selecionar: clique em um polígono. DEL remove o polígono (mantém o registro).',
        'draw-quadra':'Quadra: clique para adicionar pontos. Duplo-clique, Enter ou clique no 1º ponto para fechar. ESC cancela.',
        'draw-lote':  'Lote: selecione uma quadra no painel, depois desenhe o lote.',
    };
    setStatus(hints[t] || '', 'info');
}

// ─── Prompts ─────────────────────────────────────────────────
function promptNewQuadra(points) {
    showDialog({
        title: 'Nova Quadra',
        fields: [
            { name: 'codigo', label: 'Código da Quadra', placeholder: 'Ex: A, B, 1, 01…' },
        ],
        onConfirm: async values => {
            try {
                const res  = await apiFetch(DATA.routes.criarQuadra, { codigo: values.codigo, poligono: points });
                const data = await res.json();
                if (!res.ok) throw new Error(data.errors?.codigo?.[0] || data.message || 'Erro.');
                DATA.quadras.push({ ...data, lotes: [] });
                selectedQuadraId = data.id;
                updateSidebar();
                renderAll();
                markUnsaved();
                setStatus(`Quadra "${data.codigo}" criada e mapeada.`, 'success');
            } catch (err) { setStatus(err.message, 'error'); }
        },
    });
}

function promptNewLote(points) {
    const quadra = DATA.quadras.find(q => q.id === selectedQuadraId);
    showDialog({
        title: `Novo Lote — Quadra ${quadra?.codigo ?? ''}`,
        fields: [
            { name: 'numero',   label: 'Número do Lote',  placeholder: 'Ex: 1, 2, 01…' },
            { name: 'situacao', label: 'Situação', type: 'select',
              options: ['disponivel', 'reservado', 'vendido', 'permutado'] },
        ],
        onConfirm: async values => {
            try {
                const res  = await apiFetch(DATA.routes.criarLote, {
                    quadra_id: selectedQuadraId, numero: values.numero, situacao: values.situacao, poligono: points,
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.errors?.numero?.[0] || data.message || 'Erro.');
                if (!quadra.lotes) quadra.lotes = [];
                quadra.lotes.push(data);
                renderLotes();
                updateSidebar();
                markUnsaved();
                setStatus(`Lote "${data.numero}" criado e mapeado.`, 'success');
            } catch (err) { setStatus(err.message, 'error'); }
        },
    });
}

// ─── Save ────────────────────────────────────────────────────
async function save() {
    const btn = document.getElementById('btn-save');
    btn.disabled = true;
    btn.textContent = 'Salvando…';

    const payload = {
        quadras: DATA.quadras.map(q => ({ id: q.id, poligono: q.poligono })),
        lotes:   DATA.quadras.flatMap(q => (q.lotes || []).map(l => ({ id: l.id, poligono: l.poligono }))),
    };

    try {
        const res  = await apiFetch(DATA.routes.salvar, payload);
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Erro ao salvar.');
        setStatus(data.message, 'success');
        isSaved = true;
        document.getElementById('unsaved-indicator').classList.add('hidden');
    } catch (err) {
        setStatus(err.message, 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = '💾 Salvar';
    }
}

function markUnsaved() {
    isSaved = false;
    document.getElementById('unsaved-indicator').classList.remove('hidden');
}

// ─── API helper ──────────────────────────────────────────────
function apiFetch(url, body) {
    return fetch(url, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': DATA.csrf },
        body:    JSON.stringify(body),
    });
}

// ─── Sidebar ─────────────────────────────────────────────────
function updateSidebar() {
    const list = document.getElementById('sidebar-quadras');
    list.innerHTML = '';
    DATA.quadras.forEach(q => {
        const li  = document.createElement('li');
        li.className = 'sidebar-item' + (selectedQuadraId === q.id ? ' active' : '');
        const hasMap = q.poligono && q.poligono.length >= 3;
        li.innerHTML = `
            <span class="flex items-center gap-1">
                <span style="color:${hasMap?'#a5b4fc':'#6b7280'}">${hasMap?'●':'○'}</span>
                Quadra ${q.codigo}
            </span>
            <span class="badge" style="background:#374151;color:#9ca3af">${(q.lotes||[]).length}</span>`;
        li.addEventListener('click', () => selectQuadra(q.id));
        list.appendChild(li);
    });
    updateLotesSidebar();
}

function updateLotesSidebar() {
    const panel = document.getElementById('sidebar-lotes-panel');
    const list  = document.getElementById('sidebar-lotes');

    if (!selectedQuadraId) { panel.style.display = 'none'; return; }
    const quadra = DATA.quadras.find(q => q.id === selectedQuadraId);
    if (!quadra)            { panel.style.display = 'none'; return; }

    panel.style.display = 'flex';
    document.getElementById('sidebar-lotes-title').textContent = `Lotes — Q${quadra.codigo}`;
    list.innerHTML = '';

    const bgMap = { disponivel:'#14532d', reservado:'#713f12', vendido:'#7f1d1d', permutado:'#4a044e' };

    (quadra.lotes || []).forEach(l => {
        const li  = document.createElement('li');
        li.className = 'sidebar-item' + (selectedLoteId === l.id ? ' active' : '');
        const hasMap = l.poligono && l.poligono.length >= 3;
        li.innerHTML = `
            <span class="flex items-center gap-1">
                <span style="color:${hasMap?'#4ade80':'#6b7280'}">${hasMap?'●':'○'}</span>
                Lote ${l.numero}
            </span>
            <span class="badge" style="background:${bgMap[l.situacao]||'#374151'};color:#e5e7eb">${l.situacao}</span>`;
        li.addEventListener('click', () => { selectLote(l.id); });
        list.appendChild(li);
    });
}

// ─── Dialog system ────────────────────────────────────────────
function showDialog({ title, fields, onConfirm }) {
    const overlay = document.getElementById('dialog-overlay');
    document.getElementById('dialog-title').textContent = title;

    const body = document.getElementById('dialog-body');
    body.innerHTML = '';

    fields.forEach(f => {
        const wrap  = document.createElement('div');
        wrap.className = 'mb-3';
        const label = document.createElement('label');
        label.className = 'block text-sm font-medium mb-1';
        label.style.color = '#d1d5db';
        label.textContent = f.label;
        wrap.appendChild(label);

        let el;
        if (f.type === 'select') {
            el = document.createElement('select');
            (f.options || []).forEach(opt => {
                const o = document.createElement('option');
                o.value = opt; o.textContent = opt.charAt(0).toUpperCase() + opt.slice(1);
                el.appendChild(o);
            });
        } else {
            el = document.createElement('input');
            el.type        = 'text';
            el.placeholder = f.placeholder || '';
        }
        el.name      = f.name;
        el.style.cssText = 'display:block;width:100%;background:#374151;border:1px solid #4b5563;border-radius:6px;padding:8px 12px;color:white;font-size:14px;outline:none;box-sizing:border-box;';
        wrap.appendChild(el);
        body.appendChild(wrap);
    });

    overlay.style.display = 'flex';
    setTimeout(() => body.querySelector('input,select')?.focus(), 50);

    const confirmAction = () => {
        const values = {};
        body.querySelectorAll('input,select').forEach(el => { values[el.name] = el.value.trim(); });
        const empty = fields.filter(f => f.type !== 'select' && !values[f.name]);
        if (empty.length) { body.querySelector('input')?.focus(); return; }
        overlay.style.display = 'none';
        onConfirm(values);
    };

    document.getElementById('dialog-confirm').onclick = confirmAction;
    document.getElementById('dialog-cancel').onclick  = () => { overlay.style.display = 'none'; };
    body.addEventListener('keydown', e => {
        if (e.key === 'Enter')  { e.preventDefault(); confirmAction(); }
        if (e.key === 'Escape') { overlay.style.display = 'none'; }
    }, { once: false });
}

// ─── Status bar ───────────────────────────────────────────────
function setStatus(msg, type = 'info') {
    const el = document.getElementById('status-text');
    el.textContent = msg;
    const colors = { error: '#f87171', success: '#4ade80', warning: '#facc15', info: '#9ca3af' };
    el.style.color = colors[type] || '#9ca3af';
}

// ─── Resize ───────────────────────────────────────────────────
window.addEventListener('resize', () => {
    stage.width(container.offsetWidth);
    stage.height(container.offsetHeight);
});

// ─── Button wiring ────────────────────────────────────────────
document.querySelectorAll('[data-tool]').forEach(btn =>
    btn.addEventListener('click', () => setTool(btn.dataset.tool))
);
document.getElementById('btn-save').addEventListener('click', save);
document.getElementById('btn-fit').addEventListener('click', fitToCanvas);

document.getElementById('btn-nova-quadra').addEventListener('click', () => {
    setTool('draw-quadra');
    setStatus('Desenhando nova quadra: clique para adicionar pontos, duplo-clique para fechar.', 'info');
});

document.getElementById('btn-novo-lote').addEventListener('click', () => {
    if (!selectedQuadraId) { setStatus('Selecione uma quadra primeiro.', 'warning'); return; }
    setTool('draw-lote');
    setStatus('Desenhando novo lote: clique para adicionar pontos, duplo-clique para fechar.', 'info');
});

// Warn before leaving with unsaved changes
window.addEventListener('beforeunload', e => {
    if (!isSaved) { e.preventDefault(); e.returnValue = ''; }
});

// ─── Init ─────────────────────────────────────────────────────
setTool('pan');
loadImage();
updateSidebar();
</script>
</body>
</html>
