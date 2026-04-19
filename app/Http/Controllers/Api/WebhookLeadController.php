<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pessoa;
use App\Models\PessoaEndereco;
use App\Models\User;
use App\Models\WebhookLog;
use App\Notifications\NovoLeadNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookLeadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nome'      => 'required|string|max:255',
            'email'     => 'nullable|email|max:255',
            'cpf_cnpj'  => 'nullable|string|max:20',
            'telefone'  => 'nullable|string|max:20',
            'celular'   => 'nullable|string|max:20',
            'cep'       => 'nullable|string|max:10',
            'logradouro'=> 'nullable|string|max:255',
            'numero'    => 'nullable|string|max:20',
            'bairro'    => 'nullable|string|max:100',
            'cidade'    => 'nullable|string|max:100',
            'estado'    => 'nullable|string|max:2',
            'obs'       => 'nullable|string',
        ]);

        // --- Anti-duplicata ---
        $pessoa = null;
        $status = 'sucesso';

        if (! empty($data['email'])) {
            $pessoa = Pessoa::where('email', $data['email'])->first();
        }

        if (! $pessoa && ! empty($data['cpf_cnpj'])) {
            $cpfLimpo = preg_replace('/\D/', '', $data['cpf_cnpj']);
            $pessoa = Pessoa::whereRaw(
                "REPLACE(REPLACE(REPLACE(cpf_cnpj, '.', ''), '-', ''), '/', '') = ?",
                [$cpfLimpo]
            )->first();
        }

        if ($pessoa) {
            $nota = "\n[Lead recebido novamente via site em " . now()->format('d/m/Y H:i') . ']';
            $pessoa->obs = trim(($pessoa->obs ?? '') . $nota);
            $pessoa->save();
            $status = 'duplicata';
        } else {
            $pessoa = Pessoa::create([
                'nome'     => $data['nome'],
                'email'    => $data['email'] ?? null,
                'cpf_cnpj' => $data['cpf_cnpj'] ?? null,
                'telefone' => $data['telefone'] ?? null,
                'celular'  => $data['celular'] ?? null,
                'tipo'     => 'lead',
                'origem'   => 'site',
                'obs'      => $data['obs'] ?? null,
            ]);

            if (! empty($data['logradouro']) || ! empty($data['cidade'])) {
                $pessoa->endereco()->create([
                    'cep'        => $data['cep'] ?? null,
                    'logradouro' => $data['logradouro'] ?? null,
                    'numero'     => $data['numero'] ?? null,
                    'bairro'     => $data['bairro'] ?? null,
                    'cidade'     => $data['cidade'] ?? null,
                    'estado'     => $data['estado'] ?? null,
                ]);
            }

            User::where('role', 'admin')->each(
                fn ($admin) => $admin->notify(new NovoLeadNotification($pessoa))
            );
        }

        WebhookLog::create([
            'source'    => 'site',
            'payload'   => $request->all(),
            'status'    => $status,
            'pessoa_id' => $pessoa->id,
            'mensagem'  => $status === 'duplicata'
                ? 'Lead já existe; obs atualizada'
                : 'Lead criado com sucesso',
        ]);

        return response()->json([
            'ok'        => true,
            'status'    => $status,
            'pessoa_id' => $pessoa->id,
        ]);
    }
}
