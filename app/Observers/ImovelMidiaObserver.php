<?php

namespace App\Observers;

use App\Jobs\SyncImovelParaSite;
use App\Models\ImovelMidia;

class ImovelMidiaObserver
{
    public function saved(ImovelMidia $midia): void
    {
        SyncImovelParaSite::dispatch($midia->imovel_id, 'saved');
    }

    public function deleted(ImovelMidia $midia): void
    {
        SyncImovelParaSite::dispatch($midia->imovel_id, 'saved');
    }
}
