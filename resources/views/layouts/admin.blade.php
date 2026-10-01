<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Alquileres - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <nav class="w-64 bg-indigo-900 text-white p-6 flex flex-col">
            <div class="mb-10">
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    <span>👗</span> Alquiler Shop
                </h1>
                <p class="text-xs text-indigo-300 mt-1">Panel de Administración</p>
            </div>

            <ul class="space-y-2 flex-1">
                <li class="text-xs font-semibold text-indigo-400 uppercase tracking-wider mb-2 mt-4">Principal</li>
                <li>
                    <a href="{{ route('alquileres.dashboard') }}" 
                       class="flex items-center gap-3 p-3 hover:bg-indigo-800 rounded-lg transition-colors {{ request()->routeIs('alquileres.dashboard') ? 'bg-indigo-800 text-white shadow-inner' : 'text-indigo-100' }}">
                        <span>📊</span> Dashboard Alertas
                    </a>
                </li>

                <li class="text-xs font-semibold text-indigo-400 uppercase tracking-wider mb-2 mt-6">Operaciones</li>
                <li>
                    <a href="{{ route('alquileres.reservar') }}" 
                       class="flex items-center gap-3 p-3 hover:bg-indigo-800 rounded-lg transition-colors {{ request()->routeIs('alquileres.reservar') ? 'bg-indigo-800 text-white shadow-inner' : 'text-indigo-100' }}">
                        <span>📅</span> Nueva Reserva
                    </a>
                </li>
                <li>
                    <a href="{{ route('vestidos.index') }}" 
                       class="flex items-center gap-3 p-3 hover:bg-indigo-800 rounded-lg transition-colors {{ request()->routeIs('vestidos.index') ? 'bg-indigo-800 text-white shadow-inner' : 'text-indigo-100' }}">
                        <span>👗</span> Inventario de Vestidos
                    </a>
                </li>
            </ul>

            <div class="mt-auto pt-6 border-t border-indigo-800">
                <a href="/logout" class="flex items-center gap-3 p-3 text-indigo-300 hover:text-white hover:bg-indigo-800 rounded-lg transition-colors">
                    <span>🚪</span> Salir
                </a>
            </div>
        </nav>
        
        <!-- Content -->
        <main class="flex-1 p-8 overflow-y-auto">
            @yield('content')
        </main>
    </div>
    @yield('scripts')
</body>
</html>
