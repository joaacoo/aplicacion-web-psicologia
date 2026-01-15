@extends('errors.layout')

@section('title', 'Acceso Denegado')

@section('content')
    <h1 class="error-code" style="color: var(--color-lila);">403</h1>
    <h2 class="error-title">Acceso restringido</h2>
    <p class="error-message">
        No tenés los permisos necesarios para ver esta sección. 
        Si creés que esto es un error, por favor contactanos.
    </p>
@endsection
