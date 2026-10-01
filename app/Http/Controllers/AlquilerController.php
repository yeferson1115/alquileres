<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AlquilerService;
use App\Models\Vestido;
use App\Models\Cliente;
use App\Models\Alquiler;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

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

    public function storeCliente(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        Cliente::create($validated);
        return redirect()->route('clientes.index')->with('success', 'Cliente creado exitosamente.');
    }

    public function editCliente(Cliente $cliente): View
    {
        return view('admin.clientes.edit', compact('cliente'));
    }

    public function updateCliente(Request $request, Cliente $cliente): RedirectResponse
    {
        $cliente->update($request->validate([
            'nombre' => 'required|string|max:255', 'telefono' => 'required|string|max:20', 'email' => 'nullable|email|max:255',
        ]));

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado exitosamente.');
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

    public function storeVestido(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => 'required|string|unique:vestidos,codigo',
            'descripcion' => 'required|string',
            'talla' => 'required|string',
            'color' => 'required|string',
            'estado' => 'required|in:DISPONIBLE',
        ]);

        Vestido::create($validated);
        return redirect()->route('vestidos.index')->with('success', 'Vestido agregado al inventario.');
    }

    public function editVestido(Vestido $vestido): View
    {
        return view('admin.vestidos.edit', compact('vestido'));
    }

    public function updateVestido(Request $request, Vestido $vestido): RedirectResponse
    {
        $vestido->update($request->validate([
            'codigo' => 'required|string|unique:vestidos,codigo,' . $vestido->id,
            'descripcion' => 'required|string', 'talla' => 'required|string|max:50', 'color' => 'required|string|max:50',
        ]));

        return redirect()->route('vestidos.index')->with('success', 'Información del vestido actualizada.');
    }

    // --- CICLO DE ALQUILER ---
    public function dashboard(): View
    {
        $this->alquilerService->marcarAlquileresRetrasados();
        $alertas = $this->alquilerService->obtenerAlertasDevolucion();
        $alquileresActivos = Alquiler::with(['cliente', 'vestido'])
            ->whereIn('estado_alquiler', ['RESERVADO', 'EN_ALQUILER', 'RETRASADO'])
            ->latest()
            ->get();
        $stats = [
            'disponibles' => Vestido::where('estado', 'DISPONIBLE')->count(),
            'alquilados' => Vestido::where('estado', 'EN_ALQUILER')->count(),
            'mantenimiento' => Vestido::whereIn('estado', ['EN_LAVANDERIA', 'EN_PLANCHADO'])->count(),
        ];
        return view('admin.alquileres.dashboard', compact('alertas', 'alquileresActivos', 'stats'));
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
            'metodo_pago' => 'nullable|string|max:50',
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
            'metodo_pago' => 'nullable|string|max:50',
        ]);

        try {
            $alquiler = $this->alquilerService->despacharVestido($id, $validated['pago_final'], $validated['dias_prestamo'], $validated['metodo_pago'] ?? 'EFECTIVO');
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
