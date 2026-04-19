<?php

namespace App\Http\Controllers;

use App\Models\Condominio;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CondominioController extends Controller
{
    public function index(): View
    {
        $condominios = Condominio::orderBy('nome')->paginate(15);
        return view('condominios.index', compact('condominios'));
    }

    public function create(): View
    {
        return view('condominios.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            ...$this->rules(),
            'planta' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,svg|max:20480',
        ]);

        if ($request->hasFile('planta')) {
            $file = $request->file('planta');
            $validated['planta_path'] = $file->store('plantas', 'public');
            $validated['planta_nome_original'] = $file->getClientOriginalName();
        }

        unset($validated['planta']);
        $condominio = Condominio::create($validated);

        return redirect()->route('condominios.show', $condominio)
            ->with('success', 'Condomínio cadastrado com sucesso!');
    }

    public function show(Condominio $condominio): View
    {
        $condominio->load('quadras');
        return view('condominios.show', compact('condominio'));
    }

    public function edit(Condominio $condominio): View
    {
        return view('condominios.edit', compact('condominio'));
    }

    public function update(Request $request, Condominio $condominio): RedirectResponse
    {
        $validated = $request->validate([
            ...$this->rules($condominio->id),
            'planta' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,svg|max:20480',
            'remover_planta' => 'nullable|boolean',
        ]);

        if ($request->boolean('remover_planta') && $condominio->planta_path) {
            Storage::disk('public')->delete($condominio->planta_path);
            $validated['planta_path'] = null;
            $validated['planta_nome_original'] = null;
        }

        if ($request->hasFile('planta')) {
            if ($condominio->planta_path) {
                Storage::disk('public')->delete($condominio->planta_path);
            }
            $file = $request->file('planta');
            $validated['planta_path'] = $file->store('plantas', 'public');
            $validated['planta_nome_original'] = $file->getClientOriginalName();
        }

        unset($validated['planta'], $validated['remover_planta']);
        $condominio->update($validated);

        return redirect()->route('condominios.show', $condominio)
            ->with('success', 'Condomínio atualizado com sucesso!');
    }

    public function destroy(Condominio $condominio): RedirectResponse
    {
        if ($condominio->planta_path) {
            Storage::disk('public')->delete($condominio->planta_path);
        }

        $condominio->delete();

        return redirect()->route('condominios.index')
            ->with('success', 'Condomínio removido com sucesso!');
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'nome' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:18|unique:condominios,cnpj' . ($ignoreId ? ",$ignoreId" : ''),
            'matricula_cartorio' => 'nullable|string|max:255',
            'numero_registro' => 'nullable|string|max:255',
            'logradouro' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:9',
            'municipio_registro' => 'nullable|string|max:255',
            'area_total' => 'nullable|numeric|min:0',
            'area_verde' => 'nullable|numeric|min:0',
            'area_vias' => 'nullable|numeric|min:0',
            'total_quadras' => 'nullable|integer|min:0',
            'total_lotes' => 'nullable|integer|min:0',
            'zoneamento' => 'nullable|in:residencial,comercial,misto',
            'data_aprovacao_prefeitura' => 'nullable|date',
            'data_registro_cartorio' => 'nullable|date',
            'incorporadora' => 'nullable|string|max:255',
            'sindico' => 'nullable|string|max:255',
            'administradora' => 'nullable|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ];
    }
}
