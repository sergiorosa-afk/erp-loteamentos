<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quadra extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'condominio_id', 'codigo', 'area_total', 'observacoes', 'poligono',
    ];

    protected $casts = [
        'poligono' => 'array',
    ];

    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }

    public function lotes(): HasMany
    {
        return $this->hasMany(Lote::class);
    }
}
