<?php

use App\Util\Helpers;
//Si existe una alerta es un editar
$viewCreate = !isset($response['alert']);
// $slideKeyMultiSelect = false;
if ($viewCreate) {
    $action = route('GuardarAlerta');
    $txtButton = 'Crear';
} else {
    $action = route('EditarAlerta');
    $txtButton = 'Guardar';
}
?>
<!-- Estilos CSS -->
@section('cssfiles')
<link href="{{ URL::asset('metronic/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('metronic/global/plugins/bootstrap-select/css/bootstrap-select.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('metronic//global/plugins/jquery-multi-select/css/multi-select.css')}}" rel="stylesheet" type="text/css" />
@endsection

<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-form bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-bell font-red"></i>
                    <span class="caption-subject font-red sbold uppercase"></span>
                </div>  
                <div class="actions">
                    <a href="{{route('Alertas')}}" class="btn btn-default btn-circle btn-sm" title="Volver">
                        <i class="fa fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <!-- BEGIN FORM-->
                <form action="{{$action}}" method="POST" id="form_alertas" class="form-horizontal" enctype="multipart/form-data" autocomplete="off">
                    {{ csrf_field() }}
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                ¿Es principal?
                            </label>
                            <div class="col-md-4">
                                <select class="form-control" name="principal" id="principal">
                                    <option value="0"
                                    <?=
                                    isset($response['alert']->AlertaPrincipal) && ($response['alert']->AlertaPrincipal == 0) ?
                                            'selected="selected"' : '';
                                    ?>>Secundaria</option>
                                    <option value="1" 
                                    <?=
                                    isset($response['alert']->AlertaPrincipal) && ($response['alert']->AlertaPrincipal == 1) ?
                                            'selected="selected"' : '';
                                    ?>>Principal</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Título
                                <span class="required" aria-required="true"> * </span>
                            </label>
                            <div class="col-md-4">
                                <input name="titulo"
                                	   id="titulo"
                                       data-required="1" 
                                       class="form-control" 
                                       type="text" 
                                       value="<?= isset($response['alert']->Titulo) ? $response['alert']->Titulo : ''; ?>" 
                                       required > 
                               	<span></span>
                            </div>
                        </div>
                        <div class="form-group" id="panelDesc">
                            <label class="control-label col-md-3">
                                Descripción
                                <span class="required" aria-required="true"> * </span>
                            </label>
                            <div class="col-md-4">
                                <textarea name="descripcion" id="descripcion" required class="form-control"><?=
                                    isset($response['alert']->Detalle->ContenidoMensaje) ?
                                            trim($response['alert']->Detalle->ContenidoMensaje) : '';
                                    ?></textarea>
                            </div>
                        </div> 
                        <div class="form-group" id="panelUbi">
                            <label class="control-label col-md-3">
                                Ubicación
                                <span class="required" aria-required="true"> * </span>
                            </label>
                            <div class="col-md-4">
                                <select class="form-control" name="ubicacion" id="ubicacion">
                                    <option value="A" 
                                    <?=
                                    isset($response['alert']->Detalle->Ubicacion) && $response['alert']->Detalle->Ubicacion == 'A' ?
                                            "selected='selected'" : ""
                                    ?>>Dashboard</option>
                                    <option value="NP"
                                    <?=
                                    isset($response['alert']->Detalle->Ubicacion) && $response['alert']->Detalle->Ubicacion == 'NP' ?
                                            "selected='selected'" : ""
                                    ?>>Nuevo pedido</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="panelImg">
                            <label class="control-label col-md-3">Imagen alerta
                                <span class="required" aria-required="true"> * </span>
                            </label>
                            <div class="col-md-3">
                                <?php if (isset($response['alert']->Detalle->ImagenId) && !empty($response['alert']->Detalle->ImagenId)): ?>
                                    <img src="{{$response['alert']->Detalle->ImagenId}}" style="width: 100px; height: 100px;" alt="Imagen" />
                                    <br /><br />
                                <?php endif; ?>
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="input-group input-large">
                                        <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                            <i class="fa fa-image fileinput-exists"></i>
                                            <span class="fileinput-filename"> </span>
                                        </div>
                                        <span class="input-group-addon btn default btn-file">
                                            <span class="fileinput-new"> Seleccione imagen </span>
                                            <span class="fileinput-exists"> Cambiar </span>
                                            <input type="hidden">
                                            <?php if (isset($response['alert']->Detalle->ImagenId) && !empty($response['alert']->Detalle->ImagenId)) { ?>
                                            	<input name="oldAlertImage" id="oldAlertImage" type="hidden" value="{{$response['alert']->Detalle->ImagenId}}">
                                            	<input name="imagen" id="imagen" type="file">
                                            <?php } else { ?>
                                        		<input name="imagen" id="imagen" type="file" required>
                                            <?php }  ?>
                                        </span>
                                        <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Quitar </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="panelOri">
                            <label class="control-label col-md-3">Orientación
                                <span class="required" aria-required="true"> * </span>
                            </label>
                            <div class="col-md-4">                                
                                <select class="form-control" name="orientacion" id="orientacion">
                                    <option value="h" 
                                    <?=
                                    isset($response['alert']->Detalle->Orientacion) && $response['alert']->Detalle->Orientacion == 'h' ?
                                            "selected" : ""
                                    ?>>Horizontal</option>
                                    <option value="v"
                                    <?=
                                    isset($response['alert']->Detalle->Orientacion) && $response['alert']->Detalle->Orientacion == 'v' ?
                                            "selected" : ""
                                    ?>>Vertical</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">
                                Tipo asesoras
                                <span class="required" aria-required="true"> * </span>
                            </label>
                            <div class="col-md-4">
                                <select class="form-control" name="newAdviser" id="newAdviser">
                                    <option value="1" 
                                    <?=
                                    isset($response['alert']->Detalle->AsesorasNuevas) && ($response['alert']->Detalle->AsesorasNuevas == 1) ?
                                            "selected='selected'" : ""
                                    ?>>Nuevas</option>
                                    <option value="0" 
                                    <?=
                                    isset($response['alert']->Detalle->AsesorasNuevas) && ($response['alert']->Detalle->AsesorasNuevas == 0) ?
                                            "selected='selected'" : ""
                                    ?>>Antiguas</option>
                                    <option value="2" 
                                    <?=
                                    isset($response['alert']->Detalle->AsesorasNuevas) && ($response['alert']->Detalle->AsesorasNuevas == 2) ?
                                            "selected='selected'" : ""
                                    ?>>Todas</option>        
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Url
                                <span class="required" aria-required="true"></span>
                            </label>
                            <div class="col-md-4">
                                <input name="url"
                                	   id="url"
                                       class="form-control" 
                                       type="text" 
                                       value="<?= isset($response['alert']->Detalle->HiperVinculo) ? $response['alert']->Detalle->HiperVinculo : ''; ?>" 
                                       > 
                               	<span></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Fecha inicio
                                <span class="required" aria-required="true"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-group date" data-provide="datepicker">
                                    <input type="text" name="feini" 
                                           class="form-control date startdate" 
                                           value="<?= isset($response['alert']->FechaInicio) ? substr($response['alert']->FechaInicio, 0, 10) : ''; ?>"
                                           required>
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Fecha fin
                                <span class="required" aria-required="true"> * </span>
                            </label>
                            <div class="col-md-4">
                                <div class="input-group date" data-provide="datepicker">
                                    <input type="text" name="fefin" 
                                           class="form-control date enddate" 
                                           value="<?= isset($response['alert']->FechaFin) ? substr($response['alert']->FechaFin, 0, 10) : ''; ?>"
                                           required>
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                </div>
                                <span class="help-block help-block-error"></span>
                            </div>
                        </div>
                        <hr />
                        <div class="form-group last">
                            <label class="control-label col-md-2">Grupos
                                <span class="required" aria-required="true"> * </span>
                            </label>
                            <div class="col-md-10">
                                <select multiple="multiple" 
                                        class="multi-select" 
                                        id="my_multi_select2" 
                                        name="my_multi_select2[]" 
                                        required>
                                            <?php if (Helpers::validEmptyVar($response['stencilStatus'])) : ?>
                                        <optgroup label="STENCIL">
                                            <?php foreach ($response['stencilStatus'] as $stencil) : ?>

                                                <!-- Si es una edición mostramos la alertas seleccionadas -->
                                                <?php if (isset($response['alert']->Detalle->ListaEstadoEstencilIds)): ?>
                                                    <?=
                                                    Helpers::selectGroup($response['alert']->Detalle->ListaEstadoEstencilIds
                                                            , $stencil->Key
                                                            , $stencil->Value
                                                            , 'S');
                                                    ?>                                                    
                                                <?php else: ?>
                                                    <option value="S|{{$stencil->Key}}">{{$stencil->Value}}</option>
                                                <?php endif; ?>
                                                <!-- fin seleccion de alertas -->                                                

                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endif; ?>
                                    <?php if (Helpers::validEmptyVar($response['clasifications'])) : ?>
                                        <optgroup label="CLASIFICACIONES">
                                            <?php foreach ($response['clasifications'] as $clas) : ?>

                                                <!-- Si es una edición mostramos la alertas seleccionadas -->
                                                <?php if (isset($response['alert']->Detalle->ListaClasificacionValorIds)): ?>
                                                    <?=
                                                    Helpers::selectGroup($response['alert']->Detalle->ListaClasificacionValorIds
                                                            , $clas->Key
                                                            , $clas->Value
                                                            , 'C');
                                                    ?>                                                    
                                                <?php else: ?>
                                                    <option value="C|{{$clas->Key}}">{{$clas->Value}}</option>
                                                <?php endif; ?>
                                                <!-- fin seleccion de alertas -->                                                

                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endif; ?>
                                    <?php if (Helpers::validEmptyVar($response['zones'])) : ?>
                                        <optgroup label="ZONAS">
                                            <!-- Recorremos la lista de zonas -->
                                            <?php foreach ($response['zones'] as $zone) : ?>

                                                <!-- Si es una edición mostramos la alertas seleccionadas -->
                                                <?php if (isset($response['alert']->Detalle->ListaCodigoZonas)): ?>
                                                    <?=
                                                    Helpers::selectGroup($response['alert']->Detalle->ListaCodigoZonas
                                                            , $zone->codZona
                                                            , $zone->nombreZona
                                                            , 'Z');
                                                    ?>                                                    
                                                <?php else: ?>
                                                    <option value="Z|{{$zone->codZona}}">{{ $zone->codZona . ' - ' . $zone->nombreZona}}</option>
                                                <?php endif; ?>
                                                <!-- fin seleccion de alertas -->

                                                <!-- Finalizamos el FOREACH de Lista de Zonas -->  		
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endif; ?>
                                    <?php if (Helpers::validEmptyVar($response['mailpains'])) : ?>
                                        <optgroup label="MAILPLAN">
                                            <?php foreach ($response['mailpains'] as $mail) : ?>

                                                <!-- Si es una edición mostramos la alertas seleccionadas -->
                                                <?php if (isset($response['alert']->Detalle->ListaMailPlanIds)): ?>
                                                    <?=
                                                    Helpers::selectGroup($response['alert']->Detalle->ListaMailPlanIds
                                                            , $mail
                                                            , $mail
                                                            , 'M');
                                                    ?>                                                    
                                                <?php else: ?>
                                                    <option value="M|{{$mail}}">{{$mail}}</option>
                                                <?php endif; ?>
                                                <!-- fin seleccion de alertas -->

                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <?php if (isset($response['alert']->AlertaId)): ?>
                            <input type="hidden" name="id" value="{{$response['alert']->AlertaId}}" />
                        <?php endif; ?>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-5 col-md-7">
                                <button type="submit" class="btn bgmarca font-white">
                                    {{$txtButton}}
                                </button>                                
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
        </div>
    </div>
</div>

@section('scripts')
$(document).ready(function () {
    <!-- Si es principal muestra el panel de cargar imagen, orientación y ubicación -->
    <?php if(isset($response['alert']) && $response['alert']->AlertaPrincipal == 1) : ?>
    	$("#panelOri, #panelImg, #panelUbi").show();
    	$("#panelDesc").hide();
    <?php elseif(isset($response['alert']) && $response['alert']->AlertaPrincipal == 0) : ?>
        $("#panelOri, #panelImg, #panelUbi").hide();
        $("#panelDesc").show();
    <?php elseif(!isset($response['alert']->AlertaPrincipal)) : ?>
    	$("#panelOri, #panelImg, #panelUbi").hide();
    <?php else: ?>
    $("#panelOri, #panelImg, #panelUbi").show();
    $("#panelDesc").hide();
    <?php endif; ?>
    
    $('#principal').change(function () {
        if($(this).val() == '1'){
            $("#panelOri, #panelImg, #panelUbi").show('fade');
            $("#panelDesc").hide('fade');
            $("#orientacion").rules("add", "required");
            <?php if (!isset($response['alert']->Detalle->ImagenId) && empty($response['alert']->Detalle->ImagenId)) { ?>
        		$("#imagen").rules("add", "required");
            <?php } ?>
            $("#ubicacion").rules("add", "required");
            $("#descripcion").rules("remove", "required");
        }else{
            $("#panelOri, #panelImg, #panelUbi").hide('fade');
            $("#panelDesc").show('fade');
            $("#orientacion").rules("remove", "required");
            $("#imagen").rules("remove", "required");
            $("#ubicacion").rules("remove", "required");
            $("#descripcion").rules("add", "required");
        }
    });
});

$(".date").datepicker({
    autoclose: true,
    startDate: new Date(),
    format: "yyyy-mm-dd",
    language: 'es'
});

$.validator.addMethod("greaterThan", function (value, element) {
    var startdatevalue = $('.startdate').val();
    return Date.parse(startdatevalue) < Date.parse(value);
}, 'La fecha fin debe ser mayor que la fecha inicio.'
);

$.validator.addMethod("checkZone", function (value, element) {
	var zones = false;
	
	$.each(value, function(key, value) {
		console.log(value.substring(0, 1));
		//Si el prefijo es Z / Zona
        if(value.substring(0, 1) == 'Z' || value.substring(0, 1) == 'M') {
            zones = true;
            return;
        }
	});
	
	return zones;
	
}, 'Selecciona mínimo una zona ó mail plan.');    

$("#form_alertas").validate({
    errorElement: 'span', //default input error message container
    errorClass: 'help-block help-block-error', // default input error message class
    focusInvalid: false, // do not focus the last invalid input
    invalidHandler: function(form, validator) {
        if (!validator.numberOfInvalids())
            return;
        $('html, body').animate({
            scrollTop: $(validator.errorList[0].element).offset().top - 60
        }, 2000);
    },  
    messages: {
        titulo: {
            required: "Ingresa el título de la alerta.",
            maxlength: "El título debe contener máximo {0} carracteres."
        },
        descripcion: {
            required: "Ingresa la descripción de la aleta.",
            maxlength: "La descripción debe contener máximo {0} caracteres."
        },
        newAdviser: {
            required: "Selecciona si es para asesoras nuevas o antiguas."
        },
        feini: {
            required: "Selecciona una fecha." 
        },
        fefin: {
            required: "Selecciona una fecha.",            
        },
        'my_multi_select2[]' : {
        	required: "Selecciona mínimo una zona ó mail plan."
        },
        imagen: {
        	required: "Selecciona una imagen.",
        	extension: "La imagen debe ser .jpg ó jpeg"
    	},
    	url: {
            url: "Ingresa una url válida." 
        }
    },
    rules: {
        titulo: {
            required: true,
            maxlength: 30
        },
        descripcion: {
            required: true,
            maxlength: 800
        },
        newAdviser: {
            required: true
        },
        feini: {
            required: true
        },
        fefin: {
            required: true,
            greaterThan: true
        },
        'my_multi_select2[]': {
        	required: true,
        	checkZone: true
        },
        imagen: {
        	extension: "jpg|jpeg"
    	},
    	url: {
            url: true
        }
    },
    errorPlacement: function (error, element) { // render error placement for each input type
       if (element.parents('.mt-radio-list').size() > 0 || element.parents('.mt-checkbox-list').size() > 0) {
            if (element.parents('.mt-radio-list').size() > 0) {
                error.appendTo(element.parents('.mt-radio-list')[0]);
            }
            if (element.parents('.mt-checkbox-list').size() > 0) {
                error.appendTo(element.parents('.mt-checkbox-list')[0]);
            }
        } else if (element.parents('.mt-radio-inline').size() > 0 || element.parents('.mt-checkbox-inline').size() > 0) {
            if (element.parents('.mt-radio-inline').size() > 0) {
                error.appendTo(element.parents('.mt-radio-inline')[0]);
            }
            if (element.parents('.mt-checkbox-inline').size() > 0) {
                error.appendTo(element.parents('.mt-checkbox-inline')[0]);
            }
        } else if (element.parent(".input-group").size() > 0) {
            error.insertAfter(element.parent(".input-group"));
        } else if (element.attr("data-error-container")) { 
            error.appendTo(element.attr("data-error-container"));
        } else {
            error.insertAfter(element); // for other inputs, just perform default behavior
        }
    },
    highlight: function (element) { // hightlight error inputs
        $(element)
            .closest('.form-group').addClass('has-error'); // set error class to the control group
    },
    unhighlight: function (element) { // revert the change done by hightlight
        $(element)
            .closest('.form-group').removeClass('has-error'); // set error class to the control group
    },
    success: function (label) {
        label
            .closest('.form-group').removeClass('has-error'); // set success class to the control group
    },
});
@endsection

@section('jsfiles')
<script src="{{ URL::asset('metronic/global/plugins/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/jquery-multi-select/js/jquery.multi-select.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/select2/js/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/pages/scripts/components-multi-select.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/jquery-validation/js/jquery.validate.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/jquery-validation/js/additional-methods.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js')}}" type="text/javascript"></script>
@endsection