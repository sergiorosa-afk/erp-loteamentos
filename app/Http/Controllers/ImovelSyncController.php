<?php

namespace App\Http\Controllers;

use App\Jobs\SyncImovelParaSite;
use App\Models\Imovel;
use Illuminate\Http\RedirectResponse;

class ImovelSyncController extends Controller
{
    public function forcar(Imovel $imovel): RedirectResponse
    {
        SyncImovelParaSite::dispatch($imovel->id, 'manual');

        return back()->with('success', 'Sincronização com o site agendada. O resultado aparecerá em instantes na aba Sync.');
    }
}
