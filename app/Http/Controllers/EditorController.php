<?php

namespace App\Http\Controllers;

use App\Models\Condominio;
use App\Models\Lote;
use App\Models\Quadra;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EditorController extends Controller
{
    public function show(Condominio $condominio): View|RedirectResponse
    {
        if (! $condominio->planta_path) {
            return redirect()->route('condominios.show', $condominio)
                ->with('error', 'Faça o upload da planta antes de abrir o editor.');
        }

        $condominio->load([
            'quadras' => fn ($q) => $q->orderBy('codigo'),
            'quadras.lotes' => fn ($q) => $q->orderBy('numero'),
        ]);

        $editorData = $condominio->quadras->map(function ($q) {
            return [
                'id'       => $q->id,
                'codigo'   => $q->codigo,
                'poligono' => $q->poligono,
                'lotes'    => $q->lotes->map(function ($l) {
                    return [
                        'id'       => $l->id,
                        'numero'   => $l->numero,
                        'situacao' => $l->situacao,
                        'poligono' => $l->poligono,
                    ];
                })->values(),
            ];
        })->values();

        return view('editor.index', compact('condominio', 'editorData'));
    }

    public function salvar(Request $request, Condominio $condominio): JsonResponse
    {
        $request->validate([
            'quadras'            => 'nullable|array',
            'quadras.*.id'       => 'required|integer',
            'quadras.*.poligono' => 'nullable|array',
            'lotes'              => 'nullable|array',
            'lotes.*.id'         => 'required|integer',
            'lotes.*.poligono'   => 'nullable|array',
        ]);

        foreach ($request->input('quadras', []) as $data) {
            Quadra::where('id', $data['id'])
                ->where('condominio_id', $condominio->id)
                ->update(['poligono' => $data['poligono'] ?? null]);
        }

        foreach ($request->input('lotes', []) as $data) {
            $lote = Lote::with('quadra')->find($data['id']);
            if ($lote && $lote->quadra->condominio_id === $condominio->id) {
                $lote->update(['poligono' => $data['poligono'] ?? null]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Mapeamento salvo com sucesso!']);
    }

    public function criarQuadra(Request $request, Condominio $condominio): JsonResponse
    {
        $validated = $request->validate([
            'codigo'   => 'required|string|max:255|unique:quadras,codigo,NULL,id,condominio_id,' . $condominio->id,
            'poligono' => 'nullable|array',
        ]);

        $quadra = $condominio->quadras()->create($validated);

        return response()->json([
            'id'       => $quadra->id,
            'codigo'   => $quadra->codigo,
            'poligono' => $quadra->poligono,
            'lotes'    => [],
        ], 201);
    }

    public function criarLote(Request $request, Condominio $condominio): JsonResponse
    {
        $validated = $request->validate([
            'quadra_id' => 'required|exists:quadras,id',
            'numero'    => 'required|string|max:255',
            'situacao'  => 'nullable|in:disponivel,reservado,vendido,permutado',
            'poligono'  => 'nullable|array',
        ]);

        $quadra = $condominio->quadras()->findOrFail($validated['quadra_id']);

        $lote = $quadra->lotes()->create([
            'numero'   => $validated['numero'],
            'situacao' => $validated['situacao'] ?? 'disponivel',
            'poligono' => $validated['poligono'] ?? null,
        ]);

        return response()->json([
            'id'       => $lote->id,
            'numero'   => $lote->numero,
            'situacao' => $lote->situacao,
            'poligono' => $lote->poligono,
        ], 201);
    }
}
