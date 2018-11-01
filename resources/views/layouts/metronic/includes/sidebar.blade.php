<?php

use App\Util\Helpers;

$data = Helpers::getSessionObject();
?>
@if(Helpers::validEmptyVar($data))
<div class="page-sidebar-wrapper sidebarpedidos">
    <!-- BEGIN SIDEBAR -->
    <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
    <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
    <div class="page-sidebar navbar-collapse collapse">
        <!-- BEGIN SIDEBAR MENU -->

        <!-- SIDEBAR MENU -->
        <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
            <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
            <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
            <li class="sidebar-toggler-wrapper hide">
                <div class="sidebar-toggler">
                    <span></span>
                </div>
            </li>
            <li>
                <div class="portlet light profile-sidebar-portlet ">
                    <!-- SIDEBAR USERPIC -->
                    <div class="profile-userpic">
                        <a class="display-hide" href="javascript::type(0)" data-toggle="modal" data-target="#myPhotoProfile">
                            <i class="icon-note"></i>Cambiar
                        </a>
                        <img src="{{ Helpers::findProfilePhoto() }}"
                             class="img-responsive" alt=""> 
                    </div>
                    <!-- END SIDEBAR USERPIC -->
                    <!-- SIDEBAR USER TITLE -->
                    <div class="profile-usertitle">
                        <div class="profile-usertitle-name">
                            {{ 
                        (Helpers::validEmptyVar($data['asesora']['nombre']))
                        ? $data['asesora']['nombre'] .' '. $data['asesora']['apellido']
                        : "-" 
                            }}
                        </div>
                        <div class="profile-usertitle-job font-marca"> 
                            {{ 
                		(Helpers::validEmptyVar($data['datosMarca']['nombrePerfil']))
                		? $data['datosMarca']['nombrePerfil']
                		: "Asesora" 
                            }} 
                            <br />
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#myProfile" class="font-mini">
                                <i class="icon-user"></i> Mi perfil 
                            </a>
                        </div>
                    </div>
                    <!-- END SIDEBAR USER TITLE -->
                    <div id="blockDirector" style="">
                        <h4 class="font-marca">Tu directora</h4>
                        <div id="loadingDirectora" style="display: none">
                            <span>
                                <img src="{{ URL::asset('metronic/custom-pedidos-web/img/preloader.gif') }}" />
                            </span>
                        </div>
                        <div id="divDirectora"></div>
                    </div>            
                </div>
            </li>
            <!-- END SIDEBAR TOGGLER BUTTON -->    
            <li class="nav-item start {{ (Route::currentRouteNamed('Inicio') || Route::currentRouteNamed('Inicio')) ? 'active' : '' }}" style="margin-top: 20px;">                
                <a href="{{route('Inicio')}}" class="nav-link">
                    <i class="icon-home"></i>
                    <span class="title">Datos generales</span>
                </a>                
            </li>            
            <li class="nav-item {{ Route::currentRouteNamed('NuevoPedido') ? 'active' : '' }}">
                <a href="{{ route('NuevoPedido') }}" class="nav-link">
                    <i class="icon-basket"></i>
                    <span class="title">Nuevo pedido</span>
                </a>                
            </li>    
            @if (Helpers::validEmptyVar(App\Util\Helpers::isAdviser()) || Helpers::validEmptyVar(App\Util\Helpers::isEmployee()))      
            <li class="nav-item {{ Route::currentRouteNamed('Pedidos') || Route::currentRouteNamed('OrderDetail') ? 'active' : '' }}">
                <a href="{{ route('Pedidos') }}" class="nav-link">
                    <i class="icon-bar-chart"></i>
                    <span class="title">Hist&oacute;rico de pedidos</span>
                </a>   
            </li> 
            @else
            <li class="nav-item {{ Route::currentRouteNamed('PedidosCurso') || Route::currentRouteNamed('HistoricoPedidos') ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-bar-chart"></i>
                    <span class="title">Hist&oacute;rico de pedidos</span>
                    <span class="arrow"></span>
                </a>    
                <ul class="sub-menu">
                    <li class="nav-item  {{ Route::currentRouteNamed('PedidosCurso') }} ? 'active' : '' }}">
                        <a href="{{ route('PedidosCurso', ['history' => 0]) }}" class="nav-link">
                            <i class="fa fa-server"></i>
                            <span class="title">En curso</span>
                        </a> 
                    </li>
                    <li class="nav-item {{ Route::currentRouteNamed('HistoricoPedidos') ? 'active' : '' }}">
                        <a href="{{ route('HistoricoPedidos', ['history' => 1]) }}" class="nav-link">
                            <i class="fa fa-database"></i>
                            <span class="title">Histórico</span>
                        </a>                
                    </li>
                </ul>            
            </li> 
            @endif
            @if (Helpers::validEmptyVar(App\Util\Helpers::isAdviser()))
                @if((new \App\Http\Controllers\Pedidos\PackageController)->countFindAll())
                    <li class="nav-item {{ Route::currentRouteNamed('Paquetes') || Route::currentRouteNamed('Paquetes') ? 'active' : '' }}">
                        <a href="{{ route('Paquetes') }}" class="nav-link">
                            <i class="icon-present"></i>
                            <span class="title">Paquete del ahorro</span>
                        </a>                
                    </li>
                @endif
            @endif
            @if(Helpers::validEmptyVar(App\Util\Helpers::isAdministrator()))
            <li class="nav-item {{ Route::currentRouteNamed('Zonas')  
                        || Route::currentRouteNamed('Clientes') 
                        || Route::currentRouteNamed('Alertas') 
                || Route::currentRouteNamed('CrearAlerta')
                || Route::currentRouteNamed('DetalleAlerta') ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">Configuraci&oacute;n</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item  {{ Route::currentRouteNamed('Zonas') ? 'active' : '' }}">
                        <a href="{{ route('Zonas') }}" class="nav-link">
                            <i class="icon-check"></i>
                            <span class="title">Activar zonas</span>
                        </a> 
                    </li>
                    <li class="nav-item {{ Route::currentRouteNamed('Clientes') ? 'active' : '' }}">
                        <a href="{{ route('Usuarios', ['ajax' => false]) }}" class="nav-link">
                            <i class="icon-users"></i>
                            <span class="title">Clientes</span>
                        </a>                
                    </li>
                    <li class="nav-item {{ Route::currentRouteNamed('Alertas') 
                                || Route::currentRouteNamed('CrearAlerta') 
                                || Route::currentRouteNamed('DetalleAlerta') ? 'active' : '' }}">
                        <a href="{{ route('Alertas') }}" class="nav-link">
                            <i class="icon-bell"></i>
                            <span class="title">Alertas</span>
                        </a>                
                    </li>
                </ul>                               
            </li>
            @endif
            @if(Helpers::validEmptyVar(App\Util\Helpers::isDirector()))
            <li class="nav-item {{ Route::currentRouteNamed('Clientes') ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-settings"></i>
                    <span class="title">Configuraci&oacute;n</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ Route::currentRouteNamed('Clientes') ? 'active' : '' }}">
                        <a href="{{ route('Usuarios', ['ajax' => false]) }}" class="nav-link">
                            <i class="icon-users"></i>
                            <span class="title">Clientes</span>
                        </a>                
                    </li>
                </ul>                               
            </li>
            @endif
            @if(Helpers::validEmptyVar(App\Util\Helpers::getCatalog()))
            <li class="nav-item">
                <a href="{{App\Util\Helpers::getCatalog()}}" target="_blank"  class="nav-link">
                    <i class="icon-notebook"></i>
                    <span class="title">Ver cat&aacute;logo</span>                                
                </a>
            </li>
            @endif            
            <li class="nav-item {{ Route::currentRouteNamed('CambiarClave') ? 'active' : '' }}">
                <a href="{{ route('CambiarClave') }}" class="nav-link">
                    <i class="icon-key"></i>
                    <span class="title">Cambiar clave</span>
                </a>                
            </li>
            <li class="nav-item {{ Route::currentRouteNamed('Ayuda') ? 'active' : '' }}">
                <a href="{{ route('Ayuda') }}" class="nav-link">
                    <i class="icon-rocket"></i>
                    <span class="title">¿C&oacute;mo crear un pedido?</span>
                </a>                
            </li>         
        </ul>

        <!-- END MENU -->    
        <!-- END SIDEBAR MENU -->
        <!-- END SIDEBAR MENU -->
    </div>
    <!-- END SIDEBAR -->
</div>



<!-- BEGIN MODAL DE INFORMACION PERSONAL -->
<div class="modal fade" id="myPhotoProfile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Mi perfil</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <!-- BEGIN FORM-->
                        <form action="{{route('FotoPerfil')}}" class="form-horizontal form-bordered" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="form-body">
                                <div class="form-group last">
                                    <div class="col-md-6" style="border-right: 1px solid #DADADA">
                                        <div class="fileinput-new thumbnail" style="margin: 0 auto; ">
                                            <img src="{{ Helpers::findProfilePhoto() }}"
                                                 class="img-responsive" alt=""> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="fileinput-new thumbnail" style="margin: 0 auto; ">
                                                <img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=sin+imagen" alt="" class="img-responsive" /> 
                                            </div>
                                            <div class="fileinput-preview fileinput-exists thumbnail" style="margin: 0 auto; "> </div>
                                            <div>
                                                <span class="btn default btn-file">
                                                    <span class="fileinput-new"> Seleccionar nueva imagen </span>
                                                    <span class="fileinput-exists"> Cambiar </span>
                                                    <input type="file" name="profilePhoto"> </span>
                                                <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> Remover </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-4 col-md-8">
                                        <button type="submit" class="btn green">
                                            <i class="fa fa-check"></i> Guardar
                                        </button>
                                        <a href="javascript:;" class="btn btn-outline grey-salsa" data-dismiss="modal">Cancelar</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>                           
            </div>
        </div>
    </div>
</div>
@endif 