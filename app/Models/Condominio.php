<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Condominio extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nome', 'cnpj', 'matricula_cartorio', 'numero_registro',
        'logradouro', 'bairro', 'cidade', 'estado', 'cep', 'municipio_registro',
        'area_total', 'area_verde', 'area_vias', 'total_quadras', 'total_lotes', 'zoneamento',
        'data_aprovacao_prefeitura', 'data_registro_cartorio',
        'incorporadora', 'sindico', 'administradora', 'telefone', 'email',
        'planta_path', 'planta_nome_original',
        'ativo',
    ];

    protected $casts = [
        'data_aprovacao_prefeitura' => 'date',
        'data_registro_cartorio' => 'date',
        'ativo' => 'boolean',
    ];

    public function plantaUrl(): ?string
    {
        return $this->planta_path ? asset('storage/' . $this->planta_path) : null;
    }

    public function quadras(): HasMany
    {
        return $this->hasMany(Quadra::class);
    }
}
