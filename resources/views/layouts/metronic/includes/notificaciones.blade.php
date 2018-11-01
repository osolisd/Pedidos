<?php

use App\Util\Helpers;

$fechasClaves = Helpers::importantDates(Session::get('fechas'));
$data = Helpers::getSessionObject();

//VERIFICO SI HAY ALERTAS SECUNDARIAS
$alertsSec = (new \App\Http\Controllers\Pedidos\AlertController())->findAllAlertSecondary();
?>
<!-- BEGIN NOTIFICATION DROPDOWN -->
<?php if (!$alertsSec['error'] && Helpers::validEmptyVar($alertsSec['alerts'])) : ?>
    <li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar">
        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
            <i class="icon-bell"></i>
            <span class="badge badge-default"> {{count($alertsSec['alerts'])}} </span>
        </a>
        <ul class="dropdown-menu">
            <li class="external">
                <h3>
                    <span class="bold">{{count($alertsSec['alerts'])}} notificaciones</span>
                </h3>
            </li>
            <li>
                <ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">
                    <?php foreach ($alertsSec['alerts'] as $alert) : ?>
                        <li>
                        	@if(!empty($alert->Detalle->HiperVinculo))
                            	<a href="{{ $alert->Detalle->HiperVinculo }}" target="_blank">
                            @else
                            	<a>
                            @endif
                                <span class="photo">
                                    <span class="label label-sm label-icon label-info bgmarca">
                                        <i class="fa fa-bullhorn"></i>
                                    </span>
                                </span>
                                <span class="subject">
                                    <span class="from"> {{$alert->Titulo}} </span>
                                </span>
                                <span class="message text-justify"> {{$alert->Detalle->ContenidoMensaje}} </span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        </ul>
    </li>
<?php endif; ?>
<!-- END NOTIFICATION DROPDOWN -->

<!-- BEGIN DATES DROPDOWN -->
<li class="dropdown dropdown-extended dropdown-inbox">   
    @if(count(array_filter($fechasClaves)) > 0)
    <?php
    $totalNotif = 0;
    foreach (array_filter($fechasClaves) as $key => $value) {
        if ($key == 'FechasConferencias' || $key == 'FechasCambios') {
            $totalNotif += count($value);
        } else {
            $totalNotif ++;
        }
    }
    ?>
    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
        <i class="icon-calendar"></i>        
        <span class="badge badge-default"> {{$totalNotif}} </span>        
    </a>
    @endif
    <ul class="dropdown-menu">
        <li class="external">
            <h3>Fechas claves
            </h3>
        </li>
        <li>
            <ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">
                <?php if (Helpers::validEmptyVar($fechasClaves['FechasConferencias'])) : ?>
                    <?php foreach ($fechasClaves['FechasConferencias'] as $fechaCo): ?>
                        <?php if (!is_null($fechaCo['FechaConferencia'])): ?>
                            <li>
                                <a href="javascript:void(0)">
                                    <span class="photo">
                                        <div class="uppercase profile-stat-text text-center font-mini">
                                            {{$fechaCo['FechaConferencia'][1]}}
                                        </div>
                                        <div class="uppercase profile-stat-title text-center bold">
                                            {{$fechaCo['FechaConferencia'][2]}}
                                        </div>
                                        <div class="uppercase profile-stat-text text-center font-mini">
                                            {{$fechaCo['FechaConferencia'][0]}}
                                        </div>
                                    </span>
                                    <span class="subject">
                                        <span class="from"> Fecha y lugar de conferencia </span>
                                    </span>
                                    <span class="message"> {{$fechaCo['LugarConferencia']}} </span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if (Helpers::validEmptyVar($fechasClaves['FechasCambios'])) : ?>
                    <?php foreach ($fechasClaves['FechasCambios'] as $fechaCa): ?>
                        <?php if (!is_null($fechaCa['FechaCambiosDevoluciones'])): ?>
                            <li>
                                <a href="javascript:void(0)">
                                    <span class="photo">
                                        <div class="uppercase profile-stat-text text-center font-mini">
                                            {{$fechaCa['FechaCambiosDevoluciones'][1]}}
                                        </div>
                                        <div class="uppercase profile-stat-title text-center bold">
                                            {{$fechaCa['FechaCambiosDevoluciones'][2]}}
                                        </div>
                                        <div class="uppercase profile-stat-text text-center font-mini">
                                            {{$fechaCa['FechaCambiosDevoluciones'][0]}}
                                        </div>
                                    </span>
                                    <span class="subject">
                                        <span class="from"> Fecha y lugar de cambios y devoluciones </span>
                                    </span>
                                    <span class="message"> {{$fechaCa['LugarCambios']}} </span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if (!is_null($fechasClaves['FechaEntregaPedido'])) : ?>              
                    <li>
                        <a href="javascript:void(0)">
                            <span class="photo">
                                <div class="uppercase profile-stat-text text-center font-mini">
                                    {{$fechasClaves['FechaEntregaPedido'][1]}}
                                </div>
                                <div class="uppercase profile-stat-title text-center bold">
                                    {{$fechasClaves['FechaEntregaPedido'][2]}}
                                </div>
                                <div class="uppercase profile-stat-text text-center font-mini">
                                    {{$fechasClaves['FechaEntregaPedido'][0]}}
                                </div>
                            </span>
                            <span class="subject from-only">
                                <span class="from"> Fecha estimada de entrega de pedido </span>                                
                            </span>
                            <span class="message"> &nbsp; </span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (!is_null($fechasClaves['FechaPagoPedido'])): ?>            
                    <li>
                        <a href="javascript:void(0)">
                            <span class="photo">
                                <div class="uppercase profile-stat-text text-center font-mini">
                                    {{$fechasClaves['FechaPagoPedido'][1]}}
                                </div>
                                <div class="uppercase profile-stat-title text-center bold">
                                    {{$fechasClaves['FechaPagoPedido'][2]}}
                                </div>
                                <div class="uppercase profile-stat-text text-center font-mini">
                                    {{$fechasClaves['FechaPagoPedido'][0]}}
                                </div>
                            </span>
                            <span class="subject from-only">
                                <span class="from"> Fecha l&iacute;mite de pago </span>
                            </span>
                            <span class="message"> &nbsp; </span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (!is_null($fechasClaves['FerchaLimitePedido'])) : ?>               
                    <li>
                        <a href="javascript:void(0)">
                            <span class="photo">
                                <div class="uppercase profile-stat-text text-center font-mini">
                                    {{$fechasClaves['FerchaLimitePedido'][1]}}
                                </div>
                                <div class="uppercase profile-stat-title text-center bold">
                                    {{$fechasClaves['FerchaLimitePedido'][2]}}
                                </div>
                                <div class="uppercase profile-stat-text text-center font-mini">
                                    {{$fechasClaves['FerchaLimitePedido'][0]}}
                                </div>
                            </span>
                            <span class="subject from-only">
                                <span class="from"> Fecha l&iacute;mite de ingreso de pedido </span>
                            </span>
                            <span class="message"> &nbsp; </span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </li>
    </ul>
</li>                
<!-- END DATES DROPDOWN -->


<!-- BEGIN USER LOGIN DROPDOWN -->
<li class="dropdown dropdown-user">
    <a href="{{route('Logout')}}" class="dropdown-toggle">
    	<i class="fa fa-sign-out fa-2x" style="font-size: 16px !important;"></i>
        <span class="username"> SALIR</span>
<!--         <i class="fa fa-angle-down"></i> -->
    </a>
    <ul class="dropdown-menu dropdown-menu-default">
        <li>
            <a href="javascript:void(0)" data-toggle="modal" data-target="#myProfile">
                <i class="icon-user"></i> Mi perfil 
            </a>
        </li>
        <li>
            <a href="{{route('Logout')}}">
                <i class="icon-logout"></i> Salir </a>
        </li>
    </ul>
</li>
<!-- END USER LOGIN DROPDOWN -->