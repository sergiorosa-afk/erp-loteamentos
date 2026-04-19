<?php

namespace App\Http\Controllers;

use App\Models\Imovel;
use App\Models\ImovelHistorico;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ImovelHistoricoController extends Controller
{
    public function store(Request $request, Imovel $imovel): RedirectResponse
    {
        $request->validate([
            'tipo'                  => 'nullable|in:compra,venda,avaliacao,reforma,locacao,permuta,inventario,outro',
            'data'                  => 'nullable|date',
            'valor'                 => 'nullable|numeric|min:0',
            'proprietario_anterior' => 'nullable|string|max:255',
            'cpf_cnpj_anterior'     => 'nullable|string|max:20',
            'proprietario_atual'    => 'nullable|string|max:255',
            'cpf_cnpj_atual'        => 'nullable|string|max:20',
            'cartorio'              => 'nullable|string|max:255',
            'numero_escritura'      => 'nullable|string|max:255',
            'numero_registro'       => 'nullable|string|max:255',
            'corretor'              => 'nullable|string|max:255',
            'descricao'             => 'nullable|string',
            'observacoes'           => 'nullable|string',
        ]);

        $imovel->historicos()->create(
            array_merge($request->only([
                'tipo', 'data', 'valor',
                'proprietario_anterior', 'cpf_cnpj_anterior',
                'proprietario_atual', 'cpf_cnpj_atual',
                'cartorio', 'numero_escritura', 'numero_registro',
                'corretor', 'descricao', 'observacoes',
            ]), ['registrado_por' => auth()->id()])
        );

        return back()->with('success', 'Evento registrado no histórico.')->withFragment('historico');
    }

    public function update(Request $request, ImovelHistorico $historico): RedirectResponse
    {
        $request->validate([
            'tipo'                  => 'nullable|in:compra,venda,avaliacao,reforma,locacao,permuta,inventario,outro',
            'data'                  => 'nullable|date',
            'valor'                 => 'nullable|numeric|min:0',
            'proprietario_anterior' => 'nullable|string|max:255',
            'cpf_cnpj_anterior'     => 'nullable|string|max:20',
            'proprietario_atual'    => 'nullable|string|max:255',
            'cpf_cnpj_atual'        => 'nullable|string|max:20',
            'cartorio'              => 'nullable|string|max:255',
            'numero_escritura'      => 'nullable|string|max:255',
            'numero_registro'       => 'nullable|string|max:255',
            'corretor'              => 'nullable|string|max:255',
            'descricao'             => 'nullable|string',
            'observacoes'           => 'nullable|string',
        ]);

        $historico->update($request->only([
            'tipo', 'data', 'valor',
            'proprietario_anterior', 'cpf_cnpj_anterior',
            'proprietario_atual', 'cpf_cnpj_atual',
            'cartorio', 'numero_escritura', 'numero_registro',
            'corretor', 'descricao', 'observacoes',
        ]));

        return back()->with('success', 'Evento atualizado.')->withFragment('historico');
    }

    public function destroy(ImovelHistorico $historico): RedirectResponse
    {
        $historico->delete();
        return back()->with('success', 'Evento removido.')->withFragment('historico');
    }
}
