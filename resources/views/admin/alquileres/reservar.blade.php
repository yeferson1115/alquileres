@extends('layouts.app')

@section('content')
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Nueva Reserva de Vestido1</h2>

    <div class="bg-white p-8 rounded-lg shadow-md max-w-2xl mx-auto">
        <form id="reservaForm" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cliente</label>
                    <select name="cliente_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2">
                        <option value="">Seleccione un cliente</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Vestido Disponible</label>
                    <select name="vestido_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2">
                        <option value="">Seleccione un vestido</option>
                        @foreach($vestidos as $vestido)
                            <option value="{{ $vestido->id }}">{{ $vestido->codigo }} - {{ $vestido->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Valor Total ($)</label>
                    <input type="number" name="valor_total" id="valor_total" step="0.01" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Valor Abono ($)</label>
                    <input type="number" name="valor_abono" id="valor_abono" step="0.01" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Saldo Pendiente</label>
                    <div id="saldo_pendiente" class="mt-1 p-2 text-xl font-bold text-red-600 bg-red-50 rounded border border-red-200">$ 0.00</div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Fecha Estimada de Entrega</label>
                <input type="date" name="fecha_entrega_est" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-2">
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white py-3 px-4 rounded-md font-bold hover:bg-indigo-700 transition duration-200">
                Confirmar Reserva
            </button>
        </form>
    </div>
@endsection

@section('scripts')
<script>
    const totalInput = document.getElementById('valor_total');
    const abonoInput = document.getElementById('valor_abono');
    const saldoDiv = document.getElementById('saldo_pendiente');

    function calcularSaldo() {
        const total = parseFloat(totalInput.value) || 0;
        const abono = parseFloat(abonoInput.value) || 0;
        const saldo = total - abono;
        saldoDiv.innerText = '$ ' + saldo.toFixed(2);
    }

    totalInput.addEventListener('input', calcularSaldo);
    abonoInput.addEventListener('input', calcularSaldo);

    document.getElementById('reservaForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());

        try {
            const res = await axios.post('{{ route('alquileres.reservar.store') }}', data);
            alert(res.data.message);
            location.href = '{{ route('alquileres.dashboard') }}';
        } catch (e) {
            alert('Error: ' + e.response.data.error);
        }
    });
</script>
@endsection
