<?php

namespace App\Services;

use App\Models\Alquiler;
use App\Models\Vestido;
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
            $vestido = Vestido::whereKey($data['vestido_id'])->lockForUpdate()->firstOrFail();

            if ($vestido->estado !== 'DISPONIBLE') {
                throw new \Exception("El vestido no está disponible.");
            }

            if ((float) $data['valor_abono'] > (float) $data['valor_total']) {
                throw new \DomainException('El abono no puede ser mayor al valor total del alquiler.');
            }

            $saldo = round((float) $data['valor_total'] - (float) $data['valor_abono'], 2);

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

            if ((float) $data['valor_abono'] > 0) {
                $alquiler->pagos()->create([
                    'monto' => $data['valor_abono'],
                    'metodo_pago' => $data['metodo_pago'] ?? 'EFECTIVO',
                ]);
            }

            return $alquiler;
        });
    }

    /**
     * HU-02: Retiro del Vestido (Despacho)
     */
    public function despacharVestido($alquilerId, $pagoFinal, $diasPrestamo, string $metodoPago = 'EFECTIVO')
    {
        return DB::transaction(function () use ($alquilerId, $pagoFinal, $diasPrestamo, $metodoPago) {
            $alquiler = Alquiler::with('vestido')->lockForUpdate()->findOrFail($alquilerId);

            if ($alquiler->estado_alquiler !== 'RESERVADO' || $alquiler->vestido->estado !== Vestido::RESERVADO) {
                throw new \DomainException('Solo se pueden despachar reservas activas.');
            }

            if ($pagoFinal < $alquiler->saldo_pendiente) {
                throw new \Exception("El pago es insuficiente para cubrir el saldo pendiente.");
            }

            $fechaSalida = Carbon::now();
            $fechaLimite = $fechaSalida->copy()->addDays($diasPrestamo)->startOfDay();

            if ((float) $pagoFinal > 0) {
                $alquiler->pagos()->create(['monto' => $pagoFinal, 'metodo_pago' => $metodoPago]);
            }

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

    /** Marca como retrasados los vestidos cuya fecha límite ya venció. */
    public function marcarAlquileresRetrasados(): int
    {
        return DB::transaction(function () {
            $alquileres = Alquiler::with('vestido')
                ->where('estado_alquiler', 'EN_ALQUILER')
                ->whereDate('fecha_devolucion_limite', '<', Carbon::today())
                ->lockForUpdate()
                ->get();

            foreach ($alquileres as $alquiler) {
                $alquiler->update(['estado_alquiler' => 'RETRASADO']);
                $alquiler->vestido->update(['estado' => Vestido::RETRASADO]);
            }

            return $alquileres->count();
        });
    }

    /**
     * HU-04: Recepción y Devolución
     */
    public function recibirVestido($alquilerId)
    {
        return DB::transaction(function () use ($alquilerId) {
            $alquiler = Alquiler::with('vestido')->lockForUpdate()->findOrFail($alquilerId);

            if (!in_array($alquiler->estado_alquiler, ['EN_ALQUILER', 'RETRASADO'], true)) {
                throw new \DomainException('Solo se pueden recibir vestidos que estén fuera de tienda.');
            }

            $alquiler->update([
                'fecha_devolucion_real' => Carbon::now(),
                'estado_alquiler' => 'DEVUELTO',
            ]);

            $alquiler->vestido->update(['estado' => Vestido::EN_LAVANDERIA]);

            return $alquiler;
        });
    }

    /**
     * HU-05: Mantenimiento y Reingreso
     */
    public function actualizarEstadoMantenimiento($vestidoId, $nuevoEstado)
    {
        return DB::transaction(function () use ($vestidoId, $nuevoEstado) {
            $vestido = Vestido::whereKey($vestidoId)->lockForUpdate()->firstOrFail();

            if (!$vestido->puedeCambiarA($nuevoEstado)) {
                throw new \DomainException("No se permite cambiar un vestido {$vestido->estado} a {$nuevoEstado}.");
            }

            $vestido->update(['estado' => $nuevoEstado]);

            return $vestido;
        });
    }
}
