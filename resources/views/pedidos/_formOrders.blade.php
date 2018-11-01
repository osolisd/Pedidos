<?php

use App\Util\Helpers;
use Illuminate\Support\Facades\Log;
?>
@if(!isset($response) || $response['pedidoEncabezado']['Editable']) 

@section('cssfiles')
<link href="{{ URL::asset('metronic/global/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('metronic/global/plugins/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="//cdn.jsdelivr.net/jquery.webui-popover/1.2.1/jquery.webui-popover.min.css"/>

<style type="text/css">
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
      -webkit-appearance: none; 
      margin: 0; 
    }
    
    input[type=number] { 
        -moz-appearance:textfield; 
    }
</style>

@endsection

<!-- BEGIN VALIDATE AMOUNT -->
<div class="row">
    <div class="col-md-12" id="statusHelp" data-container='body'>
        <div class="portlet light bordered" style="padding: 35px 15px 25px;">
            <div class="portlet-body form">
            <!-- Barra gris -->
                <div class="termometro">
                <!--  Flecha Minimo -->
                    <div class="montomin">
                        <span class="font-mini">
                            ${{Helpers::numberFormat($MontoMin)}}
                        </span>
                    </div>
                    <!-- Barra Progreso -->
                    <div class="barmontopermitido">
                        <div id="montoPermitido" class="montoPermitido" style="width: 0px">
                        	<!-- Tooltip -->
                            <span class="toolFactura">
                                {{Helpers::validEmptyVar($response['pedidoEncabezado']['totalFacturaPedido'])?
                            '$'.Helpers::numberFormat($response['pedidoEncabezado']['totalFacturaPedido'])
                            :'0'
                                }}
                            </span>
                        </div>
                    </div>
                    <!-- Flecha Monto Max -->
                    <div class="cupomax">
                        <span class="font-mini">
                            ${{Helpers::numberFormat($MontoMax)}}
                        </span>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<!-- END VALIDATE AMOUNT -->
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light bordered" style="">
            <div class="row">
                <div class="form-body">
                    <?php if (Helpers::isAdministrator() || Helpers::isDirector() || Helpers::isEmtelco()): ?>
						<div class="col-md-6">
                            <div class="form-group">
                                <label class="col-md-4 control-label" style="text-align: left;">C&eacute;dula Asesora</label>
                                <div class="col-md-8">
                                	<input type="text" autocomplete="off" id="cedasesoras" name="cedasesora" placeholder="Documento Asesora" class="form-control">
                                	
                                    <input type="hidden" 
                                           name="campanaactual" 
                                           id="campanaactual" 
                                           value=""
                                           />
                                    <input type="hidden" 
                                           name="document" 
                                           id="document" 
                                           value=""
                                           />
                                    <input type="hidden" 
                                           name="zonaactual" 
                                           id="zonaactual" 
                                           value=""
                                           />
                                </div>
                            </div>
                		</div>
                		
                		<div class="col-md-2">
                    		<div class="form-group">
                                <div class="col-md-12">
                    				<a id="btn-searchAdviser" class="btn btn-sm font-white btn-circle" style="background-color: #575656" onclick="JavaScript:cargarUsuarios('{{route('BuscarAsesora')}}');"><i class="fa fa-search"></i> Buscar</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-8" id="panelAdviserName" style="display: none;">
                    		<div class="form-group">
                    			<label class="col-md-3 control-label" style="text-align: left;">Nombre Asesora</label>
                    			<div class="col-md-9">
                    				<input type="text" id="adviserName" placeholder="Nombre Asesora" class="form-control" readonly="readonly" />
                    			</div>
                			</div>
            			</div>
            			
            			<div class="col-md-6" id="panelAdviserCampaing" style="display: none;">
                    		<div class="form-group">
                    			<label class="col-md-4 control-label" style="text-align: left;">Campa&ntilde;a Actual</label>
                    			<div class="col-md-4">
                    				<input type="text" id="adviserCampaing" placeholder="Campa&ntilde;a Actual" class="form-control bold" readonly="readonly" />
                    			</div>
                			</div>
            			</div>
            			
            			<div class="col-md-12" id="panelSeparator" style="display: none;">
            				<hr/>
            			</div>
                    <?php endif; ?>
                    <div id="panelAgregarPD" style="display: <?= (Helpers::isAdministrator() || Helpers::isDirector() || Helpers::isEmtelco()) ? 'none' : 'inline'; ?>">
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-md-2 control-label">C&oacute;digo</label>
                                <div class="col-md-10">
                                    <select id="newplu" name="newplu" class="form-control select2"></select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-md-4 control-label">Cantidad</label>
                                <div class="col-md-8">
                                    <input class="form-control" type="number" name="newcantidad" id="newcantidad" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <a href="javascript:;" class="btn btn-sm font-white btn-circle" id="addProduct" style="background-color: #575656">
                                <i class="fa fa-plus"></i> Agregar producto 
                            </a>
                        </div>
                    </div>                    
                </div>
            </div>            
        </div>        
    </div>
</div>

 

@section('jsfiles')

<script src="https://cdn.jsdelivr.net/jquery.webui-popover/1.2.1/jquery.webui-popover.min.js"></script>

$('#newplu').focus();

<script src="{{ URL::asset('metronic/global/plugins/select2/js/select2.full.min.js') }}" type="text/javascript"></script>
@endsection

@endif

<?php $totalPuntos = 0; ?>
<div class="row">
    {{ csrf_field() }}
    <div class="col-md-12 col-sm-12">
        <div class="portlet grey-cascade box">
            <div class="portlet-title">
                <div class="caption">
                    @if(isset($response['pedidoEncabezado']['pedidoId']))
                    <span class="caption-subject sbold uppercase">
                        <i class="fa fa-list-alt hidden-xs"></i> <span class="hidden-xs">Pedido</span> #{{$response['pedidoEncabezado']['pedidoId']}}
                        <span>| <i class="fa fa-calendar hidden-xs"></i> {{substr($response['pedidoEncabezado']['fecha'], 0, 10)}} </span>
                        <span>| <i class="fa fa-book hidden-xs"></i> <span class="hidden-xs">Campa&ntilde;a:</span>  </span>
                        <span>{{$response['pedidoEncabezado']['campana']}}  </span>
                    </span>
                    @else
                    <i class="fa fa-shopping-cart"></i>Detalle del pedido 
                    @endif
                </div>    
                @if(!isset($response) || $response['pedidoEncabezado']['Editable'])
                <div class="actions">                   
                    <input type="submit" class="btn bgmarca font-white btn-circle" value="Enviar" />                   
                </div>
                @endif
            </div>
            <div class="portlet-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" id="tblPdDetails">
                        <thead>
                            <tr>
                                <th> C&Oacute;DIGO </th>
                                <th> DESCRIPCI&Oacute;N </th>
                                <th> CANTIDAD </th>
                                <th> PRECIO FACTURA </th>
                                <th> PRECIO CAT&Aacute;LOGO </th>
                                <th>  </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $totalCatalogoPedidoAhorro = 0; $totalFacturaPedidoAhorro = 0; ?>
                            @if(isset($response) && isset($response['pedidoDetalle']))                             
                            @foreach($response['pedidoDetalle'] as $detalle)
                            <?php
                            if ($detalle['esPaquete']) {
                                $totalCatalogoPedidoAhorro = $totalCatalogoPedidoAhorro + (int)$detalle['cantidad'] * (int)$detalle['precioCatalogo'];
                                $totalFacturaPedidoAhorro = $totalFacturaPedidoAhorro + (int)$detalle['cantidad'] * (int)$detalle['precioFactura'];
                                $diplay = 'block';
                            }else{                                
                                $totalPuntos += ($detalle['puntosPrenda'] * $detalle['cantidad']); 
                            }
                            ?>
                            <tr data-plu="{{trim($detalle['plu'])}}" 
                                data-quantity="{{trim($detalle['cantidad'])}}" 
                                data-invoiceprice="{{trim($detalle['precioFactura'])}}"
                                data-price="{{trim($detalle['precioCatalogo'])}}"
                                data-ispackage="{{trim($detalle['esPaquete'])}}"
                                data-puntos="{{trim($detalle['puntosPrenda'])}}"
                                data-pluExcluded="{{$detalle['PluExcluido']}}">  
                                <td data-plu="{{trim($detalle['plu'])}}">{{trim($detalle['plu'])}}</td>
                                <td>{{trim($detalle['descripcion'])}}</td>
                                <td>         
                                    @if(!isset($response) || $response['pedidoEncabezado']['Editable'])
                                    <input 
                                        type="number" 
                                        class="form-control cantProd" 
                                        name="products[{{trim($detalle['plu'])}}][cantidad]" 
                                        value="{{trim($detalle['cantidad'])}}" 
                                        data-value="{{trim($detalle['cantidad'])}}" 
                                        min="0"  
                                        <?= /*($detalle['esPaquete']) ? "readonly='readonly'" : "";*/"" ?> 
                                        />
                                    @else
                                    {{trim($detalle['cantidad'])}}
                                    @endif
                                </td>
                                <td id="td_invoice_{{ trim($detalle['plu']) }}" class="text-center">${{ Helpers::numberFormat($detalle['precioFactura'] * trim($detalle['cantidad'])) }}</td>
                                <td id="td_price_{{ trim($detalle['plu']) }}" class="text-center">${{ Helpers::numberFormat($detalle['precioCatalogo'] * trim($detalle['cantidad'])) }}</td>
                                <td>  
                                    @if($response['pedidoEncabezado']['Editable'])
                                    <a class="font-marca rmvPd"
                                       href="javascript:void(0)"
                                       data-toggle="confirmation"
                                       data-original-title="¿Est&aacute;s segura que quieres borrar el producto?"
                                       title=""
                                       data-btn-ok-label="Si"
                                       data-btn-cancel-label="No">
                                        <i class="fa fa-trash-o"></i>                                        
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr></tr>
                            @endif
                        </tbody>
                    </table>   
                </div>
            </div>
        </div>
    </div>            
</div>
<input type="hidden" 
       name="pedidoId" 
       value="{{(isset($response['pedidoEncabezado']['pedidoId'])) ? $response['pedidoEncabezado']['pedidoId'] : ''}}" 
       id="pedidoId"
       />
<input type="hidden" 
       name="totalFacturaPedido" 
       id="hidnTotalFacturaPedido" 
       value="{{Helpers::validEmptyVar($response['pedidoEncabezado']['totalFacturaPedido'])?
           $response['pedidoEncabezado']['totalFacturaPedido']
           :'0'}}" 
       />
<input type="hidden" 
       name="totalCatalogoPedido" 
       id="hidnTotalCatalogoPedido" 
       value="{{$response['pedidoEncabezado']['totalCatalogoPedido']}}" 
       />
<input type="hidden" 
       name="calculadoPedido" 
       id="hidnCalculadoPedido" 
       value="{{$response['pedidoEncabezado']['calculadoPedido']}}" 
       />
<input type="hidden" 
       name="mispuntos" 
       id="hidnPuntos" 
       value="{{$totalPuntos}}" 
       />

<div class="row">
    <div class="col-md-2 col-sm-12">
        <div class="infoPed">
            @if(!isset($response) || $response['pedidoEncabezado']['Editable'])
            <input type="submit" class="btn bgmarca font-white btn-circle" value="Enviar">
            @endif
        </div>  
    </div>
    <div class="col-md-6 col-md-push-4 col-sm-12 col-sm-push-0">
        <div class="well infoPed">
            <div class="row static-info align-reverse">
                <div class="col-md-8 name"> Valor precio factura: </div>
                <div class="col-md-4 value" id="totalFacturaPedido"> ${{Helpers::numberFormat($response['pedidoEncabezado']['totalFacturaPedido'] + $totalFacturaPedidoAhorro)}} </div>
            </div>
            <div class="row static-info align-reverse">
                <div class="col-md-8 name"> Valor precio cat&aacute;logo: </div>
                <div class="col-md-4 value" id="totalCatalogoPedido"> ${{Helpers::numberFormat($response['pedidoEncabezado']['totalCatalogoPedido'] + $totalCatalogoPedidoAhorro)}} </div>
            </div>
            <!-- <div class="row static-info align-reverse" id="paneltotalFacturaPedidoAhorro" style="display: <?= (isset($diplay)) ? $diplay : 'none'; ?>">
                <div class="col-md-8 name"> Valor factura paquete del ahorro: </div>
                <div class="col-md-4 value" id="totalFacturaPedidoAhorro">${{Helpers::numberFormat($totalFacturaPedidoAhorro)}}</div>
            </div>
            <div class="row static-info align-reverse" id="paneltotalCatalogoPedidoAhorro" style="display: <?= (isset($diplay)) ? $diplay : 'none'; ?>">
                <div class="col-md-8 name"> Valor cat&aacute;logo paquete del ahorro: </div>
                <div class="col-md-4 value" id="totalCatalogoPedidoAhorro">${{Helpers::numberFormat($totalCatalogoPedidoAhorro)}}</div>
            </div>-->
            @if(App\Util\Helpers::isAdviser())
            <div class="row static-info align-reverse">
                <div class="col-md-8 name"> Ganancia Estimada: </div>
                <div class="col-md-4 value" id="calculadoPedido"> ${{Helpers::numberFormat($response['pedidoEncabezado']['calculadoPedido'])}} </div>
            </div>   
            @endif         
            <?php if (!App\Util\Helpers::isEmployee() && !App\Util\Helpers::isAdministrator()) : ?>
                <div class="row static-info align-reverse">
                    <div class="col-md-8 name"> Puntos Estimados: </div>
                    <div class="col-md-4 value"> 
                        <span class="badge badge-info" id="puntos">
                            @if(!isset($response) || $response['pedidoEncabezado']['Editable'])
                            {{Helpers::numberFormat($totalPuntos)}}
                            @else
                            {{Helpers::numberFormat($response['pedidoEncabezado']['puntos'])}}
                            @endif
                        </span>
                    </div>
                </div>  
            <?php endif; ?>
            <div class="row static-info">
                <div class="col-md-12"> 
                    <p class="alert alert-warning text-justify bold">
                        <i class="fa fa-info-circle"></i>
                        Nota: Recuerda que estos valores no incluyen 
                        flete o inscripción.
                        <br /><br/>
                        Los paquetes, las ofertas, los catálogos, la inscripción, 
                        las bolsas y el flete No suman para pedido mínimo. 
                        <br/><br/>
						El valor total puede variar si se presentan agotados en el momento del despacho, 
						igualmente tus puntos se pueden afectar si realizas cambios o devoluciones.
                    </p> 
                </div>
            </div>            
        </div>
    </div>
    <div class="col-md-4 col-md-pull-6 col-sm-12 col-sm-pull-0">
        <?php if (Helpers::isAdviser() || Helpers::isEmployee()) : ?>
            <div class="portlet light bordered infoPed">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-map-marker"></i>Direcci&oacute;n de env&iacute;o 
                    </div>                                
                </div>
                <div class="portlet-body">
                    <div class="row static-info">
                        <div class="col-md-12 value"> 
                            Nombre: {{$data['nombre'] . ' ' . $data['apellido']}}
                            <br> Direción: {{$data['direccion']}}
                            <br> Barrio: {{$data['barrio']}}
                            <br> Municipio: {{$data['municipio']}}
                            <br> Departamento: {{$data['departamento']}}
                            <br> Teléfono: {{$data['telefono']}}   
                        </div>
                        <div class="col-md-12 value"> 
                            <p class="alert alert-warning text-justify">
                                <i class="fa fa-info-circle"></i>
                                Por favor verifica si la direcci&oacute;n se encuentra correcta, 
                                en caso contrario comun&iacute;cate con tú directora de zona.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if(!Helpers::isAdviser() && !Helpers::isEmployee()) : ?>
        
    		<div class="portlet light bordered infoPed" id="adviserAddressInfo" style="display: none;">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-map-marker"></i>Direcci&oacute;n de env&iacute;o 
                    </div>                                
                </div>
                <div class="portlet-body">
                    <div class="row static-info">
                        <div class="col-md-12 value" id="adviserPersonalData"> 
                        
                        </div>
                        <div class="col-md-12 value"> 
                            <p class="alert alert-warning text-justify">
                                <i class="fa fa-info-circle"></i>
                                Por favor verifica si la direcci&oacute;n se encuentra correcta, 
                                en caso contrario comun&iacute;cate con tu Directora de zona.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        
        <?php endif;?>
    </div>    
</div>