@extends('layouts.metronic.main', [
'title' => 'Actualizar alerta',
'controller' => null,
'view' => 'Actualizar alerta'
])

@section('pagetitle', 'Actualizar alerta')

@section('content')
@include('configuration._formAlerts') 
@endsection