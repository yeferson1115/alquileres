<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AlquilerService;
use App\Models\Vestido;
use App\Models\Cliente;
use App\Models\Alquiler;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class AlquilerController extends Controller
{
    protected $alquilerService;

    public function __construct(AlquilerService $alquilerService)
    {
        $this->alquilerService = $alquilerService;
    }

    // --- GESTIÓN DE CLIENTES ---
    public function indexClientes(): View
    {
        $clientes = Cliente::all();
        return view('admin.clientes.index', compact('clientes'));
    }

    public function createCliente(): View
    {
        return view('admin.clientes.create');
    }

    public function storeCliente(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $cliente = Cliente::create($validated);
        return response()->json(['message' => 'Cliente creado exitosamente', 'data' => $cliente]);
    }

    // --- GESTIÓN DE VESTIDOS ---
    public function indexVestidos(): View
    {
        $vestidos = Vestido::all();
        return view('admin.vestidos.index', compact('vestidos'));
    }

    public function createVestido(): View
    {
        return view('admin.vestidos.create');
    }

    public function storeVestido(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codigo' => 'required|string|unique:vestidos,codigo',
            'descripcion' => 'required|string',
            'talla' => 'required|string',
            'color' => 'required|string',
            'estado' => 'required|in:DISPONIBLE,RESERVADO,EN_ALQUILER,RETRASADO,EN_LAVANDERIA,EN_PLANCHADO',
        ]);

        $vestido = Vestido::create($validated);
        return response()->json(['message' => 'Vestido agregado al inventario', 'data' => $vestido]);
    }

    // --- CICLO DE ALQUILER ---
    public function dashboard(): View
    {
        $alertas = $this->alquilerService->obtenerAlertasDevolucion();
        $stats = [
            'disponibles' => Vestido::where('estado', 'DISPONIBLE')->count(),
            'alquilados' => Vestido::where('estado', 'EN_ALQUILER')->count(),
            'mantenimiento' => Vestido::whereIn('estado', ['EN_LAVANDERIA', 'EN_PLANCHADO'])->count(),
        ];
        return view('admin.alquileres.dashboard', compact('alertas', 'stats'));
    }

    public function createReserva(): View
    {
        $vestidos = Vestido::where('estado', 'DISPONIBLE')->get();
        $clientes = Cliente::all();
        return view('admin.alquileres.reservar', compact('vestidos', 'clientes'));
    }

    public function reservar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'vestido_id' => 'required|exists:vestidos,id',
            'valor_total' => 'required|numeric|min:0',
            'valor_abono' => 'required|numeric|min:0',
            'fecha_entrega_est' => 'required|date',
        ]);

        try {
            $alquiler = $this->alquilerService->reservarVestido($validated);
            return response()->json(['message' => 'Reserva creada exitosamente', 'data' => $alquiler], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function despachar(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'pago_final' => 'required|numeric|min:0',
            'dias_prestamo' => 'required|integer|min:1',
        ]);

        try {
            $alquiler = $this->alquilerService->despacharVestido($id, $validated['pago_final'], $validated['dias_prestamo']);
            return response()->json(['message' => 'Despacho registrado exitosamente', 'data' => $alquiler]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function devolver($id): JsonResponse
    {
        try {
            $alquiler = $this->alquilerService->recibirVestido($id);
            return response()->json(['message' => 'Vestido recibido y enviado a lavandería', 'data' => $alquiler]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function actualizarEstadoMantenimiento(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'nuevo_estado' => 'required|in:EN_LAVANDERIA,EN_PLANCHADO,DISPONIBLE',
        ]);

        try {
            $vestido = $this->alquilerService->actualizarEstadoMantenimiento($id, $validated['nuevo_estado']);
            return response()->json(['message' => 'Estado de mantenimiento actualizado', 'data' => $vestido]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
