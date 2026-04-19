<?php

namespace App\Http\Controllers;

use App\Models\Condominio;
use App\Models\Lote;
use App\Models\Quadra;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoteController extends Controller
{
    public function show(Lote $lote): View
    {
        $lote->load(['quadra.condominio', 'documentos.uploader', 'imovel.midias']);
        return view('lotes.show', compact('lote'));
    }

    public function create(Quadra $quadra): View
    {
        return view('lotes.create', compact('quadra'));
    }

    public function store(Request $request, Quadra $quadra): RedirectResponse
    {
        $validated = $request->validate([
            'numero'        => 'required|string|max:255|unique:lotes,numero,NULL,id,quadra_id,' . $quadra->id,
            'codigo_interno' => 'nullable|string|max:255',
            'area'          => 'nullable|numeric|min:0',
            'testada'       => 'nullable|numeric|min:0',
            'comprimento'   => 'nullable|numeric|min:0',
            'situacao'      => 'nullable|in:disponivel,reservado,vendido,permutado',
            'restricoes'    => 'nullable|string',
            'valor_tabela'  => 'nullable|numeric|min:0',
        ]);

        $quadra->lotes()->create($validated);

        return redirect()->route('quadras.show', $quadra)
            ->with('success', 'Lote cadastrado com sucesso!');
    }

    public function edit(Lote $lote): View
    {
        return view('lotes.edit', compact('lote'));
    }

    public function update(Request $request, Lote $lote): RedirectResponse
    {
        $validated = $request->validate([
            'numero'        => 'required|string|max:255|unique:lotes,numero,' . $lote->id . ',id,quadra_id,' . $lote->quadra_id,
            'codigo_interno' => 'nullable|string|max:255',
            'area'          => 'nullable|numeric|min:0',
            'testada'       => 'nullable|numeric|min:0',
            'comprimento'   => 'nullable|numeric|min:0',
            'situacao'      => 'nullable|in:disponivel,reservado,vendido,permutado',
            'restricoes'    => 'nullable|string',
            'valor_tabela'  => 'nullable|numeric|min:0',
        ]);

        $lote->update($validated);

        return redirect()->route('quadras.show', $lote->quadra)
            ->with('success', 'Lote atualizado!');
    }

    public function destroy(Lote $lote): RedirectResponse
    {
        $quadra = $lote->quadra;
        $lote->delete();
        return redirect()->route('quadras.show', $quadra)->with('success', 'Lote removido!');
    }

    public function updateSituacao(Request $request, Lote $lote): RedirectResponse
    {
        $request->validate(['situacao' => 'required|in:disponivel,reservado,vendido,permutado']);
        $lote->update(['situacao' => $request->situacao]);
        return back()->with('success', 'Situação atualizada!');
    }

    // ─── Unificação ───────────────────────────────────────────
    public function unificar(Request $request, Quadra $quadra): RedirectResponse
    {
        $validated = $request->validate([
            'lote_ids'     => 'required|array|min:2',
            'lote_ids.*'   => 'required|integer|exists:lotes,id',
            'numero'       => 'required|string|max:255',
            'area'         => 'nullable|numeric|min:0',
            'valor_tabela' => 'nullable|numeric|min:0',
            'situacao'     => 'nullable|in:disponivel,reservado,vendido,permutado',
        ]);

        $lotes = $quadra->lotes()
            ->whereIn('id', $validated['lote_ids'])
            ->whereNull('parent_lote_id')
            ->get();

        if ($lotes->count() < 2) {
            return back()->with('error', 'Selecione ao menos 2 lotes elegíveis para unificar.');
        }

        // Convex hull of all polygon points
        $allPoints = [];
        foreach ($lotes as $lote) {
            if ($lote->poligono) {
                foreach ($lote->poligono as $pt) {
                    $allPoints[] = $pt;
                }
            }
        }
        $polygon = count($allPoints) >= 3 ? $this->convexHull($allPoints) : null;

        $unified = $quadra->lotes()->create([
            'numero'          => $validated['numero'],
            'area'            => $validated['area'] ?? null,
            'valor_tabela'    => $validated['valor_tabela'] ?? null,
            'situacao'        => $validated['situacao'] ?? 'disponivel',
            'unificado'       => true,
            'lotes_originais' => $lotes->pluck('id')->toArray(),
            'poligono'        => $polygon,
        ]);

        // Mark originals and soft-delete them
        $lotes->each(function (Lote $lote) use ($unified) {
            $lote->update(['parent_lote_id' => $unified->id]);
            $lote->delete();
        });

        return redirect()->route('quadras.show', $quadra)
            ->with('success', "Lotes unificados em Lote {$unified->numero}!");
    }

    public function desunificar(Lote $lote): RedirectResponse
    {
        if (! $lote->unificado) {
            return back()->with('error', 'Este lote não é um lote unificado.');
        }

        $quadra = $lote->quadra;

        // Restore originals
        if ($lote->lotes_originais) {
            Lote::withTrashed()
                ->whereIn('id', $lote->lotes_originais)
                ->restore();

            Lote::whereIn('id', $lote->lotes_originais)
                ->update(['parent_lote_id' => null]);
        }

        $lote->forceDelete();

        return redirect()->route('quadras.show', $quadra)
            ->with('success', 'Unificação desfeita. Lotes originais restaurados.');
    }

    // ─── Convex hull (Andrew's monotone chain) ────────────────
    private function convexHull(array $points): array
    {
        $n = count($points);
        if ($n < 3) return $points;

        usort($points, fn ($a, $b) => $a[0] <=> $b[0] ?: $a[1] <=> $b[1]);

        $lower = [];
        foreach ($points as $p) {
            while (count($lower) >= 2 && $this->cross($lower[count($lower) - 2], $lower[count($lower) - 1], $p) <= 0) {
                array_pop($lower);
            }
            $lower[] = $p;
        }

        $upper = [];
        foreach (array_reverse($points) as $p) {
            while (count($upper) >= 2 && $this->cross($upper[count($upper) - 2], $upper[count($upper) - 1], $p) <= 0) {
                array_pop($upper);
            }
            $upper[] = $p;
        }

        array_pop($lower);
        array_pop($upper);

        return array_values(array_merge($lower, $upper));
    }

    private function cross(array $o, array $a, array $b): float
    {
        return ($a[0] - $o[0]) * ($b[1] - $o[1]) - ($a[1] - $o[1]) * ($b[0] - $o[0]);
    }
}
