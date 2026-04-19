<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PessoaBuscaController extends Controller
{
    public function busca(Request $request): JsonResponse
    {
        $q = $request->get('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $pessoas = Pessoa::where(function ($query) use ($q) {
            $query->where('nome', 'like', "%{$q}%")
                  ->orWhere('cpf_cnpj', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%");
        })
        ->limit(10)
        ->get(['id', 'nome', 'tipo', 'cpf_cnpj', 'celular'])
        ->map(fn ($p) => [
            'id'        => $p->id,
            'nome'      => $p->nome,
            'tipo_label'=> $p->tipoLabel(),
            'cpf_cnpj'  => $p->cpf_cnpj,
            'celular'   => $p->celular,
        ]);

        return response()->json($pessoas);
    }
}
