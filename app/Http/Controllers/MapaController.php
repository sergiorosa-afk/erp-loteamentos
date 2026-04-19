<?php

namespace App\Http\Controllers;

use App\Models\Condominio;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MapaController extends Controller
{
    public function show(Condominio $condominio): View|RedirectResponse
    {
        if (! $condominio->planta_path) {
            return redirect()->route('condominios.show', $condominio)
                ->with('error', 'Este condomínio não possui planta cadastrada.');
        }

        $condominio->load([
            'quadras'              => fn ($q) => $q->orderBy('codigo'),
            'quadras.lotes'        => fn ($q) => $q->orderBy('numero'),
            'quadras.lotes.imovel' => fn ($q) => $q->select('id', 'lote_id', 'tipo', 'valor_mercado'),
        ]);

        $stats = [
            'total'       => 0,
            'disponivel'  => 0,
            'reservado'   => 0,
            'vendido'     => 0,
            'permutado'   => 0,
            'sem_mapa'    => 0,
            'com_imovel'  => 0,
        ];

        foreach ($condominio->quadras as $quadra) {
            foreach ($quadra->lotes as $lote) {
                $stats['total']++;
                $stats[$lote->situacao]++;
                if (! $lote->poligono || count($lote->poligono) < 3) $stats['sem_mapa']++;
                if ($lote->imovel) $stats['com_imovel']++;
            }
        }

        $isAdmin  = auth()->user()->isAdmin();

        $mapaData = $condominio->quadras->map(function ($q) use ($isAdmin) {
            return [
                'id'       => $q->id,
                'codigo'   => $q->codigo,
                'poligono' => $q->poligono,
                'lotes'    => $q->lotes->map(function ($l) use ($isAdmin) {
                    return [
                        'id'               => $l->id,
                        'numero'           => $l->numero,
                        'situacao'         => $l->situacao,
                        'unificado'        => $l->unificado,
                        'poligono'         => $l->poligono,
                        'area'             => $l->area,
                        'valor_tabela'     => $l->valor_tabela,
                        'codigo_interno'   => $l->codigo_interno,
                        'tem_imovel'       => (bool) $l->imovel,
                        'imovel_id'        => $l->imovel?->id,
                        'imovel_tipo'      => $l->imovel?->tipoLabel(),
                        'imovel_valor'     => $l->imovel ? (float) $l->imovel->valor_mercado : null,
                        'proprietario_nome' => $l->proprietario_nome,
                        'lote_url'         => route('lotes.show', $l),
                        'imovel_url'       => $l->imovel ? route('imoveis.show', $l->imovel) : null,
                        'imovel_create_url'=> $isAdmin ? route('imoveis.create', $l) : null,
                    ];
                })->values(),
            ];
        })->values();

        return view('mapa.index', compact('condominio', 'stats', 'mapaData', 'isAdmin'));
    }
}
