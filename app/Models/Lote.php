<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'quadra_id', 'parent_lote_id', 'numero', 'codigo_interno',
        'area', 'testada', 'comprimento',
        'situacao', 'unificado', 'lotes_originais', 'restricoes',
        'proprietario_nome', 'valor_tabela', 'poligono',
    ];

    protected $casts = [
        'lotes_originais' => 'array',
        'unificado'       => 'boolean',
        'poligono'        => 'array',
    ];

    public function quadra(): BelongsTo
    {
        return $this->belongsTo(Quadra::class);
    }

    public function loteUnificado(): BelongsTo
    {
        return $this->belongsTo(Lote::class, 'parent_lote_id');
    }

    public function lotesOriginaisRelation(): HasMany
    {
        return $this->hasMany(Lote::class, 'parent_lote_id')->withTrashed();
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(LoteDocumento::class);
    }

    public function imovel(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Imovel::class);
    }

    public function pessoas(): BelongsToMany
    {
        return $this->belongsToMany(Pessoa::class, 'lote_pessoa')
            ->withPivot(['papel', 'data_vinculo', 'obs'])
            ->withTimestamps()
            ->orderBy('pessoas.nome');
    }

    public function proprietario(): ?Pessoa
    {
        return $this->pessoas()->wherePivot('papel', 'proprietario')->first();
    }
}
