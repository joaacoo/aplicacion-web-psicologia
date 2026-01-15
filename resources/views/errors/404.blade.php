@extends('errors.layout')

@section('title', 'Página no encontrada')

@section('content')
    <h1 class="error-code">404</h1>
    <h2 class="error-title">¡Ups! Parece que te perdiste</h2>
    <p class="error-message">
        La página que estás buscando no existe o fue movida a otra ubicación. 
        Pero no te preocupes, siempre podés volver al camino principal.
    </p>
@endsection
