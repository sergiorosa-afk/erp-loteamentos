<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use App\Models\PessoaCertidao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class PessoaCertidaoController extends Controller
{
    public function store(Request $request, Pessoa $pessoa): RedirectResponse
    {
        $request->validate([
            'arquivo'          => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:20480',
            'tipo'             => 'nullable|in:rg,cpf,cnh,comprovante_residencia,certidao_nascimento,certidao_casamento,certidao_obito,procuracao,outro',
            'titulo'           => 'nullable|string|max:255',
            'numero_documento' => 'nullable|string|max:100',
            'orgao_emissor'    => 'nullable|string|max:100',
            'data_emissao'     => 'nullable|date',
            'data_vencimento'  => 'nullable|date',
        ]);

        $file = $request->file('arquivo');
        $path = $file->store("pessoas/{$pessoa->id}/certidoes", 'public');

        $pessoa->certidoes()->create([
            'tipo'             => $request->tipo ?? 'outro',
            'titulo'           => $request->titulo,
            'numero_documento' => $request->numero_documento,
            'orgao_emissor'    => $request->orgao_emissor,
            'data_emissao'     => $request->data_emissao,
            'data_vencimento'  => $request->data_vencimento,
            'arquivo_path'     => $path,
            'nome_original'    => $file->getClientOriginalName(),
            'tamanho_bytes'    => $file->getSize(),
            'mime_type'        => $file->getMimeType(),
            'uploaded_by'      => auth()->id(),
        ]);

        return back()->with('success', 'Certidão enviada com sucesso!');
    }

    public function download(PessoaCertidao $certidao)
    {
        if (! Storage::disk('public')->exists($certidao->arquivo_path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return Storage::disk('public')->download($certidao->arquivo_path, $certidao->nome_original);
    }

    public function visualizar(PessoaCertidao $certidao): Response
    {
        if (! Storage::disk('public')->exists($certidao->arquivo_path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        $conteudo  = Storage::disk('public')->get($certidao->arquivo_path);
        $mimeType  = $certidao->mime_type ?? 'application/octet-stream';

        return response($conteudo, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $certidao->nome_original . '"');
    }

    public function destroy(PessoaCertidao $certidao): RedirectResponse
    {
        Storage::disk('public')->delete($certidao->arquivo_path);
        $certidao->delete();

        return back()->with('success', 'Certidão removida.');
    }
}
