<?php

namespace App\Http\Controllers;

use App\Models\Condominio;
use App\Models\Imovel;
use Illuminate\View\View;

class ApresentacaoController extends Controller
{
    /**
     * Grade de condomínios — tela de escolha para o apresentador.
     */
    public function index(): View
    {
        $condominios = Condominio::orderBy('nome')
            ->withCount(['quadras', 'quadras as lotes_count' => fn ($q) => $q->join('lotes', 'lotes.quadra_id', '=', 'quadras.id')])
            ->get();

        return view('apresentacao.index', compact('condominios'));
    }

    /**
     * Mapa interativo touch do condomínio escolhido.
     */
    public function mapa(Condominio $condominio): View
    {
        if (! $condominio->planta_path) {
            return redirect()->route('apresentacao.index')
                ->with('error', 'Este condomínio não possui planta cadastrada.');
        }

        $condominio->load([
            'quadras'              => fn ($q) => $q->orderBy('codigo'),
            'quadras.lotes'        => fn ($q) => $q->orderBy('numero'),
            'quadras.lotes.imovel' => fn ($q) => $q->select('id', 'lote_id', 'tipo', 'valor_mercado', 'area_construida', 'quartos'),
        ]);

        $mapaData = $condominio->quadras->map(function ($q) {
            return [
                'id'       => $q->id,
                'codigo'   => $q->codigo,
                'poligono' => $q->poligono,
                'lotes'    => $q->lotes->map(function ($l) {
                    return [
                        'id'                => $l->id,
                        'numero'            => $l->numero,
                        'situacao'          => $l->situacao,
                        'unificado'         => $l->unificado,
                        'poligono'          => $l->poligono,
                        'area'              => $l->area,
                        'proprietario_nome' => $l->proprietario_nome,
                        'tem_imovel'        => (bool) $l->imovel,
                        'imovel_id'         => $l->imovel?->id,
                        'imovel_tipo'       => $l->imovel?->tipoLabel(),
                        'imovel_area'       => $l->imovel?->area_construida,
                        'imovel_quartos'    => $l->imovel?->quartos,
                        'imovel_url'        => $l->imovel
                            ? route('apresentacao.imovel', $l->imovel)
                            : null,
                    ];
                })->values(),
            ];
        })->values();

        return view('apresentacao.mapa', compact('condominio', 'mapaData'));
    }

    /**
     * Ficha visual do imóvel — somente leitura.
     */
    public function imovel(Imovel $imovel): View
    {
        $imovel->load(['lote.quadra.condominio', 'midias', 'pessoas', 'historicos']);

        return view('apresentacao.imovel', compact('imovel'));
    }
}
