<?php

namespace App\Http\Controllers;

use App\Models\ImovelPessoaHistorico;
use App\Models\Lote;
use App\Models\Pessoa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LoteVinculoController extends Controller
{
    public function store(Request $request, Lote $lote): RedirectResponse
    {
        $data = $request->validate([
            'pessoa_id'    => 'required|exists:pessoas,id',
            'papel'        => 'required|in:proprietario,comprador,interessado',
            'data_vinculo' => 'nullable|date',
            'obs'          => 'nullable|string|max:500',
        ]);

        $lote->pessoas()->syncWithoutDetaching([
            $data['pessoa_id'] => [
                'papel'        => $data['papel'],
                'data_vinculo' => $data['data_vinculo'] ?? null,
                'obs'          => $data['obs'] ?? null,
            ]
        ]);

        // Registra histórico se o lote tiver um imóvel associado
        $lote->load('imovel');
        if ($lote->imovel) {
            ImovelPessoaHistorico::create([
                'imovel_id'      => $lote->imovel->id,
                'pessoa_id'      => $data['pessoa_id'],
                'papel'          => $data['papel'],
                'acao'           => 'vinculado',
                'data_vinculo'   => $data['data_vinculo'] ?? null,
                'valor_imovel'   => $lote->imovel->valor_mercado,
                'obs'            => $data['obs'] ?? null,
                'registrado_por' => auth()->id(),
            ]);
        }

        if ($data['papel'] === 'proprietario') {
            $pessoa = Pessoa::find($data['pessoa_id']);
            $lote->update(['proprietario_nome' => $pessoa->nome]);
        }

        return back()->with('success', 'Pessoa vinculada ao lote com sucesso!');
    }

    public function destroy(Lote $lote, Pessoa $pessoa): RedirectResponse
    {
        // Captura dados do vínculo ANTES de remover
        $vinculo = $lote->pessoas()
            ->wherePivot('pessoa_id', $pessoa->id)
            ->first();

        $papel       = $vinculo?->pivot?->papel ?? 'desconhecido';
        $dataVinculo = $vinculo?->pivot?->data_vinculo;
        $eraProp     = $papel === 'proprietario';

        $lote->pessoas()->detach($pessoa->id);

        // Registra histórico se o lote tiver um imóvel associado
        $lote->load('imovel');
        if ($lote->imovel) {
            ImovelPessoaHistorico::create([
                'imovel_id'      => $lote->imovel->id,
                'pessoa_id'      => $pessoa->id,
                'papel'          => $papel,
                'acao'           => 'desvinculado',
                'data_vinculo'   => $dataVinculo,
                'valor_imovel'   => $lote->imovel->valor_mercado,
                'registrado_por' => auth()->id(),
            ]);
        }

        if ($eraProp) {
            $outroProp = $lote->pessoas()->wherePivot('papel', 'proprietario')->first();
            $lote->update(['proprietario_nome' => $outroProp?->nome]);
        }

        return back()->with('success', 'Vínculo removido.');
    }
}
