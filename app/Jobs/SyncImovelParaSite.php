<?php

namespace App\Jobs;

use App\Models\Imovel;
use App\Models\SiteSyncLog;
use App\Services\SiteApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class SyncImovelParaSite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public readonly int $imovelId,
        public readonly string $evento = 'saved',
        public readonly ?array $midiaIds = null,
    ) {}

    public function handle(SiteApiService $service): void
    {
        $imovel = Imovel::withTrashed()
            ->with(['lote.quadra.condominio', 'midias'])
            ->find($this->imovelId);

        if (! $imovel) {
            return;
        }

        $payload = $this->buildPayload($imovel);
        $result  = $service->syncImovel($payload);

        SiteSyncLog::create([
            'imovel_id' => $this->imovelId,
            'evento'    => $this->evento,
            'status'    => $result['ok'] ? 'sucesso' : 'erro',
            'payload'   => $payload,
            'resposta'  => $result['resposta'],
        ]);

        if ($result['ok']) {
            $dados = json_decode($result['resposta'], true);
            $siteId = $dados['property_id'] ?? null;
            $imovel->updateQuietly([
                'site_imovel_id'      => $siteId ?: $imovel->site_imovel_id,
                'site_sincronizado_em' => now(),
            ]);
        }
    }

    private function buildPayload(Imovel $imovel): array
    {
        $midias = $imovel->midias
            ->when($this->midiaIds !== null, fn ($col) => $col->whereIn('id', $this->midiaIds))
            ->map(fn ($m) => [
            'tipo'   => $m->tipo,
            'url'    => Storage::disk('public')->url($m->path),
            'capa'   => (bool) $m->capa,
            'titulo' => $m->titulo ?? $m->nome_original,
        ])->values()->all();

        return [
            'acao'   => $this->evento === 'deleted' ? 'deletar' : 'sincronizar',
            'id'     => $imovel->id,
            'titulo' => $imovel->nome ?? $imovel->tipoLabel(),
            'tipo'   => $imovel->tipo,
            'descricao' => $imovel->descricao,
            'status' => $imovel->lote?->situacao ?? 'disponivel',
            'localizacao' => [
                'condominio' => $imovel->lote?->quadra?->condominio?->nome,
                'quadra'     => $imovel->lote?->quadra?->codigo,
                'lote'       => $imovel->lote?->numero,
            ],
            'caracteristicas' => [
                'area_total'        => $imovel->area_total,
                'area_construida'   => $imovel->area_construida,
                'area_privativa'    => $imovel->area_privativa,
                'quartos'           => $imovel->quartos,
                'suites'            => $imovel->suites,
                'banheiros'         => $imovel->banheiros,
                'vagas_garagem'     => $imovel->vagas_garagem,
                'andares'           => $imovel->andares,
                'ano_construcao'    => $imovel->ano_construcao,
                'padrao_acabamento' => $imovel->padrao_acabamento,
                'condominio_fechado'=> $imovel->condominio_fechado,
            ],
            'valores' => [
                'valor_mercado' => $imovel->valor_mercado,
                'valor_venal'   => $imovel->valor_venal,
            ],
            'midias' => $midias,
        ];
    }
}
