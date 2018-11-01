@extends('layouts.metronic.main', [
'title' => 'Suscripción paquete del ahorro',
'controller' => null,
'view' => 'Suscripción paquete del ahorro'
])

@section('pagetitle', 'Suscripción paquete del ahorro')

@section('cssfiles')
<link href="{{ URL::asset('metronic/global/plugins/bootstrap-multiselect/css/bootstrap-multiselect.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-form bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-present font-dark"></i>
                    <span class="caption-subject font-dark sbold uppercase">Paquetes</span>
                </div>                
            </div>
            <div class="portlet-body">
                <!-- BEGIN FORM-->
                <form class="form-horizontal" method="GET" action="{{route('GuardarSuscripcion')}}" id="form_package">
                    {{ csrf_field() }}
                    <div class="form-body" style="position: relative">
                        <div class="row">
                            <div class="col-md-5">
                                <select id="select-package" name="id" class="form-control" required>
                                    <option value="">Seleccione un paquete</option>
                                    @foreach($response['packages'] as $packages)
                                    <option value="{{$packages->paqueteAhorroEstrategiaId}}" data-cantmax="{{$packages->cantidadMaxima}}">
                                        {{$packages->codigoPaquete}} - {{$packages->descripcion}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="select-campaing" name="campaingId" class="form-control" required>
                                    <option value="">Seleccione una campa&ntilde;a</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="number" class="form-control" id="quantity" name="quantity" placeholder="cantidad" required style="width: 100px;" />
                            </div>
                        </div>
                    </div>                        
                    <div class="form-actions">
                        <button type="submit" class="btn btn-outline bgmarca font-white btn-circle">
                            <i class="fa fa-plus"></i> agregar paquete
                        </button>
                    </div>
                </form>
                <!-- END FORM-->                
            </div>
        </div>    

        <?php if (App\Util\Helpers::validEmptyVar($response['suscriptions'])): ?>
            <div class="portlet light bordered">
                <div class="portlet-title" style="border-top: none;">
                    <div class="caption">
                        <i class="icon-present font-dark"></i>
                        <span class="caption-subject font-dark sbold uppercase">Suscripciones</span>
                    </div>                
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-hover table-bordered table-condensed table-striped">
                            <tr>
                                <th class="text-center">C&oacute;digo</th>
                                <th class="text-center">Descripci&oacute;n</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Campa&ntilde;a</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                            <?php
                            foreach ($response['suscriptions'] as $suscription):   
                                if($suscription->eliminar == 0 && $suscription->campanaEntrega >= \App\Util\Helpers::getCurrentCampaign()) :
                            ?>
                                    <tr>
                                        <td class="text-center">{{$suscription->plu}}</td>
                                        <td class="text-center">{{$suscription->descripcionPlu}}</td>
                                        <td class="text-center">
                                            <input class="form-control cantidad" 
                                                           name="cantidad"
                                                           placeholder="Cantidad" 
                                                           required="" aria-required="true" 
                                                           type="number" 
                                                           data-plu="{{$suscription->plu}}" 
                                                           data-pckgid="{{$suscription->paqueteAhorroEstrategiaId}}" 
                                                           data-campana="{{$suscription->paqueteAhorroFechasEntregaId}}" 
                                                           data-cantmax="{{$suscription->cantidadMaxima}}"
                                                           data-id="{{$suscription->paqueteAhorroSuscripcionesId}}"
                                                           value="{{$suscription->cantidad}}" 
                                                        />                                                                              
                                        </td>
                                        <td class="text-center">{{$suscription->campanaEntrega}}</td>
                                        <td class="text-center">
                                            <a href="<?php
                                            echo route('EliminarSuscripcion', [
                                                'campaingId' => $suscription->paqueteAhorroFechasEntregaId,
                                                'packageId' => $suscription->paqueteAhorroEstrategiaId,
                                                'id' => $suscription->paqueteAhorroSuscripcionesId
                                            ])
                                            ?>" title="Eliminar paquete" class="font-marca">                                            
                                                <i class="fa fa-trash-o"></i>
                                            </a>
                                        </td>
                                    </tr>
                            <?php
                                endif;
                            endforeach; 
                            ?>
                        </table>
                    </div>

                </div>
            </div>  
        <?php endif; ?>
    </div>
</div>
@endsection


@section('scripts')
var routeCampanaPaquete = "{{route('CampanaPaquete')}}";
var routeEditarSuscripcion = "{{route('EditarSuscripcion')}}";

$.validator.addMethod("validQuantity", function (value, element) {
    var valSelect = $("#quantity").val();
    if (valSelect != '') {
        var cantmax = $("#select-package").find(':selected').data('cantmax');
        return cantmax >= valSelect;
    }
}, function (params, element) {
    var cantmax = $("#select-package").find(':selected').data('cantmax');
    if (typeof cantmax !== 'undefined') {
        return 'Solo puedes ingresar hasta  ' + cantmax + ' paquetes.';
    }
    return '';
});
$('#form_package').validate({
    errorElement: 'span', //default input error message container
    errorClass: 'help-block', // default input error message class
    focusInvalid: false, // do not focus the last invalid input
    rules: {
        quantity: {
            required: true,
            number: true,
            validQuantity: true,
            min: 1
        },
        campaingId: {
            required: true
        },
        id: {
            required: true
        }
    },
    messages: {
        id: {
            required: "Seleccione un paquete."
        },
        campaingId: {
            required: "Seleccione una campaña."
        },
        quantity: {
            required: "Digite una cantidad válida.",
            min: "Digite una cantidad válida."
        }
    },
    errorPlacement: function (error, element) { // render error placement for each input type
        error.insertBefore(element);
    },
});
@endsection

@section('jsfiles')
<script src="{{ URL::asset('metronic/global/plugins/bootstrap-multiselect/js/bootstrap-multiselect.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/pages/scripts/components-bootstrap-multiselect.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/jquery-validation/js/jquery.validate.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/jquery-validation/js/additional-methods.js') }}" type="text/javascript"></script>
@endsection

