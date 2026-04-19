<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PessoaController extends Controller
{
    public function index(Request $request): View
    {
        $query = Pessoa::query()->with('endereco');

        // Filtro por tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Filtro por origem
        if ($request->filled('origem')) {
            $query->where('origem', $request->origem);
        }

        // Busca por nome ou cpf_cnpj
        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                  ->orWhere('cpf_cnpj', 'like', "%{$busca}%")
                  ->orWhere('email', 'like', "%{$busca}%")
                  ->orWhere('celular', 'like', "%{$busca}%");
            });
        }

        $pessoas = $query->orderBy('nome')->paginate(20)->withQueryString();

        $totais = [
            'lead'     => Pessoa::where('tipo', 'lead')->count(),
            'prospect' => Pessoa::where('tipo', 'prospect')->count(),
            'cliente'  => Pessoa::where('tipo', 'cliente')->count(),
        ];

        return view('pessoas.index', compact('pessoas', 'totais'));
    }

    public function create(): View
    {
        return view('pessoas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data    = $this->validarPessoa($request);
        $endData = $this->validarEndereco($request);

        $pessoa = Pessoa::create($data);
        $pessoa->endereco()->create($endData);

        return redirect()->route('pessoas.show', $pessoa)
            ->with('success', 'Pessoa cadastrada com sucesso!');
    }

    public function show(Pessoa $pessoa): View
    {
        $pessoa->load([
            'endereco',
            'certidoes',
            'imoveis.lote.quadra.condominio',
            'imoveis.historicos',
            'lotes.imovel.lote.quadra.condominio',
            'lotes.imovel.historicos',
            'imovelHistoricos.imovel.lote.quadra.condominio',
            'imovelHistoricos.imovel.historicos',
        ]);
        return view('pessoas.show', compact('pessoa'));
    }

    public function edit(Pessoa $pessoa): View
    {
        $pessoa->load('endereco');
        return view('pessoas.edit', compact('pessoa'));
    }

    public function update(Request $request, Pessoa $pessoa): RedirectResponse
    {
        $data    = $this->validarPessoa($request, $pessoa->id);
        $endData = $this->validarEndereco($request);

        $pessoa->update($data);

        if ($pessoa->endereco) {
            $pessoa->endereco->update($endData);
        } else {
            $pessoa->endereco()->create($endData);
        }

        return redirect()->route('pessoas.show', $pessoa)
            ->with('success', 'Dados atualizados com sucesso!');
    }

    public function destroy(Pessoa $pessoa): RedirectResponse
    {
        $pessoa->delete();

        return redirect()->route('pessoas.index')
            ->with('success', 'Pessoa removida.');
    }

    // ── Helpers ──────────────────────────────────────────────────────

    private function validarPessoa(Request $request, ?int $ignorarId = null): array
    {
        $uniqueRule = $ignorarId
            ? "nullable|string|max:20|unique:pessoas,cpf_cnpj,{$ignorarId}"
            : 'nullable|string|max:20|unique:pessoas,cpf_cnpj';

        return $request->validate([
            'nome'             => 'required|string|max:255',
            'cpf_cnpj'        => $uniqueRule,
            'tipo'             => 'required|in:lead,prospect,cliente',
            'telefone'         => 'nullable|string|max:20',
            'celular'          => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:255',
            'data_nascimento'  => 'nullable|date',
            'estado_civil'     => 'nullable|in:solteiro,casado,divorciado,viuvo,uniao_estavel,separado',
            'profissao'        => 'nullable|string|max:255',
            'nacionalidade'    => 'nullable|string|max:100',
            'rg'               => 'nullable|string|max:30',
            'orgao_emissor_rg' => 'nullable|string|max:30',
            'origem'           => 'nullable|string|max:255',
            'obs'              => 'nullable|string',
        ]);
    }

    private function validarEndereco(Request $request): array
    {
        return $request->validate([
            'cep'         => 'nullable|string|max:9',
            'logradouro'  => 'nullable|string|max:255',
            'numero'      => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro'      => 'nullable|string|max:100',
            'cidade'      => 'nullable|string|max:100',
            'estado'      => 'nullable|string|max:2',
        ]);
    }
}
