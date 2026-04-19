<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteSyncLog extends Model
{
    protected $fillable = ['imovel_id', 'evento', 'status', 'payload', 'resposta'];

    protected $casts = ['payload' => 'array'];

    public function imovel(): BelongsTo
    {
        return $this->belongsTo(Imovel::class);
    }

    public function statusCor(): string
    {
        return match ($this->status) {
            'sucesso'  => 'green',
            'erro'     => 'red',
            'pendente' => 'yellow',
            default    => 'gray',
        };
    }

    public function eventoLabel(): string
    {
        return match ($this->evento) {
            'saved'   => 'Salvo',
            'deleted' => 'Excluído',
            'manual'  => 'Manual',
            default   => ucfirst($this->evento),
        };
    }
}
