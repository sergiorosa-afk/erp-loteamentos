<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PessoaCertidao extends Model
{
    protected $table = 'pessoa_certidoes';
    protected $fillable = [
        'pessoa_id', 'tipo', 'titulo',
        'numero_documento', 'orgao_emissor',
        'data_emissao', 'data_vencimento',
        'arquivo_path', 'nome_original',
        'tamanho_bytes', 'mime_type', 'uploaded_by',
    ];

    protected $casts = [
        'data_emissao'    => 'date',
        'data_vencimento' => 'date',
    ];

    // ── Relations ────────────────────────────────────────────────────

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ── Helpers ──────────────────────────────────────────────────────

    public function url(): string
    {
        return Storage::url($this->arquivo_path);
    }

    public function vencida(): bool
    {
        return $this->data_vencimento && $this->data_vencimento->isPast();
    }

    public function tamanhoFormatado(): string
    {
        if (! $this->tamanho_bytes) return '';
        $bytes = $this->tamanho_bytes;
        if ($bytes < 1024) return "{$bytes} B";
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    public function tipoLabel(): string
    {
        return match ($this->tipo) {
            'rg'                    => 'RG',
            'cpf'                   => 'CPF',
            'cnh'                   => 'CNH',
            'comprovante_residencia'=> 'Comprovante de Residência',
            'certidao_nascimento'   => 'Certidão de Nascimento',
            'certidao_casamento'    => 'Certidão de Casamento',
            'certidao_obito'        => 'Certidão de Óbito',
            'procuracao'            => 'Procuração',
            default                 => 'Outro',
        };
    }

    public function icone(): string
    {
        return match (true) {
            str_contains($this->mime_type ?? '', 'pdf')   => '📄',
            str_contains($this->mime_type ?? '', 'image') => '🖼️',
            str_contains($this->mime_type ?? '', 'word')  => '📝',
            default                                        => '📎',
        };
    }

    public function isPdf(): bool
    {
        return str_contains($this->mime_type ?? '', 'pdf');
    }

    public function isImage(): bool
    {
        return str_contains($this->mime_type ?? '', 'image');
    }
}
