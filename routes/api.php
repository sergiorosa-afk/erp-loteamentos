<?php

use App\Http\Controllers\Api\WebhookLeadController;
use Illuminate\Support\Facades\Route;

Route::middleware('webhook.token')
    ->post('/webhook/lead', [WebhookLeadController::class, 'store'])
    ->name('webhook.lead');
