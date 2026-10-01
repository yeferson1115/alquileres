<?php

namespace App\Services;

use App\Models\Alquiler;
use App\Models\Vestido;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AlquilerService
{
    /**
     * HU-01: Reserva/Apartado del Vestido
     */
    public function reservarVestido(array $data)
    {
        return DB::transaction(function () use ($data) {
            $vestido = Vestido::where('id', $data['vestido_id'])->firstOrFail();

            if ($vestido->estado !== 'DISPONIBLE') {
                throw new \Exception("El vestido no está disponible.");
            }

            $saldo = $data['valor_total'] - $data['valor_abono'];

            $alquiler = Alquiler::create([
                'cliente_id' => $data['cliente_id'],
                'vestido_id' => $data['vestido_id'],
                'valor_total' => $data['valor_total'],
                'valor_abono' => $data['valor_abono'],
                'saldo_pendiente' => $saldo,
                'fecha_entrega_est' => $data['fecha_entrega_est'],
                'estado_alquiler' => 'RESERVADO',
            ]);

            $vestido->update(['estado' => 'RESERVADO']);

            return $alquiler;
        });
    }

    /**
     * HU-02: Retiro del Vestido (Despacho)
     */
    public function despacharVestido($alquilerId, $pagoFinal, $diasPrestamo)
    {
        return DB::transaction(function () use ($alquilerId, $pagoFinal, $diasPrestamo) {
            $alquiler = Alquiler::with('vestido')->findOrFail($alquilerId);

            if ($pagoFinal < $alquiler->saldo_pendiente) {
                throw new \Exception("El pago es insuficiente para cubrir el saldo pendiente.");
            }

            $fechaSalida = Carbon::now();
            $fechaLimite = Carbon::now()->addDays($diasPrestamo);

            $alquiler->update([
                'saldo_pendiente' => 0,
                'fecha_salida_real' => $fechaSalida,
                'fecha_devolucion_limite' => $fechaLimite,
                'estado_alquiler' => 'EN_ALQUILER',
            ]);

            $alquiler->vestido->update(['estado' => 'EN_ALQUILER']);

            return $alquiler;
        });
    }

    /**
     * HU-03: Alarma de Recordatorio (Día previo)
     */
    public function obtenerAlertasDevolucion()
    {
        $manana = Carbon::tomorrow()->toDateString();

        return Alquiler::with(['cliente', 'vestido'])
            ->whereDate('fecha_devolucion_limite', $manana)
            ->where('estado_alquiler', 'EN_ALQUILER')
            ->get();
    }

    /**
     * HU-04: Recepción y Devolución
     */
    public function recibirVestido($alquilerId)
    {
        return DB::transaction(function () use ($alquilerId) {
            $alquiler = Alquiler::with('vestido')->findOrFail($alquilerId);

            $alquiler->update([
                'fecha_devolucion_real' => Carbon::now(),
                'estado_alquiler' => 'DEVUELTO',
            ]);

            $alquiler->vestido->update(['estado' => 'EN_LAVANDERIA']);

            return $alquiler;
        });
    }

    /**
     * HU-05: Mantenimiento y Reingreso
     */
    public function actualizarEstadoMantenimiento($vestidoId, $nuevoEstado)
    {
        $vestido = Vestido::findOrFail($vestidoId);
        $vestido->update(['estado' => $nuevoEstado]);
        return $vestido;
    }
}
