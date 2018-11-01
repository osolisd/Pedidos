@extends('layouts.metronic.main', [
'title' => 'Editar zonas',
'controller' => null,
'view' => 'Editar zonas'
])

@section('pagetitle', 'Editar zonas')

@section('cssfiles')
<link href="{{ URL::asset('metronic/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<?php //var_dump($response); ?>
<form class="horizontal-form" method="POST" action="{{route('EditarZona')}}" id="formZones">
    {{ csrf_field() }}
    <div class="portlet">
        <div class="portlet-title">
            <div class="caption font-marca bold">
                <i class="icon-check font-marca bold"></i>Campa&ntilde;as y zonas 
            </div>
            <div class="actions">
                <button class="btn btn-transparent font-white btn-circle" type="submit" style="background-color: #575656">
                    <i class="icon-note"></i>
                    Guardar
                </button>
            </div>
        </div>
        <div class="portlet-body">
            <div class="row margin-bottom-20 well">
                <div class="col-md-4">
                    <label>Buscar</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="icon-magnifier "></i></span>
                        <input class="form-control" type="text" id="searchZones" placeholder="Buscar..." />
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button" id="emptySearch" >Limpiar</button>
                        </span>
                    </div>
                </div>
                <!-- Selector Mail Plan -->
                <div class="col-md-2">
                    <label>Mail Plan</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="icon-magnifier "></i></span>
                        <select id="searchMailPlan" class="form-control">
                            <option value="">--</option>
                            @foreach($response['mailPlan'] as $mailPlan)
                            <option value="{{ trim($mailPlan) }}">{{ trim($mailPlan) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <!-- Selector para activar / desactivar zonas -->
                <div class="col-md-2">
                    <label>Activar Zona</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="icon-check"></i></span>
                        <!--  Opcion 1 con el switch -->
<!--                 		<input type="checkbox"  -->
                        <!--                                class="make-switch"  -->
                        <!--                                data-on-color="jungle"  -->
                        <!--                                data-off-color="danger"  -->
                        <!--                                data-on-text="SI" -->
                        <!--                                data-off-text="NO" -->
                        <!--                                name="activarmasiva" -->
                        <!--                                id="activarmasiva"> -->

                        <!-- Opcion 2 con un combo -->
                        <select id="activarmasiva" class="form-control">
                            <option value="">--</option>
                            <option value="true">SI</option>
                            <option value="false">NO</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <label>Cambiar Campa&ntilde;a</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="icon-note"></i></span>
                        <input class="form-control" type="text" placeholder="Cambiar campa&ntilde;a" name="campanamasiva" id="campanamasiva" />
                        <span class="input-group-btn">
                            <button class="btn bgmarca font-white" type="button" id="accionesMasivas" >Aplicar</button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="row panelzonas">
                <?php $i = 1; ?>
                @foreach($response['zones'] as $zonas)
                <div class="col-md-4 panel">
                    <div class="form-group">
                        <label class="control-label col-md-12 bold theZones">
                            <span>{{trim($zonas->codZona) .' - ' .trim($zonas->nombreZona)}}</span>
                        </label>
                        <input type="hidden" class="MailPlan" value="{{trim($zonas->mailPlan)}}" />
                    </div>
                    <div class="form-group">
                        <div class="col-md-8 block-campana">
                            <input type="hidden" name="Zonas[{{$i}}][codZona]" value="{{trim($zonas->codZona)}}" />
                            <input class="form-control inputcampana" 
                                   placeholder="Campa&ntilde;a" 
                                   type="text" 
                                   value="{{trim($zonas->campana)}}"
                                   name="Zonas[{{$i}}][campana]"
                                   >
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-4" style="padding-left: 0">
                            <input type="checkbox" 
                                   class="make-switch checkcampana" 
                                   <?= ($zonas->estado === true) ? 'checked' : ''; ?>
                                   data-on-color="info" 
                                   data-off-color="default" 
                                   data-on-text="SI"
                                   data-off-text="NO"
                                   name="Zonas[{{$i}}][estado]"
                                   >
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <label class="mt-checkbox">
                                <input type="checkbox" 
                                       class=""
                                       <?= ($zonas->repetirCampana === true) ? 'checked' : ''; ?>
                                       name="Zonas[{{$i}}][repetirCampana]"
                                       > Repetir campa&ntilde;a
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>

                <?php $i++; ?>
                @endforeach
            </div>
        </div>
    </div>
</form>

@endsection

@section('scripts')
jQuery(document).ready(function () {
$("#formZones").validate();
$('.inputcampana').each(function () {
var year1 = $(this).val().toString().trim().substring(0,4);
var year2 = $(this).val().toString().trim().substring(4,6);
$(this).rules('add', {
required: true,
number: true,
pattern: /^(20[12][0-9]|2030)(\d{2})$/, //primeros 4 digitos entre 2010 y 2030
messages: {// optional custom messages
required: "Debe ingresar una campa&ntilde;a.",
number: "La campa&ntilde;a debe ser num&eacute;rica.",
pattern: "Formato de campa&ntilde;a inv&aacute;lido."
}
});
});
});
@endsection

@section('jsfiles')
<script src="{{ URL::asset('metronic/global/plugins/jquery-validation/js/jquery.validate.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/jquery-validation/js/additional-methods.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}" type="text/javascript"></script>
@endsection