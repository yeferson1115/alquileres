@extends('layouts.app')
@section('content')
<div class="content-header row mt-5"><div class="col-12"><h2 class="content-header-title float-start mb-0">Nuevo Cliente</h2></div></div>
<div class="content-body"><section><div class="row"><div class="col-lg-8"><div class="card"><div class="card-header"><h4 class="card-title">Información del cliente</h4></div><div class="card-body"><form method="POST" action="{{ route('clientes.store') }}">@csrf @include('admin.clientes.partials.form')<div class="mt-3"><button class="btn btn-primary"><i class="fa-solid fa-save"></i> Guardar cliente</button><a class="btn btn-outline-secondary" href="{{ route('clientes.index') }}">Cancelar</a></div></form></div></div></div></div></section></div>
@endsection
