<?php

use App\Util\Helpers;

$data = Helpers::getSessionObject();
$marcaActual = (isset($data['datosMarca']['marcaActual'])) ? $data['datosMarca']['marcaActual'] : 'pcfk';
switch (strtolower($marcaActual)) {
    case 'carmel':
        $clChangeBrand = '#b79742';
        break;
    case 'loguin':
        $clChangeBrand = '#272d44';
        break;
    case 'pcfk':
        $clChangeBrand = '#ffffff';
        break;
    default:
        $clChangeBrand = '#b79742';
        break;
}
?>
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">

    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner container">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
            <div class="pull-left">
                <a href="{{ route('Inicio') }}">                     
                    <img src="{{ URL::asset('metronic/custom-pedidos-web/img/'.strtolower($marcaActual).'-logo.png') }}" 
                         alt="logo" 
                         class=""   
                         style="width: 170px;margin: 5px 0 0"/>                    
                </a>
            </div>
            <!-- BEGIN MULTIMARCA -->
            <div class="pull-right">                
                <div class="btn-group-xs" style="margin: 0; padding: 0">
                    <a class="{{$clChangeBrand}} dropdown-toggle" id="changeBrand"
                       data-toggle="dropdown" 
                       href="javascript:void(0)"
                       style="font-size: 30px; color: {{$clChangeBrand}}">
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu changeBrand" role="menu">
                        <?php
                        $marcas = (isset($data['marcas'])) ? $data['marcas'] : ['pcfk'];
                        ?>
                        @foreach($marcas as $marca)
                        @if(!empty($marca))
                        <li>
                            <a href="{{route('CambioMarca',['brand' => strtolower($marca)])}}" 
                               data-marca="{{$marca}}">
                                <img src="{{ URL::asset('metronic/custom-pedidos-web/img/'.strtolower($marca).'-logo-original.png') }}" 
                                     alt="logo" 
                                     class="logo-default"
                                     style="{{$marca=='Loguin'? 'width:110px; margin: 15px 0 15px -20px' : 'width:110px; margin: 15px 0;'}};
                                     "/>
                            </a>
                        </li>
                        @endif
                        @endforeach                    
                    </ul>
                </div>
            </div>
            <!-- END MULTIMARCA -->
        </div>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
            <span></span>
        </a>
        <!-- END RESPONSIVE MENU TOGGLER -->  



        <!-- BEGIN TOP NAVIGATION MENU -->
        <div class="top-menu">
            <!-- BEGIN NOTIFICACIONES -->
            <ul class="nav navbar-nav pull-right" id="listNotifications">
                @include('layouts.metronic.includes.notificaciones')
            </ul>
            <!-- END NOTIFICACIONES -->
        </div>
        <!-- END TOP NAVIGATION MENU -->
    </div>
    <!-- END HEADER INNER -->
</div>
<!-- END HEADER -->

<!-- BEGIN MODAL DE INFORMACION PERSONAL -->
<div class="modal fade" id="myProfile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Mi perfil</h4>
            </div>
            <div class="modal-body">
                <div class="portlet light profile-sidebar-portlet">
                    <div class="row">
                        <div class="col-md-4" style="border-right: 1px solid #efefef">
                            <div class="profile-userpic">
                                <img src="{{ Helpers::findProfilePhoto() }}" class="img-responsive" alt=""> 
                            </div>
                            <!-- SIDEBAR USER TITLE -->
                            <div class="profile-usertitle">
                                <div class="profile-usertitle-name">
                                    {{ 
                                        (Helpers::validEmptyVar($data['asesora']['nombre'])) 
                                        ? $data['asesora']['nombre']  
                                        : "-" 
                                    }}
                                    {{ 
                                        (Helpers::validEmptyVar($data['asesora']['apellido'])) 
                                        ? " " . $data['asesora']['apellido']  
                                        : "-" 
                                    }}
                                </div>
                                <div class="profile-usertitle-job">
                                    {{ 
                                        (Helpers::validEmptyVar($data['datosMarca']['nombrePerfil']))
                                        ? $data['datosMarca']['nombrePerfil']
                                        : "Asesora" 
                                    }} 
                                </div>
                            </div>
                            <!-- END SIDEBAR USER TITLE -->
                        </div>
                        <div class="col-md-8">
                            <div class="well">
                                <div>
                                    <address>                                        
                                        <br><i class="fa fa-user"></i>
                                        {{ 
                                            (Helpers::validEmptyVar($data['asesora']['documento'])) 
                                            ? $data['asesora']['documento']  
                                            : "-" 
                                        }}
                                        <br><i class="fa fa-phone"></i>
                                        {{ 
                                            (Helpers::validEmptyVar($data['asesora']['telefono'])) 
                                            ? $data['asesora']['telefono']  
                                            : "-" 
                                        }}
                                        <br><i class="icon-envelope"></i>
                                        <a href="mailto:{{ 
                                            (Helpers::validEmptyVar($data['asesora']['correo'])) 
                                            ? $data['asesora']['correo']  
                                            : "-" 
                                           }}">
                                            {{ 
                                                (Helpers::validEmptyVar($data['asesora']['correo'])) 
                                                ? $data['asesora']['correo']  
                                                : "-" 
                                            }}
                                        </a>
                                        <br><i class="fa fa-map-marker"></i>
                                        {{ 
                                            (Helpers::validEmptyVar($data['asesora']['direccion'])) 
                                            ? $data['asesora']['direccion']  
                                            : "-" 
                                        }}
                                        <br><i class="fa fa-home"></i>
                                        {{ 
                                            (Helpers::validEmptyVar($data['asesora']['barrio'])) 
                                            ? $data['asesora']['barrio']  
                                            : "-" 
                                        }}
                                        <br><i class="fa fa-flag-o"></i>
                                        {{ 
                                            (Helpers::validEmptyVar($data['asesora']['departamento'])) 
                                            ? $data['asesora']['departamento']  
                                            : "-" 
                                        }}
                                        <br><i class="fa fa-building"></i>
                                        {{ 
                                            (Helpers::validEmptyVar($data['asesora']['municipio'])) 
                                            ? $data['asesora']['municipio']  
                                            : "-" 
                                        }}
                                        <br><span class="bold">Zona:</span> 
                                        {{ 
                                            (Helpers::validEmptyVar($data['datosMarca']['zona'])) 
                                            ? $data['datosMarca']['zona']
                                            : "-" 
                                        }}
                                    </address>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>                               
            </div>
        </div>
    </div>
</div>