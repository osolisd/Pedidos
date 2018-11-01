<!-- <div class="text-center margin-top-30" id="loadingOrder">
    <span>
        <img src="{{ URL::asset('metronic/custom-pedidos-web/img/preloader.gif') }}" />
    </span>
</div>-->

<div class="portlet" id="porletHistoryOrders">
	<div class="portlet-title capaingcourse">
        <div class="caption caption-md">
            <i class="icon-bar-chart theme-font hide"></i>
            <span class="caption-subject bold uppercase font-marca">Pedidos campaña en curso</span>
        </div>
        <div class="actions">
            <a class="btn btn-transparent btn-sm font-white btn-circle btnnewped" href="{{ route('NuevoPedido') }}">  
                <i class="fa fa-plus"></i> Nuevo pedido
            </a>
            <a class="btn btn-transparent bgmarca font-white btn-sm btn-circle btn-historyorders">    
                <i class="fa fa-book"></i> Histórico pedidos
            </a>  
            <a class="btn btn-transparent bgmarca font-white btn-sm btn-circle" href="{{ route('PedidosCurso', ['history' => 0]) }}" id="showAllOrders">
                <i class="fa fa-list"></i> Ver todo
            </a>        
        </div>
    </div>
    
    <div class="portlet-title historyorders display-hide">
        <div class="caption caption-md">
            <i class="icon-bar-chart theme-font hide"></i>
            <span class="caption-subject bold uppercase font-marca">Histórico pedidos</span>
        </div>
        <div class="actions">
            <a class="btn btn-transparent btn-sm font-white btn-circle btnnewped" href="{{ route('NuevoPedido') }}">  
                <i class="fa fa-plus"></i> Nuevo pedido
            </a>
            <a class="btn btn-transparent bgmarca font-white btn-sm btn-circle btn-courseorders">    
                <i class="fa fa-book"></i> Pedidos Curso
            </a>  
            <a class="btn btn-transparent bgmarca font-white btn-sm btn-circle" href="{{ route('HistoricoPedidos', ['history' => 1]) }}" id="showAllOrders2">
                <i class="fa fa-list"></i> Ver todo
            </a>        
        </div>
    </div>
    
    <div class="portlet-body">        
        <div class="table-scrollable">
            <table class="table table table-hover table-light table-checkable order-column table-condensed" id="tblListOrders">
                <thead>
                    <tr class="uppercase">
                        <th class="bold font-dark dt-center" style="vertical-align: middle;"> PEDIDO </th>
                        <th class="bold font-dark dt-center" style="vertical-align: middle;"> CAMPAÑA </th>
                        <th class="bold font-dark dt-center" style="vertical-align: middle;"> ZONA </th>
                        <th class="bold font-dark dt-center" style="vertical-align: middle;"> ASESORA</th>
                        <th class="bold font-dark dt-center"> CUMPLE M/MIN </th>
                        <th class="bold font-dark dt-center"> CUMPLE M/MAX </th>
                        <th class="bold font-dark dt-center" style="vertical-align: middle;"> SALDO </th>
                        <th class="bold font-dark dt-center"> <i class="fa fa-info-circle" id="statusHelp" data-container='body'></i> ESTADO</th>
                        <th class="bold font-dark dt-center"> </th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            
            <table class="table table table-hover table-light table-checkable order-column table-condensed" id="tblListOrdersHistory" style="display: none;">
                <thead>
                    <tr class="uppercase">
                        <th class="bold font-dark dt-center" style="vertical-align: middle;"> PEDIDO </th>
                        <th class="bold font-dark dt-center" style="vertical-align: middle;"> FECHA </th>
                        <th class="bold font-dark dt-center" style="vertical-align: middle;"> CAMPAÑA </th>
                        <th class="bold font-dark dt-center" style="vertical-align: middle;"> ZONA </th>
						<th class="bold font-dark dt-center" style="vertical-align: middle;"> CÉDULA </th>
						<th class="bold font-dark dt-center" style="vertical-align: middle;"> ASESORA </th>
						<th class="bold font-dark dt-center"> CUMPLE <br/> M/MIN </th>
						<th class="bold font-dark dt-center"> CUMPLE <br/> M/MAX </th>
                        <th class="bold font-dark dt-center"> <i class="fa fa-info-circle" id="statusHelp" data-container='body'></i> ESTADO</th>
                        <th class="bold font-dark dt-center"> </th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            
            <div class="dt-loader" style="height: 70px;"></div>
        </div>
    </div>
</div>

<div id="popover_content_wrapper" style="display: none;">
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
