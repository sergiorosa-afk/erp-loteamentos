<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Imovel extends Model
{
    use SoftDeletes;

    protected $table = 'imoveis';

    protected $fillable = [
        'lote_id', 'tipo', 'nome', 'descricao',
        'area_total', 'area_construida', 'area_privativa',
        'quartos', 'suites', 'banheiros', 'vagas_garagem', 'andares',
        'ano_construcao', 'padrao_acabamento', 'condominio_fechado', 'caracteristicas',
        'matricula_imovel', 'inscricao_municipal', 'cartorio', 'numero_escritura',
        'livro_escritura', 'folha_escritura',
        'logradouro', 'numero_endereco', 'complemento', 'bairro',
        'cidade', 'estado', 'cep', 'latitude', 'longitude',
        'valor_venal', 'valor_mercado', 'valor_iptu_anual', 'data_ultima_avaliacao',
        'situacao_ocupacao', 'observacoes',
        'site_imovel_id', 'site_sincronizado_em',
    ];

    protected $casts = [
        'condominio_fechado'    => 'boolean',
        'data_ultima_avaliacao' => 'date',
        'site_sincronizado_em'  => 'datetime',
        'area_total'            => 'decimal:2',
        'area_construida'       => 'decimal:2',
        'area_privativa'        => 'decimal:2',
        'valor_venal'           => 'decimal:2',
        'valor_mercado'         => 'decimal:2',
        'valor_iptu_anual'      => 'decimal:2',
    ];

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class);
    }

    public function midias(): HasMany
    {
        return $this->hasMany(ImovelMidia::class)->orderBy('ordem');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(ImovelDocumento::class)->orderBy('tipo');
    }

    public function historicos(): HasMany
    {
        return $this->hasMany(ImovelHistorico::class)->orderByDesc('data');
    }

    public function syncLogs(): HasMany
    {
        return $this->hasMany(SiteSyncLog::class)->orderByDesc('created_at');
    }

    public function pessoaHistoricos(): HasMany
    {
        return $this->hasMany(ImovelPessoaHistorico::class)->orderByDesc('created_at');
    }

    public function capa(): ?ImovelMidia
    {
        return $this->midias()->where('capa', true)->where('tipo', 'imagem')->first()
            ?? $this->midias()->where('tipo', 'imagem')->first();
    }

    public function tipoLabel(): string
    {
        return match ($this->tipo) {
            'casa'        => 'Casa',
            'apartamento' => 'Apartamento',
            'terreno'     => 'Terreno',
            'galpao'      => 'Galpão',
            'sala'        => 'Sala Comercial',
            'chacara'     => 'Chácara / Sítio',
            default       => $this->tipo ?? 'Imóvel',
        };
    }

    public function ultimaVenda(): ?ImovelHistorico
    {
        return $this->historicos()->whereIn('tipo', ['venda', 'compra'])->first();
    }

    public function pessoas(): BelongsToMany
    {
        return $this->belongsToMany(Pessoa::class, 'imovel_pessoa')
            ->withPivot(['papel', 'data_vinculo', 'obs'])
            ->withTimestamps()
            ->orderBy('pessoas.nome');
    }

    public function proprietario(): ?Pessoa
    {
        return $this->pessoas()->wherePivot('papel', 'proprietario')->first();
    }
}
