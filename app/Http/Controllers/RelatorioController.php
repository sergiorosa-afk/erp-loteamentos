<?php

namespace App\Http\Controllers;

use App\Models\Condominio;
use Illuminate\View\View;

class RelatorioController extends Controller
{
    public function index(): View
    {
        $condominios = Condominio::orderBy('nome')->get();
        return view('relatorios.index', compact('condominios'));
    }

    public function condominio(Condominio $condominio): View
    {
        $condominio->load([
            'quadras'       => fn ($q) => $q->orderBy('codigo'),
            'quadras.lotes' => fn ($q) => $q->orderBy('numero'),
        ]);

        $situacoes  = ['disponivel', 'reservado', 'vendido', 'permutado'];
        $stats      = array_fill_keys($situacoes, ['count' => 0, 'area' => 0, 'valor' => 0]);
        $stats['total'] = ['count' => 0, 'area' => 0, 'valor' => 0];

        foreach ($condominio->quadras as $quadra) {
            foreach ($quadra->lotes as $lote) {
                $s = $lote->situacao;
                $stats[$s]['count']++;
                $stats[$s]['area']  += (float) $lote->area;
                $stats[$s]['valor'] += (float) $lote->valor_tabela;
                $stats['total']['count']++;
                $stats['total']['area']  += (float) $lote->area;
                $stats['total']['valor'] += (float) $lote->valor_tabela;
            }
        }

        return view('relatorios.condominio', compact('condominio', 'stats', 'situacoes'));
    }
}
