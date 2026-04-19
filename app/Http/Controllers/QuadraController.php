<?php

namespace App\Http\Controllers;

use App\Models\Condominio;
use App\Models\Quadra;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QuadraController extends Controller
{
    public function index(Condominio $condominio): View
    {
        $quadras = $condominio->quadras()->withCount('lotes')->orderBy('codigo')->get();
        return view('quadras.index', compact('condominio', 'quadras'));
    }

    public function create(Condominio $condominio): View
    {
        return view('quadras.create', compact('condominio'));
    }

    public function store(Request $request, Condominio $condominio): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:255|unique:quadras,codigo,NULL,id,condominio_id,' . $condominio->id,
            'area_total' => 'nullable|numeric|min:0',
            'observacoes' => 'nullable|string',
        ]);

        $quadra = $condominio->quadras()->create($validated);

        return redirect()->route('condominios.quadras.show', [$condominio, $quadra])
            ->with('success', 'Quadra cadastrada com sucesso!');
    }

    public function show(Quadra $quadra): View
    {
        $quadra->load(['condominio', 'lotes.imovel']);
        $condominio = $quadra->condominio;
        return view('quadras.show', compact('condominio', 'quadra'));
    }

    public function edit(Condominio $condominio, Quadra $quadra): View
    {
        return view('quadras.edit', compact('condominio', 'quadra'));
    }

    public function update(Request $request, Condominio $condominio, Quadra $quadra): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:255|unique:quadras,codigo,' . $quadra->id . ',id,condominio_id,' . $condominio->id,
            'area_total' => 'nullable|numeric|min:0',
            'observacoes' => 'nullable|string',
        ]);

        $quadra->update($validated);

        return redirect()->route('condominios.quadras.show', [$condominio, $quadra])
            ->with('success', 'Quadra atualizada com sucesso!');
    }

    public function destroy(Condominio $condominio, Quadra $quadra): RedirectResponse
    {
        $quadra->delete();

        return redirect()->route('condominios.quadras.index', $condominio)
            ->with('success', 'Quadra removida com sucesso!');
    }
}
