<?php

namespace App\Observers;

use App\Jobs\SyncImovelParaSite;
use App\Models\Imovel;
use App\Models\ImovelHistorico;

class ImovelObserver
{
    public function saved(Imovel $imovel): void
    {
        // Auto-registra avaliação sempre que valor_mercado for alterado
        if ($imovel->wasChanged('valor_mercado') && $imovel->valor_mercado) {
            ImovelHistorico::create([
                'imovel_id'   => $imovel->id,
                'tipo'        => 'avaliacao',
                'data'        => now()->toDateString(),
                'valor'       => $imovel->valor_mercado,
                'descricao'   => 'Atualização automática de valor de mercado.',
                'registrado_por' => auth()->id(),
            ]);
        }

        SyncImovelParaSite::dispatch($imovel->id, 'saved');
    }

    public function deleted(Imovel $imovel): void
    {
        SyncImovelParaSite::dispatch($imovel->id, 'deleted');
    }
}
