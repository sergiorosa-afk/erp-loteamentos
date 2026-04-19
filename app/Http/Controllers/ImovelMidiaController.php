<?php

namespace App\Http\Controllers;

use App\Models\Imovel;
use App\Models\ImovelMidia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImovelMidiaController extends Controller
{
    public function store(Request $request, Imovel $imovel): RedirectResponse
    {
        $request->validate([
            'arquivos'   => 'required|array|min:1',
            'arquivos.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm,pdf|max:102400',
        ]);

        $ordem = $imovel->midias()->max('ordem') ?? 0;

        foreach ($request->file('arquivos') as $file) {
            $mime = $file->getMimeType();
            $tipo = match (true) {
                str_starts_with($mime, 'image/') => 'imagem',
                str_starts_with($mime, 'video/') => 'video',
                $mime === 'application/pdf'       => 'pdf',
                default                           => 'imagem',
            };

            $ordem++;
            $path = $file->store("imoveis/{$imovel->id}/midias", 'public');

            $imovel->midias()->create([
                'tipo'          => $tipo,
                'titulo'        => $request->input("titulos.{$file->getClientOriginalName()}"),
                'path'          => $path,
                'nome_original' => $file->getClientOriginalName(),
                'tamanho_bytes' => $file->getSize(),
                'mime_type'     => $mime,
                'ordem'         => $ordem,
                'capa'          => $imovel->midias()->where('tipo', 'imagem')->count() === 0 && $tipo === 'imagem',
                'uploaded_by'   => auth()->id(),
            ]);
        }

        return back()->with('success', 'Mídia(s) enviada(s) com sucesso.');
    }

    public function setCapa(ImovelMidia $midia): RedirectResponse
    {
        $imovel = $midia->imovel;
        $imovel->midias()->where('tipo', 'imagem')->update(['capa' => false]);
        $midia->update(['capa' => true]);

        return back()->with('success', 'Foto de capa definida.');
    }

    public function updateTitulo(Request $request, ImovelMidia $midia): RedirectResponse
    {
        $request->validate(['titulo' => 'nullable|string|max:255']);
        $midia->update(['titulo' => $request->titulo]);
        return back()->with('success', 'Título atualizado.');
    }

    public function reorder(Request $request, Imovel $imovel): \Illuminate\Http\JsonResponse
    {
        $request->validate(['ordem' => 'required|array', 'ordem.*' => 'integer']);
        foreach ($request->ordem as $pos => $id) {
            $imovel->midias()->where('id', $id)->update(['ordem' => $pos + 1]);
        }
        return response()->json(['ok' => true]);
    }

    public function destroy(ImovelMidia $midia): RedirectResponse
    {
        Storage::disk('public')->delete($midia->path);
        $imovel = $midia->imovel;
        $midia->delete();

        // Se a foto deletada era capa, define a primeira imagem restante como capa
        if ($midia->capa) {
            $imovel->midias()->where('tipo', 'imagem')->orderBy('ordem')->first()?->update(['capa' => true]);
        }

        return back()->with('success', 'Mídia removida.');
    }
}
