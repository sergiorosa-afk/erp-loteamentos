<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoteDocumento extends Model
{
    protected $fillable = [
        'lote_id', 'nome_original', 'path', 'tipo',
        'tamanho_bytes', 'mime_type', 'uploaded_by',
    ];

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function url(): string
    {
        return asset('storage/' . $this->path);
    }

    public function tamanhoFormatado(): string
    {
        $bytes = $this->tamanho_bytes ?? 0;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    public function icone(): string
    {
        $ext = strtolower(pathinfo($this->nome_original, PATHINFO_EXTENSION));
        return match($ext) {
            'pdf'                    => '📄',
            'jpg','jpeg','png','gif' => '🖼',
            'doc','docx'             => '📝',
            'xls','xlsx'             => '📊',
            default                  => '📎',
        };
    }

    public function isPdf(): bool
    {
        if ($this->mime_type) {
            return str_contains($this->mime_type, 'pdf');
        }
        return strtolower(pathinfo($this->nome_original, PATHINFO_EXTENSION)) === 'pdf';
    }

    public function isImage(): bool
    {
        if ($this->mime_type) {
            return str_contains($this->mime_type, 'image');
        }
        return in_array(
            strtolower(pathinfo($this->nome_original, PATHINFO_EXTENSION)),
            ['jpg', 'jpeg', 'png', 'gif', 'webp']
        );
    }

    public function mimeTypeResolved(): string
    {
        if ($this->mime_type) return $this->mime_type;
        $ext = strtolower(pathinfo($this->nome_original, PATHINFO_EXTENSION));
        return match($ext) {
            'pdf'        => 'application/pdf',
            'jpg','jpeg' => 'image/jpeg',
            'png'        => 'image/png',
            'gif'        => 'image/gif',
            default      => 'application/octet-stream',
        };
    }
}
