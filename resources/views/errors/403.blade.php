<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Negado</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="text-center px-6">
        <p class="text-6xl font-bold text-indigo-600 mb-4">403</p>
        <h1 class="text-2xl font-semibold text-gray-800 mb-2">Acesso Negado</h1>
        <p class="text-gray-500 mb-6">Você não tem permissão para acessar esta página.</p>
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}"
           class="px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm">
            Voltar
        </a>
    </div>
</body>
</html>
