<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ImovelMidia extends Model
{
    protected $fillable = [
        'imovel_id', 'tipo', 'titulo', 'descricao',
        'path', 'nome_original', 'tamanho_bytes', 'mime_type',
        'ordem', 'capa', 'uploaded_by',
    ];

    protected $casts = [
        'capa'  => 'boolean',
        'ordem' => 'integer',
    ];

    public function imovel(): BelongsTo
    {
        return $this->belongsTo(Imovel::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function url(): string
    {
        return Storage::url($this->path);
    }

    public function tamanhoFormatado(): string
    {
        if (!$this->tamanho_bytes) return '';
        $bytes = $this->tamanho_bytes;
        if ($bytes < 1024) return "{$bytes} B";
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    public function icone(): string
    {
        return match ($this->tipo) {
            'imagem' => '🖼️',
            'video'  => '🎬',
            'pdf'    => '📄',
            default  => '📎',
        };
    }
}
