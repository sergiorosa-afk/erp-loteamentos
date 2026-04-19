<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImovelHistorico extends Model
{
    protected $fillable = [
        'imovel_id', 'tipo', 'data', 'valor',
        'proprietario_anterior', 'cpf_cnpj_anterior',
        'proprietario_atual', 'cpf_cnpj_atual',
        'cartorio', 'numero_escritura', 'numero_registro',
        'corretor', 'descricao', 'observacoes', 'registrado_por',
    ];

    protected $casts = [
        'data'  => 'date',
        'valor' => 'decimal:2',
    ];

    public function imovel(): BelongsTo
    {
        return $this->belongsTo(Imovel::class);
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function tipoLabel(): string
    {
        return match ($this->tipo) {
            'compra'    => 'Compra',
            'venda'     => 'Venda',
            'avaliacao' => 'Avaliação',
            'reforma'   => 'Reforma',
            'locacao'   => 'Locação',
            'permuta'   => 'Permuta',
            'inventario'=> 'Inventário',
            default     => 'Outro',
        };
    }

    public function tipoIcone(): string
    {
        return match ($this->tipo) {
            'compra'    => '🏠',
            'venda'     => '💰',
            'avaliacao' => '📊',
            'reforma'   => '🔨',
            'locacao'   => '🔑',
            'permuta'   => '🔄',
            'inventario'=> '📋',
            default     => '📌',
        };
    }

    public function tipoCor(): string
    {
        return match ($this->tipo) {
            'compra'    => 'blue',
            'venda'     => 'green',
            'avaliacao' => 'purple',
            'reforma'   => 'orange',
            'locacao'   => 'yellow',
            'permuta'   => 'teal',
            'inventario'=> 'gray',
            default     => 'slate',
        };
    }
}
