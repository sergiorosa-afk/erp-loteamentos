<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookLog extends Model
{
    protected $fillable = ['source', 'payload', 'status', 'pessoa_id', 'mensagem'];

    protected $casts = ['payload' => 'array'];

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class);
    }

    public function statusCor(): string
    {
        return match ($this->status) {
            'sucesso'   => 'green',
            'duplicata' => 'yellow',
            'erro'      => 'red',
            default     => 'gray',
        };
    }
}
