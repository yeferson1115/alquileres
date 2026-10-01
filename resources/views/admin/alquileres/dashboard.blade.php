@extends('layouts.admin')

@section('content')
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Panel de Control</h2>
    
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
            <p class="text-sm text-gray-500 uppercase font-bold">Disponibles</p>
            <p class="text-3xl font-bold">{{ $stats['disponibles'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
            <p class="text-sm text-gray-500 uppercase font-bold">En Alquiler</p>
            <p class="text-3xl font-bold">{{ $stats['alquilados'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-yellow-500">
            <p class="text-sm text-gray-500 uppercase font-bold">En Mantenimiento</p>
            <p class="text-3xl font-bold">{{ $stats['mantenimiento'] }}</p>
        </div>
    </div>

    <!-- Alertas de Devolución (HU-03) -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="bg-red-500 p-4 text-white font-bold">
            ⚠️ Devoluciones Pendientes para Mañana
        </div>
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="p-4">Cliente</th>
                    <th class="p-4">Teléfono</th>
                    <th class="p-4">Vestido</th>
                    <th class="p-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alertas as $alerta)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4">{{ $alerta->cliente->nombre }}</td>
                        <td class="p-4">{{ $alerta->cliente->telefono }}</td>
                        <td class="p-4">{{ $alerta->vestido->codigo }} - {{ $alerta->vestido->descripcion }}</td>
                        <td class="p-4">
                            <button onclick="marcarDevuelto('{{ $alerta->id }}')" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                                Registrar Devolución
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-gray-500">No hay alertas para mañana.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
<script>
    async function marcarDevuelto(id) {
        if(confirm('¿Confirmar la recepción del vestido? Pasará a estado de Lavandería.')) {
            try {
                const res = await axios.post(`{{ url('admin/alquileres') }}/${id}/devolver`);
                alert(res.data.message);
                location.reload();
            } catch (e) {
                alert('Error: ' + e.response.data.error);
            }
        }
    }
</script>
@endsection
