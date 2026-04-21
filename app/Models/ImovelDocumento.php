<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ImovelDocumento extends Model
{
    protected $fillable = [
        'imovel_id', 'tipo', 'titulo', 'descricao',
        'path', 'nome_original', 'tamanho_bytes', 'mime_type',
        'numero_documento', 'orgao_emissor',
        'data_emissao', 'data_vencimento', 'uploaded_by',
    ];

    protected $casts = [
        'data_emissao'    => 'date',
        'data_vencimento' => 'date',
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
        return Storage::disk('public')->url($this->path);
    }

    public function tamanhoFormatado(): string
    {
        if (!$this->tamanho_bytes) return '';
        $bytes = $this->tamanho_bytes;
        if ($bytes < 1024) return "{$bytes} B";
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    public function tipoLabel(): string
    {
        return match ($this->tipo) {
            'matricula'        => 'Matrícula',
            'escritura'        => 'Escritura',
            'iptu'             => 'IPTU',
            'certidao_negativa'=> 'Certidão Negativa',
            'habite_se'        => 'Habite-se',
            'planta'           => 'Planta Baixa',
            'condominio'       => 'Doc. Condomínio',
            'procuracao'       => 'Procuração',
            'contrato'         => 'Contrato',
            default            => 'Outro',
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

    public function vencido(): bool
    {
        return $this->data_vencimento && $this->data_vencimento->isPast();
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
