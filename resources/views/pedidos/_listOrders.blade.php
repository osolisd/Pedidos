<div class="text-center margin-top-30" id="loadingOrder">
    <span>
        <img src="{{ URL::asset('metronic/custom-pedidos-web/img/preloader.gif') }}" />
    </span>
</div>
<div class="portlet" id="porletHistoryOrders" style="display: none">
    <div class="portlet-title">
        <div class="caption caption-md">
            <i class="icon-bar-chart theme-font hide"></i>
            <span class="caption-subject bold uppercase font-marca">&Uacute;ltimos pedidos</span>
        </div>
        <div class="actions">
            <a class="btn btn-transparent btn-sm font-white btn-circle btnnewped" href="{{ route('NuevoPedido') }}">  
                <i class="fa fa-plus"></i> Nuevo pedido
            </a>
            <a class="btn btn-transparent bgmarca font-white btn-sm btn-circle" href="{{ route('Pedidos') }}" id="showAllOrders">
                <i class="fa fa-list"></i> Ver todo
            </a>            
        </div>
    </div>
    <div class="portlet-body">        
        <div>
            <table class="table table table-hover table-light table-checkable order-column table-condensed" id="tblListOrders">
                <thead>
                    <tr class="uppercase">
                        <th class="bold font-dark"> PEDIDO </th>
                        <th class="bold font-dark"> CAMPA&Ntilde;A </th>
                        <th class="bold font-dark"> FECHA PEDIDO</th>
                        <th class="bold font-dark"> ZONA </th>
                        <th class="bold font-dark"> ESTADO <i class="fa fa-info-circle" id="statusHelp" data-container='body'></i></th>
                        <th class="bold font-dark"> ACCIONES </th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
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