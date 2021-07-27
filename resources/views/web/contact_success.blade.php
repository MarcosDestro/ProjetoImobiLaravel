@extends('web.master.master')

@section('content')
    <div class="container p-5">
        <h2 class="text-center text-front">Seu email foi enviado com sucesso! Embre entraremos em contato.</h2>    
        <p class="text-center"><a href="{{ url()->previous() }}" class="text-front">... Continuar navegando!</a></p>
    </div>
@endsection