<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\LoteDocumento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    public function store(Request $request, Lote $lote): RedirectResponse
    {
        $request->validate([
            'arquivo' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:20480',
            'tipo'    => 'required|in:escritura,planta,memorial,contrato,procuracao,outro',
        ]);

        $file = $request->file('arquivo');
        $path = $file->store("documentos/{$lote->id}", 'public');

        $lote->documentos()->create([
            'nome_original' => $file->getClientOriginalName(),
            'path'          => $path,
            'tipo'          => $request->tipo,
            'tamanho_bytes' => $file->getSize(),
            'mime_type'     => $file->getMimeType(),
            'uploaded_by'   => auth()->id(),
        ]);

        return back()->with('success', 'Documento enviado com sucesso!');
    }

    public function download(LoteDocumento $documento)
    {
        if (! Storage::disk('public')->exists($documento->path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return Storage::disk('public')->download($documento->path, $documento->nome_original);
    }

    public function visualizar(LoteDocumento $documento): Response
    {
        if (! Storage::disk('public')->exists($documento->path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        $conteudo = Storage::disk('public')->get($documento->path);
        $mime     = $documento->mimeTypeResolved();

        return response($conteudo, 200)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', 'inline; filename="' . $documento->nome_original . '"');
    }

    public function destroy(LoteDocumento $documento): RedirectResponse
    {
        Storage::disk('public')->delete($documento->path);
        $documento->delete();

        return back()->with('success', 'Documento removido.');
    }
}
