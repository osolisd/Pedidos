@section('cssfiles')
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ URL::asset('metronic/pages/css/faq.min.css') }}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL STYLES -->
@endsection

<?php
//var_dump($response[0]->ReturnChanges);
//exit;
?>

<div class="row">
    <div class="col-md-12">
        <div class="portlet box grey-cascade">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-notebook"></i>Pedidos facturados
                </div>                
            </div>
            <div class="portlet-body">
                <div class="panel-group accordion" id="accordInvoices">
                    <?php $contAccordion = 1; ?>
                    <?php foreach ($response as $pedido): ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a class="accordion-toggle accordion-toggle-styled" 
                                       data-toggle="collapse" 
                                       data-parent="#accordInvoices" href="#collapse_<?= $contAccordion ?>"> 
                                        <i class="fa fa-list-alt hidden-xs"></i> 
                                        <span class="hidden-xs">Pedido</span> #{{$pedido->PedidoId}}
                                        <span>| <i class="fa fa-calendar hidden-xs"></i> 
                                            {{substr($pedido->Fecha, 0, 10)}} 
                                        </span>
                                        <span>| <i class="fa fa-book hidden-xs"></i> 
                                            <span class="hidden-xs">Campa&ntilde;a:</span>  </span>
                                        <span>{{$pedido->CampanaId}}  </span>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse_<?= $contAccordion ?>" class="panel-collapse in">
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-condensed" id="tblPdDetails">
                                            <thead>
                                                <tr>
                                                    <th> C&Oacute;DIGO </th>
                                                    <th> DESCRIPCI&Oacute;N </th>
                                                    <th> CANTIDAD </th>
                                                    <th> PRECIO FACTURA </th>
                                                    <th> SUBTOTAL </th>
                                                    <th> ESTADO </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (isset($pedido->OrderDetail)): ?>                            
                                                    <?php foreach ($pedido->OrderDetail as $detalle): ?>                                                        
                                                        <tr class="<?= ($detalle->Estado == 'A' || $detalle->Estado == 'F') ? 'bg-grey-cararra font-grey-salsa' : '' ?>">
                                                            <td>
                                                                {{trim($detalle->CodigoPlu)}}
                                                                <?php if ($detalle->EsOferta == 'S'): ?>
                                                                    <span class="badge badge-important pull-right">
                                                                        OF
                                                                    </span>
                                                                <?php endif; ?>                                                                
                                                            </td>
                                                            <td>{{trim($detalle->Descripcion)}}</td>
                                                            <td>{{trim($detalle->Cantidad)}}</td>
                                                            <td>${{trim($detalle->PrecioFactura)}}</td>
                                                            <td>${{trim($detalle->Subtotal)}}</td>                                                            
                                                            <td>
                                                                <?= App\Util\Helpers::statusProductInvoiced($detalle->Estado); ?>
                                                            </td>                                                            
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>                    
                                    </div>
                                    <div class="row">                                        
                                        <div class="col-md-6 col-md-offset-6">
                                            <div class="well">
                                                <div class="row static-info align-reverse">
                                                    <div class="col-md-8 name"> Total factura del pedido: </div>
                                                    <div class="col-md-3 value" id="totalFacturaPedido">
                                                        ${{$pedido->TotalFactura}} 
                                                    </div>
                                                </div>
                                                <!--<div class="row static-info align-reverse">
                                                    <div class="col-md-8 name"> Ganancia: </div>
                                                    <div class="col-md-3 value" id="calculadoPedido"> $ </div>
                                                </div>-->   
                                                <?php if (!App\Util\Helpers::isEmployee() && !App\Util\Helpers::isAdministrator()) : ?>
                                                    <div class="row static-info align-reverse">
                                                        <div class="col-md-8 name"> Puntos: </div>
                                                        <div class="col-md-3 value"> 
                                                            <span class="badge badge-info" id="puntos">
                                                                {{$pedido->PuntosPedido}}
                                                            </span>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (App\Util\Helpers::validEmptyVar($pedido->ReturnChanges)): ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="portlet light">
                                                    <div class="portlet-title">
                                                        <div class="caption font-red-sunglo">
                                                            <span class="caption-subject bold uppercase">
                                                                Cambios y devoluciones enviados y facturados 
                                                            </span>                                                            
                                                        </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-striped table-condensed" id="tblPdDetails">
                                                                <thead>
                                                                    <tr>
                                                                        <th> C&Oacute;DIGO </th>
                                                                        <th> DESCRIPCI&Oacute;N </th>
                                                                        <th> CANTIDAD </th>
                                                                        <th> PRECIO FACTURA </th>
                                                                        <th> SUBTOTAL </th>
                                                                        <th> ESTADO </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php if (isset($pedido->ReturnChanges)): ?>                            
                                                                        <?php foreach ($pedido->ReturnChanges as $detalle): ?>                                                        
                                                                            <tr class="<?= ($detalle->Estado == 'A' || $detalle->Estado == 'F') ? 'bg-grey-cararra font-grey-salsa' : '' ?>">
                                                                                <td>
                                                                                    {{trim($detalle->CodigoPlu)}}
                                                                                    <?php if ($detalle->EsOferta == 'S'): ?>
                                                                                        <span class="badge badge-important pull-right">
                                                                                            OF
                                                                                        </span>
                                                                                    <?php endif; ?>                                                                
                                                                                </td>
                                                                                <td>{{trim($detalle->Descripcion)}}</td>
                                                                                <td>{{trim($detalle->Cantidad)}}</td>
                                                                                <td>${{trim($detalle->PrecioFactura)}}</td>
                                                                                <td>${{trim($detalle->Subtotal)}}</td>                                                            
                                                                                <td>
                                                                                    <?= App\Util\Helpers::statusProductInvoiced($detalle->Estado); ?>
                                                                                </td>                                                            
                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    <?php else: ?>
                                                                        <tr></tr>
                                                                    <?php endif; ?>
                                                                </tbody>
                                                            </table>                    
                                                        </div>
                                                    </div>
                                                </div>                                                
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php $contAccordion++; ?>
                    <?php endforeach; ?>                    
                </div>
            </div>
        </div>
    </div>
</div>