<?php

namespace App\Http\Controllers;

use App\Models\Imovel;
use App\Models\Lote;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ImovelController extends Controller
{
    public function show(Imovel $imovel): View
    {
        $imovel->load(['lote.quadra.condominio', 'lote.pessoas', 'midias', 'documentos', 'historicos', 'pessoas']);
        return view('imoveis.show', compact('imovel'));
    }

    public function create(Lote $lote): View
    {
        if ($lote->imovel) {
            return redirect()->route('imoveis.show', $lote->imovel);
        }
        $lote->load('quadra.condominio');
        return view('imoveis.create', compact('lote'));
    }

    public function store(Request $request, Lote $lote): RedirectResponse
    {
        if ($lote->imovel) {
            return redirect()->route('imoveis.show', $lote->imovel);
        }

        $data = $this->validated($request);
        $data['lote_id'] = $lote->id;

        $imovel = Imovel::create($data);

        return redirect()->route('imoveis.show', $imovel)
            ->with('success', 'Imóvel cadastrado com sucesso.');
    }

    public function edit(Imovel $imovel): View
    {
        $imovel->load('lote.quadra.condominio');
        return view('imoveis.edit', compact('imovel'));
    }

    public function update(Request $request, Imovel $imovel): RedirectResponse
    {
        $imovel->update($this->validated($request));

        return redirect()->route('imoveis.show', $imovel)
            ->with('success', 'Imóvel atualizado com sucesso.');
    }

    public function destroy(Imovel $imovel): RedirectResponse
    {
        $lote = $imovel->lote;
        $imovel->delete();

        return redirect()->route('lotes.show', $lote)
            ->with('success', 'Imóvel removido.');
    }

    private function validated(Request $request): array
    {
        $request->validate([
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ], [
            'latitude.between'  => 'A latitude deve estar entre -90 e 90.',
            'longitude.between' => 'A longitude deve estar entre -180 e 180.',
        ]);

        return $request->only([
            'tipo', 'nome', 'descricao',
            'area_total', 'area_construida', 'area_privativa',
            'quartos', 'suites', 'banheiros', 'vagas_garagem', 'andares',
            'ano_construcao', 'padrao_acabamento', 'condominio_fechado', 'caracteristicas',
            'matricula_imovel', 'inscricao_municipal', 'cartorio',
            'numero_escritura', 'livro_escritura', 'folha_escritura',
            'logradouro', 'numero_endereco', 'complemento', 'bairro',
            'cidade', 'estado', 'cep', 'latitude', 'longitude',
            'valor_venal', 'valor_mercado', 'valor_iptu_anual', 'data_ultima_avaliacao',
            'situacao_ocupacao', 'observacoes',
        ]);
    }
}
