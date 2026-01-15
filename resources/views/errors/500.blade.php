@extends('errors.layout')

@section('title', 'Error del Servidor')

@section('content')
    <h1 class="error-code" style="color: var(--color-rosa);">500</h1>
    <h2 class="error-title">Algo salió mal de nuestro lado</h2>
    <p class="error-message">
        Hubo un error interno en el servidor. Estamos trabajando para solucionarlo. 
        Por favor, intentá recargar la página en unos momentos.
    </p>
@endsection
