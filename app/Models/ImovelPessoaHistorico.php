<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImovelPessoaHistorico extends Model
{
    protected $fillable = [
        'imovel_id', 'pessoa_id', 'papel', 'acao',
        'data_vinculo', 'valor_imovel', 'obs', 'registrado_por',
    ];

    protected $casts = [
        'data_vinculo' => 'date',
        'valor_imovel' => 'decimal:2',
    ];

    public function imovel(): BelongsTo
    {
        return $this->belongsTo(Imovel::class);
    }

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function papelLabel(): string
    {
        return match ($this->papel) {
            'proprietario' => '👑 Proprietário',
            'locatario'    => '🏠 Locatário',
            'comprador'    => '🛒 Comprador',
            'interessado'  => '🔍 Interessado',
            default        => ucfirst($this->papel),
        };
    }

    public function acaoCor(): string
    {
        return $this->acao === 'vinculado' ? 'green' : 'gray';
    }
}
