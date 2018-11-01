@extends('layouts.metronic.main', [
'title' => 'Datos generales',
'controller' => null,
'view' => 'Datos generales'
])

@section('pagetitle', 'Datos generales')

@section('content')

<?php

use App\Util\Helpers;
use App\Util\Constants;
?>

@section('cssfiles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ URL::asset('metronic/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('metronic/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('metronic/custom-pedidos-web/css/dt-loading.css') }}" rel="stylesheet" type="text/css" />
<link href="http://fontawesome.io/assets/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
<link href="https://www.electrictoolbox.com/examples/facebox/facebox.css" rel="stylesheet" type="text/css" />  

<style type="text/css">
    .popover-title {
        color: #333 !important;
    }
</style>

<!-- END PAGE LEVEL PLUGINS -->
@endsection
   
@if(Helpers::validEmptyVar($response))
<div class="row">
    <div class="col-md-12">        
        <!-- BEGIN PROFILE CONTENT -->
        <div class="profile-content">
            <div class="row">
            
            	@if(Helpers::isAdviser() || Helpers::isEmployee())
            
                    <div class="col-md-12">
                        <div class="row widget-row">
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                <div class="widget-thumb margin-bottom-20">
                                    <div class="widget-thumb-wrap">
                                        <div class="widget-thumb-body">
                                            <?php
                                            $CupoCatalogo = (Helpers::validEmptyVar($response['cupos']->CupoCatalogo)) ?
                                                    $response['cupos']->CupoCatalogo : "-"
                                            ?>
                                            <span class="widget-thumb-body-stat font-marca" 
                                                  data-counter="counterup" data-value="{{ $CupoCatalogo }}">
                                                ${{ $CupoCatalogo }}
                                            </span>
                                        </div>
                                    </div>
                                    <h4 class="widget-thumb-heading">Cupo valor cat&aacute;logo</h4>
                                </div>  
                            </div>
                            @if(Helpers::isAdviser())
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                <div class="widget-thumb margin-bottom-20">
                                    <div class="widget-thumb-wrap">
                                        <div class="widget-thumb-body">
                                            <?php
                                            if (Helpers::validEmptyVar($response['saldos']['SaldoPagar']) &&
                                                    $response['saldos']['SaldoPagar'] != '0') {
                                                $saldo = $response['saldos']['SaldoPagar'];
                                                $color = 'font-red-haze';
                                                $texto = 'Saldo a pagar actual';
                                            } elseif (Helpers::validEmptyVar($response['saldos']['SaldoFavor']) &&
                                                    $response['saldos']['SaldoFavor'] != '0') {
                                                $saldo = $response['saldos']['SaldoFavor'];
                                                $color = 'font-green-jungle';
                                                $texto = 'Saldo a favor actual';
                                            } else {
                                                $saldo = 0;
                                                $color = 'font-green-jungle';
                                                $texto = 'Saldo a pagar actual';
                                            }
                                            ?>
                                            <span class="widget-thumb-body-stat font-marca" 
                                                  data-counter="counterup" data-value="{{ $saldo }}">
                                                ${{ $saldo }}
                                            </span>
                                        </div>
                                    </div>
                                    <h4 class="widget-thumb-heading">{{$texto}}</h4>
                                </div>  
                            </div>
                            @endif
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 list-separated profile-stat" id="blockPoints">
                                <div id="loadingPuntos" style="display: none">
                                    <span>
                                        <img src="{{ URL::asset('metronic/custom-pedidos-web/img/preloader.gif') }}" />
                                    </span>
                                </div>                            
                                <div class="col-md-4 col-sm-4 col-xs-4">
                                    <div class="uppercase font-mini text-center">Puntos</div>
                                    <div class="uppercase profile-stat-title font-marca" id="_PuntosCampanaActual"></div>
                                    <div class="uppercase profile-stat-text" id="_CampanaActual"></div>
                                </div>
                                <div class="col-md-4 col-sm-4 col-xs-4">
                                     <div class="uppercase font-mini text-center">Puntos</div>
                                    <div class="uppercase profile-stat-title font-marca" id="_PuntosCampanaAnterior"></div>
                                    <div class="uppercase profile-stat-text" id="_CampanaAnterior"></div>
                                </div>
                                <div class="col-md-43 col-sm-4 col-xs-4">
                                     <div class="uppercase font-mini text-center">Puntos</div>
                                    <div class="uppercase profile-stat-title font-marca" id="_PuntosCampanaTrasanterior"></div>
                                    <div class="uppercase profile-stat-text" id="_CampanaTrasanterior"></div>
                                </div>
                            </div>                        
                        </div>                    
                    </div>
                    
                @endif
                                
                <div class="col-md-12">
                    <!-- INICIO ULTIMOS 5 PEDIDOS -->
                    @if(Helpers::isAdviser() || Helpers::isEmployee())
                    	@include('pedidos._listOrders', ['all' => false])   
                	@else
                		@include('pedidos._listOrdersAdministrator', ['all' => false])   
                	@endif                 
                    <!-- FIN ULTIMOS 5 PEDIDOS -->
                </div>                
                <div class="col-md-12" id="blockDates">                    
                    <div class="portlet">
                        <div class="portlet-title">
                            <div class="caption caption-md">
                                <i class="icon-bar-chart theme-font hide"></i>
                                <span class="caption-subject font-blue-madison bold uppercase font-marca">Fechas claves</span>
                            </div>
                        </div>
                        <div id="loadingDates" style="display: none">
                            <span>
                                <img src="{{ URL::asset('metronic/custom-pedidos-web/img/preloader.gif') }}" />
                            </span>
                        </div>
                        <div class="portlet-body fechasImportantesHome">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="simpleDateConf" style="display: none">
                                        <div class="mt-element-ribbon">
                                            <div class="ribbon ribbon-shadow ribbon-color-default uppercase">
                                                <span class="photo">                               
                                                    <div class="uppercase profile-stat-title font-mini">
                                                        Sin fecha
                                                    </div>                                                
                                                </span>
                                            </div>
                                            <div class="ribbon-content" style="clear: initial">
                                                <span class="subject">
                                                    <h5 class="from bold dark" style="margin-left: 20px"> Fecha y lugar de conferencia </h5>
                                                </span>
                                                <span class="message font-dark"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="FeConfCarousel" 
                                         class="" 
                                         data-interval="5000" 
                                         data-ride="carousel">            
                                        <ol class="carousel-indicators"></ol>
                                        <div class="carousel-inner">
                                        	<div class="mt-element-ribbon" id="KeyDateContent">
                                            	  
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="simpleDateCamb" style="display: none">
                                        <div class="mt-element-ribbon">
                                            <div class="ribbon ribbon-shadow ribbon-color-default uppercase">
                                                <span class="photo">                               
                                                    <div class="uppercase profile-stat-title font-mini">
                                                        Sin fecha
                                                    </div>                                                
                                                </span>
                                            </div>
                                            <div class="ribbon-content" style="clear: initial">
                                                <span class="subject">
                                                    <h5 class="from bold dark" style="margin-left: 20px"> Fecha y lugar de cambios y devoluciones </h5>
                                                </span>
                                                <span class="message font-dark"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="FeCyDCarousel" 
                                         class="" 
                                         data-interval="5000" 
                                         data-ride="carousel">
                                        <ol class="carousel-indicators"></ol>
                                        <div class="carousel-inner">
                                        	<div class="mt-element-ribbon" id="KeyDateContent">
                                            	  
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mt-element-ribbon">
                                        <div class="ribbon ribbon-shadow ribbon-color-default uppercase">
                                            <span class="photo">                                                
                                                <div class="uppercase profile-stat-text text-center font-mini" id="_FechaEntregaPedidoMes"></div>
                                                <div class="uppercase profile-stat-title text-center bold" id="_FechaEntregaPedidoDia"></div>
                                                <div class="uppercase profile-stat-text text-center font-mini" id="_FechaEntregaPedidoAnio"></div>                                                
                                                <div class="uppercase profile-stat-title font-mini"  id="_FechaEntregaPedidoNA">
                                                    Sin fecha
                                                </div>                                                
                                            </span>
                                        </div>
                                        <div class="ribbon-content from-only" style="clear: initial">
                                            <span class="subject">
                                                <h5 class="from dark bold"> Fecha estimada de entrega de pedido </h5>
                                            </span>
                                            <span class="message font-dark"> &nbsp;  </span>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mt-element-ribbon">
                                        <div class="ribbon ribbon-shadow ribbon-color-default uppercase">
                                            <span class="photo">
                                                <div class="uppercase profile-stat-text text-center font-mini" id="_FechaPagoPedidoMes"></div>
                                                <div class="uppercase profile-stat-title text-center bold" id="_FechaPagoPedidoDia"></div>
                                                <div class="uppercase profile-stat-text text-center font-mini" id="_FechaPagoPedidoAnio"></div>                                                
                                                <div class="uppercase profile-stat-title font-mini" id="_FechaPagoPedidoNA">
                                                    Sin fecha
                                                </div>
                                            </span>
                                        </div>
                                        <div class="ribbon-content from-only" style="clear: initial">
                                            <span class="subject">
                                                <h5 class="from dark bold"> Fecha l&iacute;mite de pago </h5>
                                            </span>
                                            <span class="message"> &nbsp; </span>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mt-element-ribbon">
                                        <div class="ribbon ribbon-shadow ribbon-color-default uppercase">
                                            <span class="photo">
                                                <div class="uppercase profile-stat-text text-center font-mini" id="_FerchaLimitePedidoMes"></div>
                                                <div class="uppercase profile-stat-title text-center bold" id="_FerchaLimitePedidoDia"></div>
                                                <div class="uppercase profile-stat-text text-center font-mini" id="_FerchaLimitePedidoAnio"></div>
                                                <div class="uppercase profile-stat-title font-mini" id="_FerchaLimitePedidoNA">
                                                    Sin fecha
                                                </div>
                                            </span>
                                        </div>
                                        <div class="ribbon-content from-only" style="clear: initial">
                                            <span class="subject">
                                                <h5 class="from dark bold"> Fecha l&iacute;mite de ingreso de pedido </h5>
                                            </span>
                                            <span class="message"> &nbsp; </span>
                                        </div>

                                    </div>
                                </div>
                            </div>                        
                        </div>
                    </div>
                </div>
            </div>            
        </div>
        <!-- END PROFILE CONTENT -->
    </div>
</div>

<?php
//VERIFICO SI HAY ALERTAS PRINCIPALES
$alerts = (new \App\Http\Controllers\Pedidos\AlertController())->findMainAlert(Constants::ALERT_DASHBOARD);
$isAlert = false;
if (!$alerts['error'] && Helpers::validEmptyVar($alerts['alert'])) :
    $isAlert = true;  
?>
    @include('pedidos._alerts', ['detalle' => $alerts['alert']->Detalle])
<?php endif; ?>


@endif
@endsection

@section('scripts')
jQuery(document).ready(function () {
    <?php if($isAlert) : ?>
        //VALIDO LA COOKIE PARA MOSTRAR LAS ALERTAS
        if(getCookie('<?= Constants::DASHBOARD_ALERT_COOKIE . auth()->user()->brand ?>') === ''){
            setCookieInfo('<?= Constants::DASHBOARD_ALERT_COOKIE . auth()->user()->brand ?>');
            $("#alertasPpales").show();   
        } else {     
            $("#alertasPpales").hide();
        }
    <?php endif; ?> 
    
    $('#statusHelp,#statusHelpHistory').popover({
        trigger: 'hover',
        title : 'Ayuda',
        content: function() {
            return $('#popover_content_wrapper').html();
        },
        placement : 'bottom',
        html : true
    });
    
    //TODO: variables de Auth
    var route = "{{ route('FindOrders', ['quantity' => 5])}}";
    var routeHistory = "{{ route('PedidosCurso', ['quantity' => 5]) }}";
    var urlDetail = "{{ url('findOrderById')}}";    
    var urlDelete = "{{ url('deleteOrder')}}";
    var show = false;
    @if(Helpers::isAdviser() || Helpers::isEmployee())
    	listOrders(route, urlDetail, urlDelete);
	@else
	
		var orderDetailTableRoute = "{!! $response['orderDetailRoute'] !!}"; 
		var currentOrdersRoute = "{!! $response["currentOrderRoute"] !!}";
		var historyOrdersRoute = "{!! $response['historyOrderRoute'] !!}";
	
		loadCourseOrdersTable(currentOrdersRoute, 'tblListOrders', orderDetailTableRoute, urlDetail, urlDelete, false, 5, null);
		
		$('.btn-historyorders').on('click', function () {
        	$('.capaingcourse').hide();
        	$('.historyorders').show();
        	$('#tblListOrders').hide();
        	$('#tblListOrdersHistory').show();  
        	if(!show) {
        		$('.dt-loader').show(); 
        		loadHistoryOrdersTable(historyOrdersRoute, 'tblListOrdersHistory', urlDetail, false, 5, null);
        		show = true;
    		}
        });
	@endif

    /**
    * CARGAR PUNTOS
    */
    <?php if (App\Util\Helpers::isAdviser()): ?>
        findHistoryPoints('{{route('Puntos')}}');
    <?php else: ?>
        $("#blockPoints").hide();
    <?php endif; ?>
    /**
    * CARGAR FECHAS
    */
    <?php if (!App\Util\Helpers::isAdministrator() && !App\Util\Helpers::isEmtelco()): ?>
        findKeyDates('{{route('Fechas')}}', '{{ route('Notificaciones')}}');
    <?php else: ?>
        $("#blockDates").hide();
    <?php endif; ?>   
});
@endsection

@section('jsfiles')
<script src="{{ URL::asset('metronic/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/datatables/jquery.dataTables.odata.js') }}" type="text/javascript"></script>
<script src="http://opensource.teamdf.com/number/jquery.number.js" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/faceboox/src/facebox-bootstrap.js') }}" type="text/javascript"></script>
@endsection