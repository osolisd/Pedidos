@extends('layouts.metronic.main', [
'title' => 'Hist&oacute;rico de pedidos',
'controller' => null,
'view' => 'Hist&oacute;rico de pedidos'
])

@section('pagetitle', 'Hist&oacute;rico de pedidos')

@section('cssfiles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ URL::asset('metronic/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('metronic/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="portlet" id="porletHistoryOrders">
            <div class="portlet-title">
                <div class="caption caption-md">
                    <i class="icon-bar-chart theme-font hide"></i>
                    <span class="caption-subject font-blue-madison bold uppercase"></span>
                </div>
                <div class="actions">
                    <a class="btn btn-transparent font-white btn-circle btn-sm btnnewped" href="{{ route('NuevoPedido') }}">
                        <i class="fa fa-plus"></i> Nuevo pedido
                    </a>        
                </div>                
            </div>
            <div class="portlet-body">                
                <div>
                    <table class="table table-hover table-light table-checkable order-column table-condensed" id="tableOrders">
                        <thead>
                            <tr>
                                <!-- TODO: MOSTRAR PARA ADMINISTRADOR -->
                                <!-- <th>
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="group-checkable" data-set="#sample_1_2 .checkboxes" />
                                        <span></span>
                                    </label>
                                </th>-->
                                <th> &nbsp;&nbsp;PEDIDO </th>
                                <th> CAMPA&Ntilde;A </th>
                                @if(App\Util\Helpers::isAdviser() || App\Util\Helpers::isEmployee())
                                	<th> FECHA PEDIDO</th>
                                @else
                                	<th> ASESORA</th>
                            	@endif	
                                <th> ZONA </th>
                                <th> ESTADO  <i class="fa fa-info-circle" id="statusHelp" data-container='body'></i></th>
                                <th> ACCIONES </th>
                            </tr>
                        </thead>
                        <tbody>  
                            @foreach($response['orders'] as $order)
                            <tr class="odd gradeX">
                                <!-- TODO: MOSTRAR PARA ADMINISTRADOR -->
                                <!-- <td>
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" value="1" />
                                        <span></span>
                                    </label>
                                </td>-->
                                <td class="details-control" style="cursor: pointer;"
                    			    id="td_{{ $order->PedidoId }}"
                			        data-max="{{ $order->CupoMax }}"
                    			    data-min="{{ $order->MontoMin }}"
                    			    data-name="{{ $order->NombreAsesora }}"
                    			    data-document="{{ $order->NumeroDcto }}"
                    			    data-zone="{{ $order->CodigoZona }}"
                    			    data-date="{{ $order->Fecha.substr(0, 10) }}"
                    			    data-campaing="{{ $order->CampanaId }}" 
                    			    data-division="{{ $order->Division }}"
                    			    data-mailplan="{{ $order->MailPlan }}"
                			        data-total="{{ $order->SaldoPagar }}"> 
                			        	{{$order->PedidoId}} 
        			        	</td>
                                <td>
                                    {{$order->CampanaId}}
                                </td>
                                <td>
                                 @if(App\Util\Helpers::isAdviser() || App\Util\Helpers::isEmployee())
                                    @if(App\Util\Helpers::validEmptyVar($order->Fecha))
                                    <?php
                                    $expFecha = explode("T", $order->Fecha);
                                    ?>
                                    {{$expFecha[0] . ' ' . $expFecha[1]}}
                                    @endif
                                @else
                                	{{ $order->NombreAsesora }}
                                @endif
                                </td>
                                <td class="center"> {{$order->CodigoZona}} </td>
                                <td class="center"> 
                                    <?php $status = App\Util\Helpers::statusOrder($order->Procesado); ?>
                                    <span class="label {{ $status['bg'] }}">
                                        {{ $status['strStatus'] }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ url('findOrderById') .'/'. $order->PedidoId.'/'.$order->Procesado.'/'.$order->NumeroDcto }}" class="btn btn-outline btn-xs bgmarca font-white btn-circle">
                                        <i class="fa fa-eye"></i> Ver
                                    </a>
                                    @if($order->Editable)
                                    <a href="{{ route('DeleteOrder', ['id' => $order->PedidoId]) }}" 
                                       class="font-marca" 
                                       data-toggle="confirmation"
                                       data-original-title="¿Estás segura que quieres borrar el pedido?"
                                       title=""
                                       data-btn-ok-label="Si"
                                       data-btn-cancel-label="No">
                                        <i class="fa fa-trash-o"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

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
@endsection

@section('scripts')
jQuery(document).ready(function () {
$('#statusHelp').popover({
trigger: 'hover',
title : 'Ayuda',
content: function() {
return $('#popover_content_wrapper').html();
},
placement : 'bottom',
html : true
});
/**
* DATATABLE DE ORDENES
*/
var dt = $('#tableOrders').DataTable({
"language": {
"url": "metronic/custom-pedidos-web/scripts/Spanish.json"
},
"iDisplayLength": 20,
"aLengthMenu": [[20, 30, 50, 100, -1], [20, 30, 50, 100, "Todos"]],
"order": [[ 2, "desc" ]]
});

// Array to track the ids of the details displayed rows
var detailRows = [];

$('#tableOrders tbody').on( 'click', 'tr td.details-control', function () {
    var tr = $(this).closest('tr');
    console.log(dt);
    var row = dt.row( tr );
    var idx = $.inArray( tr.attr('id'), detailRows );

    if ( row.child.isShown() ) {
        tr.removeClass( 'details' );
        row.child.hide();

        // Remove from the 'open' array
        detailRows.splice( idx, 1 );
    }
    else {
        tr.addClass( 'details' );
        row.child( format( row.data() ) ).show();

        // Add to the 'open' array
        if ( idx === -1 ) {
            detailRows.push( tr.attr('id') );
        }
    }
} );

// On each draw, loop over the `detailRows` array and show any child rows
dt.on( 'draw', function () {
    $.each( detailRows, function ( i, id ) {
        $('#'+id+' td.details-control').trigger( 'click' );
    } );
} );



});
@endsection

@section('jsfiles')
<script src="{{ URL::asset('metronic/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
@endsection