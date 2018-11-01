@extends('layouts.metronic.main', [
'title' => 'Crear Alerta',
'controller' => null,
'view' => 'Crear alerta'
])

@section('pagetitle', 'Crear alerta')

@section('content')
@include('configuration._formAlerts') 
@endsection