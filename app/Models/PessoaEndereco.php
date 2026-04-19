<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PessoaEndereco extends Model
{
    protected $fillable = [
        'pessoa_id', 'cep', 'logradouro',
        'numero', 'complemento',
        'bairro', 'cidade', 'estado',
    ];

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class);
    }

    public function enderecoCompleto(): string
    {
        $partes = array_filter([
            $this->logradouro,
            $this->numero ? 'nº ' . $this->numero : null,
            $this->complemento,
            $this->bairro,
            $this->cidade,
            $this->estado,
        ]);

        return implode(', ', $partes) ?: '—';
    }
}
