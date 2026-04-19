<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <div class="flex items-center gap-2 text-gray-500 text-sm mb-1">
                    <a href="{{ route('relatorios.index') }}" class="hover:text-indigo-600">Relatórios</a>
                    <span>/</span>
                    <span class="text-gray-900">{{ $condominio->nome }}</span>
                </div>
                <h2 class="font-semibold text-xl text-gray-800">Relatório — {{ $condominio->nome }}</h2>
            </div>
            <button onclick="window.print()"
                    class="px-4 py-2 border border-gray-300 rounded text-sm text-gray-700 hover:bg-gray-50 print:hidden">
                🖨 Imprimir
            </button>
        </div>
    </x-slot>

    <style>
        @media print {
            nav, header, .print\:hidden { display: none !important; }
            .py-8 { padding: 0 !important; }
            .max-w-5xl { max-width: 100% !important; }
            .shadow-sm { box-shadow: none !important; }
        }
    </style>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Resumo geral --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Resumo Geral</h3>
                @php
                    $colors = ['disponivel'=>'green','reservado'=>'yellow','vendido'=>'red','permutado'=>'purple'];
                @endphp
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 text-center">
                    @foreach($situacoes as $s)
                    <div class="rounded-lg border border-gray-200 p-4">
                        <p class="text-2xl font-bold text-{{ $colors[$s] ?? 'gray' }}-600">{{ $stats[$s]['count'] }}</p>
                        <p class="text-xs text-gray-500 capitalize mt-1">{{ $s }}</p>
                        <p class="text-xs text-gray-400">{{ $stats[$s]['area'] > 0 ? number_format($stats[$s]['area'], 0, ',', '.') . ' m²' : '—' }}</p>
                    </div>
                    @endforeach
                    <div class="rounded-lg border-2 border-gray-900 p-4">
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total']['count'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">Total</p>
                        <p class="text-xs text-gray-400">{{ $stats['total']['area'] > 0 ? number_format($stats['total']['area'], 0, ',', '.') . ' m²' : '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Resumo financeiro --}}
            @if($stats['total']['valor'] > 0)
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Resumo Financeiro (Valor de Tabela)</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-6 text-sm">
                    @foreach($situacoes as $s)
                    @if($stats[$s]['valor'] > 0)
                    <div>
                        <p class="text-gray-500 capitalize">{{ $s }}</p>
                        <p class="font-semibold text-gray-900 text-base">R$ {{ number_format($stats[$s]['valor'], 2, ',', '.') }}</p>
                    </div>
                    @endif
                    @endforeach
                    <div class="col-span-2 sm:col-span-1 border-t sm:border-t-0 sm:border-l border-gray-200 sm:pl-6 pt-3 sm:pt-0">
                        <p class="text-gray-500">Total geral</p>
                        <p class="font-bold text-gray-900 text-lg">R$ {{ number_format($stats['total']['valor'], 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Detalhe por quadra --}}
            @foreach($condominio->quadras as $quadra)
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                    <h4 class="font-semibold text-gray-800">Quadra {{ $quadra->codigo }}</h4>
                </div>
                @if($quadra->lotes->isEmpty())
                    <p class="px-6 py-4 text-sm text-gray-400">Nenhum lote nesta quadra.</p>
                @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Lote</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cód. Interno</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Área (m²)</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Testada (m)</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Valor Tabela</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Situação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($quadra->lotes as $lote)
                            @php $c = $colors[$lote->situacao] ?? 'gray'; @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 font-medium text-gray-900">
                                    <a href="{{ route('lotes.show', $lote) }}" class="hover:text-indigo-600 print:no-underline">
                                        {{ $lote->numero }}
                                        @if($lote->unificado) <span class="text-xs text-indigo-500">(unif.)</span> @endif
                                    </a>
                                </td>
                                <td class="px-4 py-2 text-gray-500">{{ $lote->codigo_interno ?? '—' }}</td>
                                <td class="px-4 py-2 text-right text-gray-700">{{ $lote->area ? number_format($lote->area, 2, ',', '.') : '—' }}</td>
                                <td class="px-4 py-2 text-right text-gray-700">{{ $lote->testada ? number_format($lote->testada, 2, ',', '.') : '—' }}</td>
                                <td class="px-4 py-2 text-right text-gray-700">{{ $lote->valor_tabela ? 'R$ ' . number_format($lote->valor_tabela, 2, ',', '.') : '—' }}</td>
                                <td class="px-4 py-2 text-center">
                                    <span class="text-xs bg-{{ $c }}-100 text-{{ $c }}-800 px-2 py-0.5 rounded-full capitalize">{{ $lote->situacao }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
            @endforeach

        </div>
    </div>
</x-app-layout>
