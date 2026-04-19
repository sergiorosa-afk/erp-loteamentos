<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pessoa extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nome', 'cpf_cnpj', 'tipo',
        'telefone', 'celular', 'email',
        'data_nascimento', 'estado_civil',
        'profissao', 'nacionalidade',
        'rg', 'orgao_emissor_rg',
        'origem', 'obs',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
    ];

    // ── Relations ────────────────────────────────────────────────────

    public function endereco(): HasOne
    {
        return $this->hasOne(PessoaEndereco::class);
    }

    public function certidoes(): HasMany
    {
        return $this->hasMany(PessoaCertidao::class)->orderByDesc('created_at');
    }

    public function imoveis(): BelongsToMany
    {
        return $this->belongsToMany(Imovel::class, 'imovel_pessoa')
            ->withPivot(['papel', 'data_vinculo', 'obs'])
            ->withTimestamps();
    }

    public function lotes(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Lote::class, 'lote_pessoa')
            ->withPivot(['papel', 'data_vinculo', 'obs'])
            ->withTimestamps();
    }

    public function imovelHistoricos(): HasMany
    {
        return $this->hasMany(ImovelPessoaHistorico::class)->orderByDesc('created_at');
    }

    // ── Helpers ──────────────────────────────────────────────────────

    public function tipoLabel(): string
    {
        return match ($this->tipo) {
            'lead'      => 'Lead',
            'prospect'  => 'Prospect',
            'cliente'   => 'Cliente',
            default     => ucfirst($this->tipo),
        };
    }

    public function tipoCor(): string
    {
        return match ($this->tipo) {
            'lead'     => 'yellow',
            'prospect' => 'blue',
            'cliente'  => 'green',
            default    => 'gray',
        };
    }

    public function estadoCivilLabel(): string
    {
        return match ($this->estado_civil) {
            'solteiro'      => 'Solteiro(a)',
            'casado'        => 'Casado(a)',
            'divorciado'    => 'Divorciado(a)',
            'viuvo'         => 'Viúvo(a)',
            'uniao_estavel' => 'União Estável',
            'separado'      => 'Separado(a)',
            default         => '—',
        };
    }

    public function cpfCnpjFormatado(): string
    {
        $v = preg_replace('/\D/', '', $this->cpf_cnpj ?? '');
        if (strlen($v) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $v);
        }
        if (strlen($v) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $v);
        }
        return $this->cpf_cnpj ?? '';
    }

    public function certidoesVencidas(): int
    {
        return $this->certidoes->filter(fn ($c) => $c->vencida())->count();
    }
}
