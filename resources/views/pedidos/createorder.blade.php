<?php

use App\Util\Helpers;
use App\Util\Constants;

?>
@extends('layouts.metronic.main', [
'title' => 'Nuevo pedido',
'controller' => null,
'view' => 'Nuevo pedido'
])

@section('pagetitle', 'Nuevo pedido')

<?php 
$data = App\Util\Helpers::getAdviser(); 
$dataAmmount = App\Util\Helpers::getAmmount();
$MontoMin = (App\Util\Helpers::validEmptyVar($dataAmmount)) ? (int) trim($dataAmmount->MontoMin) : 0;
$MontoMax = (App\Util\Helpers::validEmptyVar($dataAmmount)) ? (int) trim($dataAmmount->MontoMax) : 0;
$cantPermitida = 100; //App\Util\Helpers::getAllowQuantity();
?>

<?php
//VERIFICO SI HAY ALERTAS PRINCIPALES
$alerts = (new \App\Http\Controllers\Pedidos\AlertController())->findMainAlert(Constants::ALERT_NEW_ORDER);
$isAlert = false;
if (!$alerts['error'] && Helpers::validEmptyVar($alerts['alert'])) :
    $isAlert = true;
?>
    @include('pedidos._alerts', ['detalle' => $alerts['alert']->Detalle])
<?php endif; ?>
    
@section('content')
<form class="form-horizontal" method="POST" action="{{route('ConfirmOrder')}}" id="formOrder" autocomplete="on">
    @include('pedidos._formOrders')    
    
    <div id="popover_content_wrapper" style="display: none">
    <p class="font-mini">
        <span class="label label-sm font-dark">
            Enviado
        </span>
        &nbsp;El pedido está creado y aún está abierto para modificaciones, pero cuando llegue a la hora límite de ingreso de pedido será tomado para ser facturado e iniciar proceso de despacho..
    </p>
    <p class="font-mini">
        <span class="label label-sm font-dark">
            Guardado
        </span>
        &nbsp;El pedido est&aacute; creado y aún está abierto para modificaciones.
    </p>
    <p class="font-mini">
        <span class="label font-marca">
            Facturado
        </span>
        &nbsp;Cuando ya existe una factura asociada al pedido.
    </p>
</div>
</form>
@endsection

@section('scripts')
var routeCheckProduct = "{{ url('checkProduct')}}";
var min = parseInt({{ $MontoMin }});
var max = parseInt({{ $MontoMax }});
var cantPermitida = parseInt({{ $cantPermitida }});
var SaveOrder = "{{route('SaveOrder')}}";
var routeValidAsesora = "{{route('NuevoPedidoAsesora')}}";
var routeProductos = "{{route('Productos')}}";
var routeSuscripcionesPaquetes = "{{route('SuscripcionesPaquetes')}}";
var routeEditarSuscripcion = "{{route('EditarSuscripcion')}}";
var getCurrentCampaign = "{{Helpers::getCurrentCampaign()}}";

var showMessageAddproduct = false;



$(document).ready(function () { 
	var isNew = true;    

    <?php if(Helpers::isAdviser() || Helpers::isEmployee()) : ?>
    /**
    * VERIFICAR MONTO AL INICIO DE LA CARGA
    */
    validarMonto();
    /**
    * CARGAR PLUS
    */
    cargarPlus(routeProductos, null);
    $("#newplu").select2({
        allowClear: true,
        placeholder: 'Código o descripción',
        width: '100%',
        language: {
             noResults: function(term) {
                 return "No se encontraron resultados";
            }
        }
    });
    /**
    * VERIFICO SI TIENE PAQUETE DE AHORRO
    */    
    cargarSuscripciones(routeSuscripcionesPaquetes,getCurrentCampaign);
    <?php endif; ?>
    <?php if (Helpers::isAdministrator() || Helpers::isDirector() || Helpers::isEmtelco()): ?>    
    /*cargarUsuarios("{{route('Usuarios', ['ajax' => 'true'])}}");
    $("#cedasesoras").select2({
        allowClear: true,
        placeholder: 'Asesora',
        width: '100%',
        language: {
             noResults: function(term) {
                 return "La asesora no existe, por favor verifica los datos.";
            }
        }
    });  */
    <?php endif; ?>
    
    <?php if($isAlert) : ?>
        //VALIDO LA COOKIE PARA MOSTRAR LAS ALERTAS
        if(getCookie('<?= Constants::NEW_ORDER_ALERT_COOKIE . auth()->user()->brand  ?>') === ''){
            setCookieInfo('<?= Constants::NEW_ORDER_ALERT_COOKIE . auth()->user()->brand ?>');
            $("#alertasPpales").show();   
        } else {     
            $("#alertasPpales").hide();
        }
    <?php endif; ?>  
});
@endsection