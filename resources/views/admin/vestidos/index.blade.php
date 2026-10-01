@extends('layouts.app')

@section('title', 'Inventario de Vestidos')
@section('page_title', 'Inventario de Vestidos')

@section('content')
<div class="content-header row mt-5">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-start mb-0">Inventario de Vestidos</h2>
            </div>
        </div>
    </div>
    <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
        <div class="mb-1 breadcrumb-right">
            <div class="dropdown">               
                <a href="{{ route('vestidos.create') }}" class="btn btn-success waves-effect waves-float waves-light">
                    <i class="fa-solid fa-plus"></i> Nuevo Vestido
                </a>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Vestidos en Almacén</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="datatables">
                                <thead class="table-light">
                                    <tr>
                                        <th>Código</th>
                                        <th>Descripción</th>
                                        <th>Talla/Color</th>
                                        <th>Estado</th>
                                        <th>Acciones Mantenimiento</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($vestidos as $vestido)
                                        <tr class="odd row{{ $vestido->id }}">
                                            <td class="fw-bold">{{ $vestido->codigo }}</td>
                                            <td>{{ $vestido->descripcion }}</td>
                                            <td>{{ $vestido->talla }} / {{ $vestido->color }}</td>
                                            <td>
                                                <span class="badge {{ $vestido->estado == 'DISPONIBLE' ? 'bg-success' : ($vestido->estado == 'RESERVADO' ? 'bg-warning' : ($vestido->estado == 'EN_ALQUILER' ? 'bg-primary' : 'bg-secondary')) }}">
                                                    {{ $vestido->estado }}
                                                </span>
                                            </td>
                                            <td>
                                                <select onchange="cambiarEstado('{{ $vestido->id }}', this.value)" class="form-select form-select-sm w-auto">
                                                    <option value="">Actualizar...</option>
                                                    <option value="EN_LAVANDERIA">Enviar a Lavandería</option>
                                                    <option value="EN_PLANCHADO">Enviar a Planchado</option>
                                                    <option value="DISPONIBLE">Marcar Disponible</option>
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    function cambiarEstado(id, nuevoEstado) {
        if(!nuevoEstado) return;
        $.ajax({
            url: `/admin/vestidos/${id}/estado`,
            type: 'PATCH',
            data: { nuevo_estado: nuevoEstado },
            success: function(res) {
                _alertGeneric('success', 'Éxito', res.message, 1);
            },
            error: function(xhr) {
                _alertGeneric('error', 'Error', xhr.responseJSON.error);
            }
        });
    }
</script>
@endpush
