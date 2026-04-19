<?php

namespace App\Http\Controllers;

use App\Models\Imovel;
use App\Models\ImovelPessoaHistorico;
use App\Models\Pessoa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ImovelVinculoController extends Controller
{
    public function store(Request $request, Imovel $imovel): RedirectResponse
    {
        $data = $request->validate([
            'pessoa_id'    => 'required|exists:pessoas,id',
            'papel'        => 'required|in:proprietario,locatario',
            'data_vinculo' => 'nullable|date',
            'obs'          => 'nullable|string|max:500',
        ]);

        $imovel->pessoas()->syncWithoutDetaching([
            $data['pessoa_id'] => [
                'papel'        => $data['papel'],
                'data_vinculo' => $data['data_vinculo'] ?? null,
                'obs'          => $data['obs'] ?? null,
            ]
        ]);

        // Grava histórico de vínculo
        ImovelPessoaHistorico::create([
            'imovel_id'      => $imovel->id,
            'pessoa_id'      => $data['pessoa_id'],
            'papel'          => $data['papel'],
            'acao'           => 'vinculado',
            'data_vinculo'   => $data['data_vinculo'] ?? null,
            'valor_imovel'   => $imovel->valor_mercado,
            'obs'            => $data['obs'] ?? null,
            'registrado_por' => auth()->id(),
        ]);

        if ($data['papel'] === 'proprietario') {
            $pessoa = Pessoa::find($data['pessoa_id']);
            $imovel->lote?->update(['proprietario_nome' => $pessoa->nome]);
        }

        return back()->with('success', 'Pessoa vinculada ao imóvel com sucesso!')->with('tab', 'pessoas');
    }

    public function destroy(Imovel $imovel, Pessoa $pessoa): RedirectResponse
    {
        // Captura dados do vínculo ANTES de remover
        $vinculo = $imovel->pessoas()
            ->wherePivot('pessoa_id', $pessoa->id)
            ->first();

        $papel       = $vinculo?->pivot?->papel ?? 'desconhecido';
        $dataVinculo = $vinculo?->pivot?->data_vinculo;
        $eraProp     = $papel === 'proprietario';

        $imovel->pessoas()->detach($pessoa->id);

        // Grava histórico de desvínculo
        ImovelPessoaHistorico::create([
            'imovel_id'      => $imovel->id,
            'pessoa_id'      => $pessoa->id,
            'papel'          => $papel,
            'acao'           => 'desvinculado',
            'data_vinculo'   => $dataVinculo,
            'valor_imovel'   => $imovel->valor_mercado,
            'registrado_por' => auth()->id(),
        ]);

        if ($eraProp) {
            $outroProp = $imovel->pessoas()->wherePivot('papel', 'proprietario')->first();
            $imovel->lote?->update(['proprietario_nome' => $outroProp?->nome]);
        }

        return back()->with('success', 'Vínculo removido.')->with('tab', 'pessoas');
    }
}
