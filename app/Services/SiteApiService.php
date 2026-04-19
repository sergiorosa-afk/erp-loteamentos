<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SiteApiService
{
    public function syncImovel(array $payload): array
    {
        $url = config('services.site.api_url');
        $key = config('services.site.api_key');

        if (! $url || ! $key) {
            return [
                'ok'       => false,
                'resposta' => 'API não configurada — defina SITE_API_URL e SITE_API_KEY no .env',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $key,
                'Accept'        => 'application/json',
            ])->timeout(30)->post(rtrim($url, '/') . '/imoveis/sync', $payload);

            return [
                'ok'       => $response->successful(),
                'resposta' => $response->body(),
            ];
        } catch (\Throwable $e) {
            return [
                'ok'       => false,
                'resposta' => $e->getMessage(),
            ];
        }
    }
}
