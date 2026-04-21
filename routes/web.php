<?php

use App\Http\Controllers\CondominioController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\EditorController;
use App\Http\Controllers\ImovelController;
use App\Http\Controllers\ImovelDocumentoController;
use App\Http\Controllers\ImovelHistoricoController;
use App\Http\Controllers\ImovelMidiaController;
use App\Http\Controllers\ImovelSyncController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\ApresentacaoController;
use App\Http\Controllers\ImovelVinculoController;
use App\Http\Controllers\LoteVinculoController;
use App\Http\Controllers\PessoaBuscaController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\PessoaCertidaoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuadraController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// ROTA TEMPORÁRIA DE SETUP — APAGAR APÓS USO
Route::get('/setup-init', function () {
    if (request('token') !== 'erp2026setup') {
        abort(403, 'Acesso negado.');
    }
    $output = [];
    Artisan::call('key:generate', ['--force' => true]);
    $output[] = '✓ key:generate — ' . Artisan::output();
    Artisan::call('migrate', ['--force' => true]);
    $output[] = '✓ migrate — ' . Artisan::output();
    Artisan::call('config:cache');
    $output[] = '✓ config:cache — ' . Artisan::output();
    Artisan::call('route:cache');
    $output[] = '✓ route:cache — ' . Artisan::output();
    Artisan::call('view:cache');
    $output[] = '✓ view:cache — ' . Artisan::output();
    return '<pre>=== SETUP CONCLUIDO ===\n' . implode("\n", $output) . '\n\nAPAGUE a rota /setup-init do web.php agora!</pre>';
});

Route::get('/', fn () => redirect()->route('dashboard'));

Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── READ-ONLY routes (todos os usuários autenticados) ──────────
    Route::get('/condominios', [CondominioController::class, 'index'])->name('condominios.index');
    Route::get('/quadras/{quadra}', [QuadraController::class, 'show'])->name('quadras.show');
    Route::get('/lotes/{lote}', [LoteController::class, 'show'])->name('lotes.show');
    Route::get('/imoveis/{imovel}', [ImovelController::class, 'show'])->name('imoveis.show');
    Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');

    // Documento download/visualizar (todos os usuários autenticados)
    Route::get('/documentos/{documento}/download', [DocumentoController::class, 'download'])->name('documentos.download');
    Route::get('/documentos/{documento}/visualizar', [DocumentoController::class, 'visualizar'])->name('documentos.visualizar');
    Route::get('/imoveis/documentos/{documento}/visualizar', [ImovelDocumentoController::class, 'visualizar'])->name('imoveis.documentos.visualizar');

    // Pessoas — leitura (todos autenticados)
    // IMPORTANT: rotas estáticas ANTES do wildcard {pessoa}
    Route::get('/pessoas', [PessoaController::class, 'index'])->name('pessoas.index');
    Route::get('/pessoas/certidoes/{certidao}/visualizar', [PessoaCertidaoController::class, 'visualizar'])->name('pessoas.certidoes.visualizar');
    Route::get('/pessoas/certidoes/{certidao}/download', [PessoaCertidaoController::class, 'download'])->name('pessoas.certidoes.download');

    // API de busca de pessoas (autocomplete — todos autenticados)
    Route::get('/api/pessoas/busca', [PessoaBuscaController::class, 'busca'])->name('pessoas.busca');

    // ── ADMIN-ONLY routes ──────────────────────────────────────────
    Route::middleware('admin')->group(function () {

        // Condomínios — escrita
        // IMPORTANT: /condominios/create must come before /condominios/{condominio}
        Route::get('/condominios/create', [CondominioController::class, 'create'])->name('condominios.create');
        Route::post('/condominios', [CondominioController::class, 'store'])->name('condominios.store');
        Route::get('/condominios/{condominio}/edit', [CondominioController::class, 'edit'])->name('condominios.edit');
        Route::put('/condominios/{condominio}', [CondominioController::class, 'update'])->name('condominios.update');
        Route::delete('/condominios/{condominio}', [CondominioController::class, 'destroy'])->name('condominios.destroy');

        // Quadras
        Route::get('/condominios/{condominio}/quadras/create', [QuadraController::class, 'create'])->name('condominios.quadras.create');
        Route::post('/condominios/{condominio}/quadras', [QuadraController::class, 'store'])->name('condominios.quadras.store');
        Route::get('/quadras/{quadra}/edit', [QuadraController::class, 'edit'])->name('quadras.edit');
        Route::put('/quadras/{quadra}', [QuadraController::class, 'update'])->name('quadras.update');
        Route::delete('/quadras/{quadra}', [QuadraController::class, 'destroy'])->name('quadras.destroy');

        // Lotes
        Route::get('/quadras/{quadra}/lotes/create', [LoteController::class, 'create'])->name('quadras.lotes.create');
        Route::post('/quadras/{quadra}/lotes', [LoteController::class, 'store'])->name('quadras.lotes.store');
        Route::get('/lotes/{lote}/edit', [LoteController::class, 'edit'])->name('lotes.edit');
        Route::put('/lotes/{lote}', [LoteController::class, 'update'])->name('lotes.update');
        Route::delete('/lotes/{lote}', [LoteController::class, 'destroy'])->name('lotes.destroy');
        Route::patch('/lotes/{lote}/situacao', [LoteController::class, 'updateSituacao'])->name('lotes.situacao');
        Route::post('/quadras/{quadra}/unificar', [LoteController::class, 'unificar'])->name('quadras.unificar');
        Route::post('/lotes/{lote}/desunificar', [LoteController::class, 'desunificar'])->name('lotes.desunificar');

        // Documentos
        Route::post('/lotes/{lote}/documentos', [DocumentoController::class, 'store'])->name('documentos.store');
        Route::delete('/documentos/{documento}', [DocumentoController::class, 'destroy'])->name('documentos.destroy');

        // Imóveis
        Route::get('/lotes/{lote}/imovel/create', [ImovelController::class, 'create'])->name('imoveis.create');
        Route::post('/lotes/{lote}/imovel', [ImovelController::class, 'store'])->name('imoveis.store');
        Route::get('/imoveis/{imovel}/edit', [ImovelController::class, 'edit'])->name('imoveis.edit');
        Route::put('/imoveis/{imovel}', [ImovelController::class, 'update'])->name('imoveis.update');
        Route::delete('/imoveis/{imovel}', [ImovelController::class, 'destroy'])->name('imoveis.destroy');

        // Histórico do Imóvel
        Route::post('/imoveis/{imovel}/historicos', [ImovelHistoricoController::class, 'store'])->name('imoveis.historicos.store');
        Route::put('/imoveis/historicos/{historico}', [ImovelHistoricoController::class, 'update'])->name('imoveis.historicos.update');
        Route::delete('/imoveis/historicos/{historico}', [ImovelHistoricoController::class, 'destroy'])->name('imoveis.historicos.destroy');

        // Documentos do Imóvel
        Route::post('/imoveis/{imovel}/documentos', [ImovelDocumentoController::class, 'store'])->name('imoveis.documentos.store');
        Route::get('/imoveis/documentos/{documento}/download', [ImovelDocumentoController::class, 'download'])->name('imoveis.documentos.download');
        Route::delete('/imoveis/documentos/{documento}', [ImovelDocumentoController::class, 'destroy'])->name('imoveis.documentos.destroy');

        // Mídias do Imóvel
        Route::post('/imoveis/{imovel}/midias', [ImovelMidiaController::class, 'store'])->name('imoveis.midias.store');
        Route::post('/imoveis/{imovel}/midias/reorder', [ImovelMidiaController::class, 'reorder'])->name('imoveis.midias.reorder');
        Route::patch('/midias/{midia}/capa', [ImovelMidiaController::class, 'setCapa'])->name('imoveis.midias.capa');
        Route::patch('/midias/{midia}/titulo', [ImovelMidiaController::class, 'updateTitulo'])->name('imoveis.midias.titulo');
        Route::delete('/midias/{midia}', [ImovelMidiaController::class, 'destroy'])->name('imoveis.midias.destroy');

        // Editor
        Route::post('/condominios/{condominio}/editor/salvar', [EditorController::class, 'salvar'])->name('condominios.editor.salvar');
        Route::post('/condominios/{condominio}/editor/quadra', [EditorController::class, 'criarQuadra'])->name('condominios.editor.quadra');
        Route::post('/condominios/{condominio}/editor/lote', [EditorController::class, 'criarLote'])->name('condominios.editor.lote');

        // Usuários
        Route::resource('usuarios', UsuarioController::class);

        // Pessoas — escrita (admin)
        Route::get('/pessoas/create', [PessoaController::class, 'create'])->name('pessoas.create');
        Route::post('/pessoas', [PessoaController::class, 'store'])->name('pessoas.store');
        Route::get('/pessoas/{pessoa}/edit', [PessoaController::class, 'edit'])->name('pessoas.edit');
        Route::put('/pessoas/{pessoa}', [PessoaController::class, 'update'])->name('pessoas.update');
        Route::delete('/pessoas/{pessoa}', [PessoaController::class, 'destroy'])->name('pessoas.destroy');

        // Certidões — upload e exclusão (admin)
        Route::post('/pessoas/{pessoa}/certidoes', [PessoaCertidaoController::class, 'store'])->name('pessoas.certidoes.store');
        Route::delete('/pessoas/certidoes/{certidao}', [PessoaCertidaoController::class, 'destroy'])->name('pessoas.certidoes.destroy');

        // Vínculos Lote ↔ Pessoa (admin)
        Route::post('/lotes/{lote}/pessoas', [LoteVinculoController::class, 'store'])->name('lotes.pessoas.store');
        Route::delete('/lotes/{lote}/pessoas/{pessoa}', [LoteVinculoController::class, 'destroy'])->name('lotes.pessoas.destroy');

        // Vínculos Imóvel ↔ Pessoa (admin)
        Route::post('/imoveis/{imovel}/pessoas', [ImovelVinculoController::class, 'store'])->name('imoveis.pessoas.store');
        Route::delete('/imoveis/{imovel}/pessoas/{pessoa}', [ImovelVinculoController::class, 'destroy'])->name('imoveis.pessoas.destroy');

        // Sync Imóvel → Site
        Route::get('/imoveis/{imovel}/sync/preview', [ImovelSyncController::class, 'preview'])->name('imoveis.sync.preview');
        Route::post('/imoveis/{imovel}/sync', [ImovelSyncController::class, 'forcar'])->name('imoveis.sync.forcar');
    });

    // Wildcard routes AFTER all static paths to avoid conflicts
    Route::get('/condominios/{condominio}', [CondominioController::class, 'show'])->name('condominios.show');
    Route::get('/condominios/{condominio}/mapa', [MapaController::class, 'show'])->name('condominios.mapa');
    Route::get('/condominios/{condominio}/editor', [EditorController::class, 'show'])->name('condominios.editor');
    Route::get('/relatorios/{condominio}', [RelatorioController::class, 'condominio'])->name('relatorios.condominio');

    // Pessoas — wildcard LAST (after /pessoas/create and /pessoas/certidoes/*)
    Route::get('/pessoas/{pessoa}', [PessoaController::class, 'show'])->name('pessoas.show');

    // ── MODO APRESENTAÇÃO (admin + apresentador) ───────────────────
    Route::middleware('apresentador')->group(function () {
        Route::get('/apresentacao', [ApresentacaoController::class, 'index'])->name('apresentacao.index');
        Route::get('/apresentacao/{condominio}/mapa', [ApresentacaoController::class, 'mapa'])->name('apresentacao.mapa');
        Route::get('/apresentacao/imovel/{imovel}', [ApresentacaoController::class, 'imovel'])->name('apresentacao.imovel');
    });
});

require __DIR__.'/auth.php';
