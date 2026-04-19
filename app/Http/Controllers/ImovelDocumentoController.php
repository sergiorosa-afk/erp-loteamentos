<?php

namespace App\Http\Controllers;

use App\Models\Imovel;
use App\Models\ImovelDocumento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ImovelDocumentoController extends Controller
{
    public function store(Request $request, Imovel $imovel): RedirectResponse
    {
        $request->validate([
            'arquivo'          => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:20480',
            'tipo'             => 'nullable|in:matricula,escritura,iptu,certidao_negativa,habite_se,planta,condominio,procuracao,contrato,outro',
            'titulo'           => 'nullable|string|max:255',
            'numero_documento' => 'nullable|string|max:255',
            'orgao_emissor'    => 'nullable|string|max:255',
            'data_emissao'     => 'nullable|date',
            'data_vencimento'  => 'nullable|date',
        ]);

        $file = $request->file('arquivo');
        $path = $file->store("imoveis/{$imovel->id}/documentos", 'public');

        $imovel->documentos()->create([
            'tipo'             => $request->tipo ?? 'outro',
            'titulo'           => $request->titulo,
            'path'             => $path,
            'nome_original'    => $file->getClientOriginalName(),
            'tamanho_bytes'    => $file->getSize(),
            'mime_type'        => $file->getMimeType(),
            'numero_documento' => $request->numero_documento,
            'orgao_emissor'    => $request->orgao_emissor,
            'data_emissao'     => $request->data_emissao,
            'data_vencimento'  => $request->data_vencimento,
            'uploaded_by'      => auth()->id(),
        ]);

        return back()->with('success', 'Documento enviado com sucesso!');
    }

    public function download(ImovelDocumento $documento)
    {
        if (! Storage::disk('public')->exists($documento->path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return Storage::disk('public')->download($documento->path, $documento->nome_original);
    }

    public function visualizar(ImovelDocumento $documento): Response
    {
        if (! Storage::disk('public')->exists($documento->path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        $conteudo = Storage::disk('public')->get($documento->path);
        $mime     = $documento->mime_type ?? 'application/octet-stream';

        return response($conteudo, 200)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', 'inline; filename="' . $documento->nome_original . '"');
    }

    public function destroy(ImovelDocumento $documento): RedirectResponse
    {
        Storage::disk('public')->delete($documento->path);
        $documento->delete();

        return back()->with('success', 'Documento removido.');
    }
}
