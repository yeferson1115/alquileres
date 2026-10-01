@extends('layouts.app')
@section('content')
<div class="content-header row mt-5"><div class="col-12"><h2 class="content-header-title float-start mb-0">Editar Vestido</h2></div></div>
<div class="content-body"><section><div class="row"><div class="col-lg-8"><div class="card"><div class="card-header"><h4 class="card-title">{{ $vestido->codigo }}</h4></div><div class="card-body"><form method="POST" action="{{ route('vestidos.update', $vestido) }}">@csrf @method('PUT') @include('admin.vestidos.partials.form')<div class="mt-3"><button class="btn btn-primary"><i class="fa-solid fa-save"></i> Guardar cambios</button><a href="{{ route('vestidos.index') }}" class="btn btn-outline-secondary">Cancelar</a></div></form></div></div></div></div></section></div>
@endsection
