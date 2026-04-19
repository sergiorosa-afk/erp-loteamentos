{{--
    Partial reutilizável: modal visualizador de documentos (PDF / imagem / outro)
    ─────────────────────────────────────────────────────────────────────────────
    Como usar:
      1. Inclua este partial UMA VEZ em qualquer view que precise do visualizador:
            @include('partials._doc_viewer_modal')

      2. Para abrir o modal, dispare o evento global Alpine.js de qualquer botão:
            @click="$dispatch('open-doc-viewer', {
                url  : '{{ route('alguma.rota.visualizar', $doc) }}',
                tipo : 'pdf',      // 'pdf' | 'imagem' | 'outro'
                nome : '{{ $doc->nome_original }}'
            })"

    O partial ouve @open-doc-viewer.window e renderiza o conteúdo apropriado.
--}}

<div x-data="docViewerModal()"
     @open-doc-viewer.window="abrir($event.detail)"
     x-show="aberto"
     x-cloak
     @keydown.escape.window="fechar()"
     class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6"
     style="background: rgba(0,0,0,0.82);"
     @click.self="fechar()">

    <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-5xl flex flex-col"
         style="max-height: 92vh;">

        {{-- Cabeçalho --}}
        <div class="flex items-center gap-3 px-5 py-3 border-b border-gray-200 shrink-0">
            <span class="text-lg" x-text="doc.tipo === 'pdf' ? '📄' : (doc.tipo === 'imagem' ? '🖼️' : '📎')"></span>
            <p class="flex-1 text-sm font-medium text-gray-700 truncate min-w-0" x-text="doc.nome"></p>
            <a :href="doc.downloadUrl || doc.url"
               class="text-xs text-indigo-600 hover:text-indigo-800 font-medium shrink-0 px-2 py-1 rounded hover:bg-indigo-50">
                ⬇ Baixar
            </a>
            <button @click="fechar()"
                    class="text-gray-400 hover:text-gray-700 text-xl leading-none shrink-0 ml-1"
                    title="Fechar (Esc)">
                ✕
            </button>
        </div>

        {{-- Corpo --}}
        <div class="flex-1 overflow-auto min-h-0">

            {{-- PDF --}}
            <template x-if="doc.tipo === 'pdf'">
                <iframe :src="doc.url"
                        class="w-full"
                        style="height: 80vh; border: none;">
                </iframe>
            </template>

            {{-- Imagem --}}
            <template x-if="doc.tipo === 'imagem'">
                <div class="flex items-center justify-center p-4 bg-gray-50" style="min-height: 50vh;">
                    <img :src="doc.url"
                         :alt="doc.nome"
                         class="max-w-full object-contain rounded shadow"
                         style="max-height: 78vh;">
                </div>
            </template>

            {{-- Outro (não visualizável) --}}
            <template x-if="doc.tipo === 'outro'">
                <div class="flex flex-col items-center justify-center gap-4 p-12 text-center" style="min-height: 30vh;">
                    <span class="text-6xl">📎</span>
                    <p class="text-gray-600 text-sm" x-text="doc.nome"></p>
                    <p class="text-gray-400 text-xs">Este tipo de arquivo não pode ser visualizado diretamente.<br>Faça o download para abrir.</p>
                    <a :href="doc.downloadUrl || doc.url"
                       class="mt-2 px-5 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 font-medium">
                        ⬇ Baixar arquivo
                    </a>
                </div>
            </template>

        </div>
    </div>
</div>

<script>
function docViewerModal() {
    return {
        aberto: false,
        doc: { url: '', downloadUrl: '', tipo: 'pdf', nome: '' },

        abrir(detail) {
            this.doc = {
                url:         detail.url         ?? '',
                downloadUrl: detail.downloadUrl ?? detail.url ?? '',
                tipo:        detail.tipo        ?? 'outro',
                nome:        detail.nome        ?? 'Documento',
            };
            this.aberto = true;
            document.body.style.overflow = 'hidden';
        },

        fechar() {
            this.aberto = false;
            this.doc = { url: '', downloadUrl: '', tipo: 'pdf', nome: '' };
            document.body.style.overflow = '';
        },
    };
}
</script>
