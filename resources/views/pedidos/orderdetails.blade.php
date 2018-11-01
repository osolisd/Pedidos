@extends('layouts.metronic.main', [
'title' => 'Detalle del pedido',
'controller' => null,
'view' => 'Detalle del pedido'
])

@section('pagetitle', 'Detalle del pedido')

<?php
$data = App\Util\Helpers::getAdviser();
use App\Util\Helpers;
use App\Util\Constants;
?>
<?php
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
@if(!isset($response) || (isset($response['pedidoEncabezado']) && $response['pedidoEncabezado']['Editable']))
<form class="form-horizontal" method="POST" action="{{route('ConfirmOrder')}}" id="formOrder" autocomplete="on">
@endif
    
    @if(isset($response['pedidoEncabezado']))
        @include('pedidos._formOrders', ['response' => $response])
    @else
        @include('pedidos._historyinvoices', ['response' => $response])
    @endif
    
@if(!isset($response) || (isset($response['pedidoEncabezado']) && $response['pedidoEncabezado']['Editable']))
</form>
@endif

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
	isNew = false;  

	@if(isset($response['user']->Cedula))
		$('#cedasesoras').val('{{ $response['user']->Cedula }}');
		$('#cedasesoras').prop('readonly', true);
		//Mostramos los datos del usuario
		showUserData(<?= json_encode($response['user']) ?>);		
		//Dessabilitamos el boton buscar 
		$('#btn-searchAdviser').attr("disabled", true);
        //Eliminamos el evento click del botón
        $('#btn-searchAdviser').removeAttr('onclick');
        //MUESTRA FORMULARO PARA AGREGAR PLU
        $("#panelAgregarPD").show();
	@endif

    /**
    * VERIFICAR MONTO AL INICIO DE LA CARGA
    */
    validarMonto();
    /**
    * CARGAR PLUS
    */
    cargarPlus("{{route('Productos')}}");
    $("#newplu").select2({
        allowClear: true,
        placeholder: 'Código o descripción',
        width: '100%',
        language: {
             noResults: function(term) {
                 return "No se encontraron resultados";
            }
        },
        focus: true
    });
    
    /**
    * VERIFICO SI TIENE PAQUETE DE AHORRO
    */    
    
    console.log(routeSuscripcionesPaquetes);
    cargarSuscripciones(routeSuscripcionesPaquetes,getCurrentCampaign);
    
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