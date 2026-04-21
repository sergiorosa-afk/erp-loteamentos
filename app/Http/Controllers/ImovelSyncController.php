<?php

namespace App\Http\Controllers;

use App\Jobs\SyncImovelParaSite;
use App\Models\Imovel;
use App\Services\SiteApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImovelSyncController extends Controller
{
    public function preview(Imovel $imovel, SiteApiService $service): View
    {
        $imovel->load(['lote.quadra.condominio', 'midias']);

        $midiasSincronizaveis = $imovel->midias->whereIn('tipo', ['imagem', 'video'])->values();

        $dadosSite  = null;
        $erroSite   = null;
        $comparacao = null;

        if ($imovel->site_imovel_id) {
            $resultado = $service->buscarDoSite('erp_' . $imovel->id);
            if ($resultado['ok']) {
                $dadosSite  = $resultado['dados'];
                $comparacao = $this->buildComparacao($imovel, $dadosSite);
            } else {
                $erroSite = $resultado['erro'];
            }
        }

        return view('imoveis.sync_preview', compact(
            'imovel', 'midiasSincronizaveis', 'dadosSite', 'erroSite', 'comparacao'
        ));
    }

    public function forcar(Request $request, Imovel $imovel): RedirectResponse
    {
        $midiaIds = $request->has('midia_ids')
            ? array_map('intval', (array) $request->input('midia_ids'))
            : null;

        SyncImovelParaSite::dispatch($imovel->id, 'manual', $midiaIds);

        return redirect()->route('imoveis.show', $imovel)
            ->with('success', 'Sincronização com o site agendada. O resultado aparecerá em instantes na aba Sync.')
            ->with('tab', 'sync');
    }

    private function buildComparacao(Imovel $imovel, array $site): array
    {
        $situacaoMap = [
            'disponivel' => 'available',
            'reservado'  => 'reserved',
            'vendido'    => 'sold',
            'permutado'  => 'sold',
        ];

        $linhas = [
            ['label' => 'Título',           'erp' => $imovel->nome ?? $imovel->tipoLabel(),                              'site' => $site['title'] ?? null],
            ['label' => 'Tipo',             'erp' => $imovel->tipoLabel(),                                                'site' => $site['property_type'] ?? null],
            ['label' => 'Status',           'erp' => $situacaoMap[$imovel->lote?->situacao ?? 'disponivel'] ?? 'available', 'site' => $site['status'] ?? null],
            ['label' => 'Descrição',        'erp' => $imovel->descricao,                                                  'site' => $site['full_description'] ?? null],
            ['label' => 'Condomínio',       'erp' => $imovel->lote?->quadra?->condominio?->nome,                          'site' => $site['development_name'] ?? null],
            ['label' => 'Área total (m²)',  'erp' => $imovel->area_total,                                                 'site' => $site['land_area'] ?? null],
            ['label' => 'Área construída',  'erp' => $imovel->area_construida,                                            'site' => $site['built_area'] ?? null],
            ['label' => 'Quartos',          'erp' => $imovel->quartos,                                                    'site' => $site['bedrooms'] ?? null],
            ['label' => 'Suítes',           'erp' => $imovel->suites,                                                     'site' => $site['suites'] ?? null],
            ['label' => 'Banheiros',        'erp' => $imovel->banheiros,                                                  'site' => $site['bathrooms'] ?? null],
            ['label' => 'Vagas',            'erp' => $imovel->vagas_garagem,                                              'site' => $site['parking_spaces'] ?? null],
            ['label' => 'Andares',          'erp' => $imovel->andares,                                                    'site' => $site['floors'] ?? null],
            ['label' => 'Ano construção',   'erp' => $imovel->ano_construcao,                                             'site' => $site['year_built'] ?? null],
            ['label' => 'Valor de mercado', 'erp' => $imovel->valor_mercado,                                              'site' => $site['price'] ?? null],
            ['label' => 'Cidade',           'erp' => $imovel->cidade,                                                     'site' => $site['city'] ?? null],
            ['label' => 'Estado',           'erp' => $imovel->estado,                                                     'site' => $site['state'] ?? null],
            ['label' => 'CEP',              'erp' => $imovel->cep,                                                        'site' => $site['postal_code'] ?? null],
        ];

        return array_map(function ($linha) {
            $linha['diff'] = trim((string) ($linha['erp'] ?? '')) !== trim((string) ($linha['site'] ?? ''));
            return $linha;
        }, $linhas);
    }
}
