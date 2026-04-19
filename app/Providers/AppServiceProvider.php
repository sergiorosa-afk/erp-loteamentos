<?php

namespace App\Providers;

use App\Models\Imovel;
use App\Models\ImovelMidia;
use App\Observers\ImovelMidiaObserver;
use App\Observers\ImovelObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Imovel::observe(ImovelObserver::class);
        ImovelMidia::observe(ImovelMidiaObserver::class);
    }
}
