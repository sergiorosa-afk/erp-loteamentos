<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SiteApiService
{
    public function buscarDoSite(string $referenceCode): array
    {
        $url = config('services.site.api_url');
        $key = config('services.site.api_key');

        if (! $url || ! $key) {
            return [
                'ok'      => false,
                'dados'   => null,
                'erro'    => 'API não configurada — defina SITE_API_URL e SITE_API_KEY no .env',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $key,
                'Accept'        => 'application/json',
            ])->timeout(30)->get(rtrim($url, '/') . '/imoveis', ['ref' => $referenceCode]);

            return [
                'ok'    => $response->successful(),
                'dados' => $response->successful() ? $response->json() : null,
                'erro'  => $response->successful() ? null : $response->body(),
            ];
        } catch (\Throwable $e) {
            return [
                'ok'    => false,
                'dados' => null,
                'erro'  => $e->getMessage(),
            ];
        }
    }

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
