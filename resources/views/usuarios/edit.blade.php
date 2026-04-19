<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-gray-500 text-sm mb-1">
            <a href="{{ route('usuarios.index') }}" class="hover:text-indigo-600">Usuários</a>
            <span>/</span>
            <span class="text-gray-900">Editar</span>
        </div>
        <h2 class="font-semibold text-xl text-gray-800">Editar — {{ $usuario->name }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form action="{{ route('usuarios.update', $usuario) }}" method="POST" class="space-y-5">
                    @csrf @method('PUT')

                    <div>
                        <x-input-label for="name" value="Nome" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                      :value="old('name', $usuario->name)" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="email" value="E-mail" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                      :value="old('email', $usuario->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="role" value="Perfil" />
                        <select id="role" name="role"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="viewer" {{ old('role', $usuario->role) === 'viewer' ? 'selected' : '' }}>Leitor (somente leitura)</option>
                            <option value="admin"  {{ old('role', $usuario->role) === 'admin'  ? 'selected' : '' }}>Admin (acesso total)</option>
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="password" value="Nova Senha (deixe em branco para não alterar)" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full"
                                      autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('usuarios.index') }}"
                           class="px-4 py-2 border border-gray-300 rounded text-sm text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </a>
                        <x-primary-button>Salvar Alterações</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
