<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Usuários</h2>
            <a href="{{ route('usuarios.create') }}"
               class="px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                + Novo Usuário
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded text-sm">
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded text-sm">
                {{ session('error') }}
            </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                @if($usuarios->isEmpty())
                    <p class="px-6 py-8 text-center text-gray-400 text-sm">Nenhum usuário cadastrado.</p>
                @else
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">E-mail</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Perfil</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($usuarios as $usuario)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium text-gray-900">
                                {{ $usuario->name }}
                                @if($usuario->id === auth()->id())
                                    <span class="ml-1 text-xs text-gray-400">(você)</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-gray-500">{{ $usuario->email }}</td>
                            <td class="px-6 py-3 text-center">
                                @if($usuario->role === 'admin')
                                    <span class="bg-indigo-100 text-indigo-700 text-xs px-2 py-0.5 rounded-full">Admin</span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">Leitor</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-right space-x-3">
                                <a href="{{ route('usuarios.edit', $usuario) }}"
                                   class="text-indigo-600 hover:text-indigo-900 text-sm">Editar</a>
                                @if($usuario->id !== auth()->id())
                                <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Remover {{ addslashes($usuario->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700 text-sm">Remover</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
