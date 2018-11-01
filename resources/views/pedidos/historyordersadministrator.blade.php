@extends('layouts.metronic.main', [
    'title' => $response['title'],
    'controller' => null,
    'view' => $response['title']
])
<?php
    use App\Util\Helpers;
?>

@section('pagetitle', $response['title'])

@section('cssfiles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ URL::asset('metronic/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('metronic/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('metronic/custom-pedidos-web/css/dt-loading.css') }}" rel="stylesheet" type="text/css" />
<link href="http://fontawesome.io/assets/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .popover-title {
        color: #333 !important;
    }
</style>

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
                
                
					@if(Helpers::isAdministrator())                
                    	@if((isset($response['current']) && $response['current'] == false))
                    		<a class="btn btn-transparent bgmarca font-white btn-sm btn-circle btn-courseorders" href="{{ route('ExportHistoryOrders') }}">
                                <i class="fa fa-cloud-download"></i> Exportar
                            </a> 
                        @else    
                            <a class="btn btn-transparent bgmarca font-white btn-sm btn-circle btn-courseorders" data-toggle="modal" href="#small">
                                <i class="fa fa-cloud-download"></i> Exportar
                            </a> 
                    	@endif
                	@endif
                	
                	@if(Helpers::isDirector()) 
                		<a class="btn btn-transparent bgmarca font-white btn-sm btn-circle btn-courseorders" href="{{ (isset($response['current']) && $response['current'] == true) ? route('ExportCurrentOrders') : route('ExportHistoryOrders') }}">
                            <i class="fa fa-cloud-download"></i> Exportar
                        </a>
                	@endif
                	
                    <a class="btn btn-transparent font-white btn-circle btn-sm btnnewped" href="{{ route('NuevoPedido') }}">
                        <i class="fa fa-plus"></i> Nuevo pedido
                    </a>        
                </div>                
            </div>
            <div class="portlet-body">          
                <div>
                	<div id="filterAdvancedGroup" class="row margin-bottom-20 well hide" style="margin-left: 0px !important; margin-right: 0px !important;">
                		<form autocomplete="off" id="formAdvancedFilter" name="formAdvancedFilter" method="POST" action="{{ (isset($response['current']) && $response['current'] == true) ? route('PedidosCurso', ['history' => 0]) : route('HistoricoPedidos', ['history' => 1]) }}">
                    		{{ csrf_field() }}
                    		
                    		@if(Helpers::isDirector() && isset($response['current']) && $response['current'])
                    		
                        		<div class="col-md-4">
                        			<h5>Saldo</h5>
                                	<div class="input-group">
                                		<span class="input-group-addon"><i class="fa fa-filter"></i></span>
                                		<select class="form-control" name="filterBalance">
                                			@if(isset($response['filterBalance']))
                                				@if($response['filterBalance'] == 1)
                                					<option value="1">MAYOR DE CERO</option>
                                					<option value="">--</option>
                                					<option value="2">MENOR / IGUAL DE CERO</option>
                                				@else	
                                					<option value="2">MENOR / IGUAL DE CERO</option>
                                					<option value="">--</option>
                                					<option value="1">MAYOR DE CERO</option>
                                				@endif
                                			@else 
                                				<option value="">--</option>
                                				<option value="1">MAYOR DE CERO</option>   
                                				<option value="2">MENOR / IGUAL DE CERO</option>
                                			@endif
                                        </select>
                                	</div>
                                </div>
                                
                                <div class="col-md-4">
                                	<h5>Monto</h5>
                                	<div class="input-group">
                                		<span class="input-group-addon"><i class="fa fa-filter"></i></span>
                                		<select class="form-control" name="filterAmmount">
                                			@if(isset($response['filterAmmount']))
                                				@if($response['filterAmmount'] == 1)
                                					<option value="1">CUMPLE MONTO MÍNIMO</option>
                                					<option value="">--</option>
                                					<option value="2">NO CUMPLE MONTO MINÍMO</option>
                                					<option value="3">CUMPLE MONTO MAXÍMO</option>
                                					<option value="4">NO CUMPLE MONTO MAXÍMO</option>
                                				@elseif($response['filterAmmount'] == 2)	
                                					<option value="2">NO CUMPLE MONTO MINÍMO</option>
                                					<option value="">--</option>
                                					<option value="3">CUMPLE MONTO MAXÍMO</option>
                                					<option value="4">NO CUMPLE MONTO MAXÍMO</option>
                                					<option value="1">CUMPLE MONTO MÍNIMO</option>
                                				@elseif($response['filterAmmount'] == 3)
                                					<option value="3">CUMPLE MONTO MAXÍMO</option>
                                					<option value="">--</option>
                                					<option value="4">NO CUMPLE MONTO MAXÍMO</option>
                                					<option value="1">CUMPLE MONTO MÍNIMO</option>
                                					<option value="2">NO CUMPLE MONTO MINÍMO</option>
                                				@else
                                					<option value="4">NO CUMPLE MONTO MAXÍMO</option>
                                					<option value="">--</option>
                                					<option value="1">CUMPLE MONTO MÍNIMO</option>
                                					<option value="2">NO CUMPLE MONTO MINÍMO</option>
                                					<option value="3">CUMPLE MONTO MAXÍMO</option>
                                				@endif
                                			@else 
                                				<option value="">--</option>
                                        		<option value="1">CUMPLE MONTO MÍNIMO</option>
                            					<option value="2">NO CUMPLE MONTO MINÍMO</option>
                            					<option value="3">CUMPLE MONTO MAXÍMO</option>
                            					<option value="4">NO CUMPLE MONTO MAXÍMO</option>
                                			@endif
                                        </select>
                                	</div>
                                </div>
                                
                                <div class="col-md-4">
                                	<h5>Stencil</h5>
                                	<div class="input-group">
                                		<span class="input-group-addon"><i class="fa fa-filter"></i></span>
                                		<select class="form-control" name="filterStatus">
                                			@if(isset($response['filterStatus']))
                                				<option value="{{ $response['filterStatus'] }}">{{ $response['filterStatus'] }}</option>
                                				<option value="">--</option>
                            				@else
                            					<option value="">--</option>
                                        	@endif
                                        	
                                        	@if(isset($response['stencilStatus']))
                                        		@foreach($response['stencilStatus'] as $key)
                                    				<option value="{{$key->Value}}">{{ strtoupper($key->Value) }}</option>
                                        		@endforeach
                                        	@endif
                                        </select>
                                	</div>
                                </div>
                                
                                <div class="col-md-4">
                                	<h5>Clasificación</h5>
                                	<div class="input-group">
                                		<span class="input-group-addon"><i class="fa fa-filter"></i></span>
                                		<select class="form-control" name="filterClasification">
                                        	@if(isset($response['filterClasification']))
                                				<option value="{{ $response['filterClasification'] }}">{{ $response['filterClasification'] }}</option>
                                				<option value="">--</option>
                            				@else
                            					<option value="">--</option>
                                        	@endif
                                        	@if(isset($response['clasifications']))
                                        		@foreach($response['clasifications'] as $key)
                                    				<option value="{{$key->Value}}">{{$key->Value}}</option>
                                    			@endforeach
                                    		@endif
                                        </select>
                                	</div>
                                </div>
                                
                                <div class="col-md-4">
                        			<h5>Cédula asesora</h5>
                                	<div class="input-group">
                                		<span class="input-group-addon"><i class="fa fa-filter"></i></span>
                                		<input type="text" class="form-control" placeholder="Cédula asesora" name="filterDocumentAdviser" id="filterDocumentAdviser" /> 
                            		</div>
                        		</div>
                                
                                <div class="col-md-2" style="top: 35px !important;">
                                	<div class="input-group">
                                		<button class="btn btn-transparent bgmarca font-white btn-sm btn-circle btn-courseorders" type="submit"><i class="fa fa-search"></i> Buscar</button>	
                                	</div>
                            	</div>
                        	@endif
                        	
                        	@if(Helpers::isAdministrator() && isset($response['current']) && $response['current'])
                        		<!-- Filtro Campaña en curso administrador -->
                        		<div class="col-md-3">  
                                	<h5 id="mailplan-title">Mail plan <span class="text-danger" aria-required="true"> * </span></h5>
                                	<div class="input-group">
                                		<span id="mailplan_filter" class="input-group-addon"><i class="fa fa-filter"></i></span>
                                		<select class="form-control" name="filterMailPlan" id="filterMailPlan" onchange="findAllZones('{{ route('FindAllZones', ['ajax' => true]) }}'); showErrorFilter();">  
                                			@if(isset($response['filterMailPlan']))
                                				<option value="{{ $response['filterMailPlan'] }}">{{ $response['filterMailPlan'] }}</option>
                                				<option value="">--</option>
                                			@else
                                				<option value="">--</option>
                                			@endif	
                                				
                                			@if(isset($response['mailPains']))
                                				@foreach($response['mailPains'] as $key)
                                					<option value="{{ $key }}">{{ $key }}</option>	
                                				@endforeach
                                			@endif
                                		</select>
                            		</div>
                        		</div>
                        		
                        		<div class="col-md-3">
                                	<h5>Zona</h5>
                                	<div class="input-group">
                                		<span class="input-group-addon"><i class="fa fa-filter"></i></span>
                                		<select class="form-control" name="filterZone" id="filterZone">
                                			@if(isset($response['filterZone']))
                                				<option value="{{ $response['filterZone'] }}">{{ $response['filterZone'] }}</option>
                                				<option value="">--</option>
                                			@else 
                                				<option value="">--</option>
                                			@endif
                                			
                                			@if(isset($response['zones']))
                                				@foreach($response['zones'] as $zone)
 													<option value="{{ $zone->codZona }}">{{ $zone->codZona }}</option>
                                				@endforeach
                                			@endif
                                		</select>
                            		</div>
                        		</div>
                        	
                        		<div class="col-md-3">
                                	<h5>Campaña</h5>
                                	<div class="input-group">
                                		<span class="input-group-addon"><i class="fa fa-filter"></i></span>
                                		<select class="form-control" name="filterCampaing" id="filterCampaing">
                                			@if(isset($response['filterCampaing']))
                                				<option value="{{ $response['filterCampaing'] }}">{{ $response['filterCampaing'] }}</option>
                                				<option value="">--</option>
                                			@else
                                				<option value="">--</option>
                                			@endif
                                		
                                			@if(isset($response['campaings']))
                                				@foreach($response['campaings'] as $campaing)
                                					<option value="{{ $campaing }}">{{ $campaing }}</option>	
                                				@endforeach
                                			@endif
                                		</select>
                            		</div>
                        		</div>
                        		
                        		<div class="col-md-3">
                                	<h5>Asesora</h5>
                                	<div class="input-group">
                                		<span class="input-group-addon"><i class="fa fa-filter"></i></span>
                                		<input type="text" class="form-control" value="{{ isset($response['filterAdviserDocument']) ? $response['filterAdviserDocument'] : ''}}" placeholder="Cédula Asesora" name="filterAdviserDocument">
                            		</div>
                        		</div>
                        		
                        		<div class="col-md-4">
                                	<h5>Montos</h5>
                                	<div class="input-group">
                                		<span class="input-group-addon"><i class="fa fa-filter"></i></span>
                                		<select class="form-control" name="filterAmmount">
                                			@if(isset($response['filterAmmount']))
                                				@if($response['filterAmmount'] == 1)
                                					<option value="1">CUMPLE MONTO MÍNIMO</option>
                                					<option value="">--</option>
                                					<option value="2">NO CUMPLE MONTO MINÍMO</option>
                                					<option value="3">CUMPLE MONTO MAXÍMO</option>
                                					<option value="4">NO CUMPLE MONTO MAXÍMO</option>
                                				@elseif($response['filterAmmount'] == 2)	
                                					<option value="2">NO CUMPLE MONTO MINÍMO</option>
                                					<option value="">--</option>
                                					<option value="3">CUMPLE MONTO MAXÍMO</option>
                                					<option value="4">NO CUMPLE MONTO MAXÍMO</option>
                                					<option value="1">CUMPLE MONTO MÍNIMO</option>
                                				@elseif($response['filterAmmount'] == 3)
                                					<option value="3">CUMPLE MONTO MAXÍMO</option>
                                					<option value="">--</option>
                                					<option value="4">NO CUMPLE MONTO MAXÍMO</option>
                                					<option value="1">CUMPLE MONTO MÍNIMO</option>
                                					<option value="2">NO CUMPLE MONTO MINÍMO</option>
                                				@else
                                					<option value="4">NO CUMPLE MONTO MAXÍMO</option>
                                					<option value="">--</option>
                                					<option value="1">CUMPLE MONTO MÍNIMO</option>
                                					<option value="2">NO CUMPLE MONTO MINÍMO</option>
                                					<option value="3">CUMPLE MONTO MAXÍMO</option>
                                				@endif
                                			@else 
                                				<option value="">--</option>
                                        		<option value="1">CUMPLE MONTO MÍNIMO</option>
                            					<option value="2">NO CUMPLE MONTO MINÍMO</option>
                            					<option value="3">CUMPLE MONTO MAXÍMO</option>
                            					<option value="4">NO CUMPLE MONTO MAXÍMO</option>
                                			@endif
                                		</select>
                            		</div>
                        		</div>
                        		
                        		<div class="col-md-3">
                                	<h5>División</h5>
                                	<div class="input-group">
                                		<span class="input-group-addon"><i class="fa fa-filter"></i></span>
                                		<select class="form-control" name="filterDivision">
                                			@if(isset($response['filterDivision']))
                                				<option value="{{ $response['filterDivision'] }}">{{ $response['filterDivision'] }}</option>
                                				<option value="">--</option>
                            				@else
                            					<option value="">--</option>
                            				@endif
                            				
                                			@if(isset($response['divisions']))
                                				@foreach($response['divisions'] as $key)
				                                	<option value="{{ $key->Value }}">{{ $key->Value }}</option>			
                                				@endforeach
                                			@endif
                                		</select>
                            		</div>
                        		</div>
                        		
                        		<div class="col-md-2" style="top: 35px !important;">
                                	<div class="input-group">
                                		<button class="btn btn-transparent bgmarca font-white btn-sm btn-circle btn-courseorders" type="submit"><i class="fa fa-search"></i> Buscar</button>	
                                	</div>
                            	</div>
                        	@endif
                        	
                        	@if(isset($response['current']) && !$response['current'])
                        		<!-- Filtro pedidos historicos asesora administrador -->        
                        		<div class="col-md-3">
                                	<h5>Campaña inicial</h5>
                                	<div class="input-group">
                                		<span class="input-group-addon"><i class="fa fa-filter"></i></span>
                                		<select class="form-control" name="filterStartCampaing" id="filterStartCampaing">
                                			@if(isset($response['filterStartCampaing']))
                                				<option value="{{ $response['filterStartCampaing'] }}">{{ $response['filterStartCampaing'] }}</option>
                                			@endif
                            				<option value="">--</option>
                                			@if(isset($response['startCampaing']))
                                				@foreach($response['startCampaing'] as $campaing)
                                					<option value="{{ $campaing }}">{{ $campaing }}</option>	
                                				@endforeach
                                			@endif
                                		</select>
                            		</div>
                        		</div>
                        		
                        		<div class="col-md-3">
                                	<h5>Campaña final</h5>
                                	<div class="input-group">
                                		<span class="input-group-addon"><i class="fa fa-filter"></i></span>
                                		<select class="form-control" name="filterEndCampaing" id="filterEndCampaing">
                                			@if(isset($response['filterStartCampaing']))
                                				<option value="{{ $response['filterStartCampaing'] }}">{{ $response['filterStartCampaing'] }}</option>
                                			@endif
                            				<option value="">--</option>
                                			@if(isset($response['endCampaing']))  
                                				@foreach($response['endCampaing'] as $campaing)
                                					<option value="{{ $campaing }}">{{ $campaing }}</option>	
                                				@endforeach
                                			@endif
                                		</select>
                            		</div>
                        		</div>
                        		
                        		<div class="col-md-3">
                                	<h5>Asesora</h5>
                                	<div class="input-group">
                                		<span class="input-group-addon"><i class="fa fa-filter"></i></span>
                                		<input type="text" class="form-control" value="{{ isset($response['filterDocumentAdviser']) ? $response['filterDocumentAdviser'] : ''}}" placeholder="Cédula Asesora" name="filterDocumentAdviser">
                            		</div>
                        		</div>
                        		
                        		<div class="col-md-2" style="top: 35px !important;">
                                	<div class="input-group">
                                		<button class="btn btn-transparent bgmarca font-white btn-sm btn-circle btn-courseorders" type="submit"><i class="fa fa-search"></i> Buscar</button>	
                                	</div>
                            	</div>
                    		@endif
                		</form>
                	</div> 
                
                    <table class="table table-hover table-light table-checkable order-column table-condensed" id="tableOrders">
                        <thead>
                        	@if(isset($response['current']) && $response['current'])
                                <tr class="uppercase">
                                    <th class="bold font-dark dt-center" style="vertical-align: middle;"> PEDIDO </th>
									<th class="bold font-dark dt-center" style="vertical-align: middle;"> CAMPAÑA </th>
									<th class="bold font-dark dt-center" style="vertical-align: middle;"> ZONA </th>
									<th class="bold font-dark dt-center" style="vertical-align: middle;"> ASESORA</th>
									<th class="bold font-dark dt-center"> CUMPLE <br/> M/MIN </th>
									<th class="bold font-dark dt-center"> CUMPLE <br/> M/MAX </th>
									<th class="bold font-dark dt-center" style="vertical-align: middle;"> SALDO </th>
									<th class="bold font-dark dt-center"> <i class="fa fa-info-circle" id="statusHelp" data-container='body'></i> <br/> ESTADO</th>
									<th class="bold font-dark dt-center"> </th>
                                </tr>
                            @else
                            	<tr class="uppercase">
                                    <th class="bold font-dark dt-center" style="vertical-align: middle;"> PEDIDO </th>
                                    <th class="bold font-dark dt-center" style="vertical-align: middle;"> FECHA </th>
									<th class="bold font-dark dt-center" style="vertical-align: middle;"> CAMPAÑA </th>
									<th class="bold font-dark dt-center" style="vertical-align: middle;"> ZONA </th>
									<th class="bold font-dark dt-center" style="vertical-align: middle;"> CÉDULA </th>
									<th class="bold font-dark dt-center" style="vertical-align: middle;"> ASESORA </th>
									<th class="bold font-dark dt-center"> CUMPLE <br/> M/MIN </th>
									<th class="bold font-dark dt-center"> CUMPLE <br/> M/MAX </th>
									<th class="bold font-dark dt-center"> <i class="fa fa-info-circle" id="statusHelp" data-container='body'></i> <br/> ESTADO</th>
									<th class="bold font-dark dt-center"> </th>
                                </tr>
                            @endif
                        </thead>   
                        <tbody></tbody>
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



<div class="modal fade bs-modal-sm" id="small" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Seleccione una zona</h4>
            </div>
            <div class="modal-body"> 
            	<div class="row">
            		<div class="col-md-12">
            			<span id="exportZoneFilterMessage" class="font-danger"></span>
            			<select class="form-control" name="exportZoneFilter" id="exportZoneFilter">  
            				@if(isset($response['allZones']))
            					@foreach($response['allZones'] as $zone)
    								<option value="{{ $zone->codZona }}">{{ $zone->codZona }}</option>
            					@endforeach
            				@endif
            			</select>
            		</div>
        		</div>
             </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-transparent font-white btnnewped" data-dismiss="modal">Cerrar</button>
                <a onclick="exportOrders('{{ route('ExportCurrentOrders') }}');" class="btn btn-transparent font-white bgmarca">Aceptar</a>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
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
    
    var urlDetail = "{{ url('findOrderById')}}";    
    var urlDelete = "{{ url('deleteOrder')}}";
    var filter = "{!! $response['filter'] !!}";
    var showFilter = "{{isset($response['showFilter']) ? true : false }}";
    console.log('show filter-->:' + showFilter); 
    
    if(filter !==null && filter !== "" || showFilter) {
    	$('#filterAdvancedGroup').removeClass('hide');
    }
    
    @if(isset($response['current']) && $response['current'])
    	var orderDetailTableRoute = "{!! $response['orderDetailRoute'] !!}"; 
		var currentOrdersRoute = "{!! $response["currentOrderRoute"] !!}";
		loadCourseOrdersTable(currentOrdersRoute, 'tableOrders', orderDetailTableRoute, urlDetail, urlDelete, true, 10, filter);
    @endif
    
    @if(isset($response['current']) && !$response['current'])
    	var historyOrdersRoute = "{!! $response['historyOrderRoute'] !!}";
    	loadHistoryOrdersTable(historyOrdersRoute, 'tableOrders', urlDetail, true, 10, filter);   
    @endif  
    
});

$("#formAdvancedFilter").validate({
    errorElement: 'span', //default input error message container
    errorClass: 'help-block help-block-error', // default input error message class
    focusInvalid: false, // do not focus the last invalid input
    messages: {
        filterMailPlan: {
            required: "Selecciona un mail plan."
        }
    },
    rules: {
        filterMailPlan: {
            required: true
        }
    },
    errorPlacement: function (error, element) { 
    	showErrorFilter();   	
    },
    highlight: function (element) { // hightlight error inputs
        $(element)
            .closest('.form-group').addClass('has-error'); // set error class to the control group
    },
    unhighlight: function (element) { // revert the change done by hightlight
        showErrorFilter();
    },
    success: function (label) {
       showErrorFilter();
    }
});

function showErrorFilter() {
	if($('#filterMailPlan').val() === '' || $('#filterMailPlan').val() === null) {
		$('#mailplan-title').empty();
    	$('#mailplan-title').append('Selecciona un mail plan.');
    	$('#mailplan-title').addClass('text-danger');
    	$('#filterMailPlan').css('border-color', 'red'); 
    	$('#mailplan_filter').css('border-color', 'red');
	} else {
		$('#mailplan-title').empty();
		$('#mailplan-title').append('Mail plan <span class="text-danger" aria-required="true"> * </span>');
		$('#filterMailPlan').css('border-color', '#ccc'); 
    	$('#mailplan_filter').css('border-color', '#ccc');
    	$('#mailplan-title').removeClass('text-danger');
	}             
}

function exportOrders(url) {
	if($('#exportZoneFilter').val() === '' || $('#exportZoneFilter').val() === undefined) {
		$('#exportZoneFilterMessage').empty();
    	$('#exportZoneFilterMessage').append('Selecciona una zona.');
    	$('#exportZoneFilter').css('border-color', 'red');
	}
	
	url = url + '/' + $('#exportZoneFilter').val();  
	
	window.open(url, '_self');
}


@endsection

@section('jsfiles')
<script src="{{ URL::asset('metronic/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/datatables/jquery.dataTables.odata.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/jquery-validation/js/jquery.validate.js') }}" type="text/javascript"></script>
@endsection