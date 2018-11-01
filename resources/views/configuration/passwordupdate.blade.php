@extends('layouts.metronic.main', [
'title' => 'Cambiar Clave',
'controller' => null,
'view' => 'Cambiar Clave'
])

@section('pagetitle', 'Cambiar Clave')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-form bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-key font-red"></i>
                    <span class="caption-subject font-red sbold uppercase"></span>
                </div>  
                <div class="actions">
                    <a href="{{route('Inicio')}}" class="btn btn-default btn-circle btn-sm" title="Volver">
                        <i class="fa fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <!-- BEGIN FORM-->
                <form action="{{ route('EditarClaveAsesora') }}" method="POST" id="form_clients" class="form-horizontal" autocomplete="off">
                    {{ csrf_field() }}
                    <div class="form-body">
                    	<input type="hidden" value="{{ $response['checkPassword'] }}" name="checkPassword" id="checkPassword">
                    	
                        <div class="form-group">
                        	<label class="control-label col-md-3">
                                Contrase&ntilde;a Actual
                            </label>
                        	<div class="col-md-4">
                                <input type="password" name="currentpassword" id="current" class="form-control" placeholder="Contrase&ntilde;a Actual" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                        	<label class="control-label col-md-3">
                                Contrase&ntilde;a
                            </label>
                        	<div class="col-md-4">
                                <input type="password" name="password" id="password" class="form-control" placeholder="Nueva Contrase&ntilde;a" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                        	<label class="control-label col-md-3">
                                Confirmar Contrase&ntilde;a
                            </label>
                        	<div class="col-md-4">
                                <input type="password" name="repeatpassword" id="repeatpassword" class="form-control" placeholder="Confirmar Contrase&ntilde;a" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn bgmarca font-white">
                                    Guardar
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

@endsection

@section('scripts')

$("#form_clients").validate({
    errorElement: 'span', //default input error message container
    errorClass: 'help-block help-block-error', // default input error message class
    focusInvalid: false, // do not focus the last invalid input
    messages: {
        password: {
            required: "Ingresa tú nueva contraseña",
            maxlength: "Tú contraseña debe contener máximo {0} caracteres."
        },
        repeatpassword: {
            required: "Confirma tú nueva contraseña.",
            maxlength: "Tú contraseña debe contener máximo {0} caracteres.",
            equalTo: "Tú contraseña no es igual a la digitada."
        },
        currentpassword: {
            required: "Ingresa tú contraseña actual.",
            maxlength: "Tú contraseña actual debe contener máximo {0} caracteres.",
            equalTo: "Tú contraseña actual es incorrecta."
        }
    },
    rules: {
        password: {
            required: true,
            maxlength: 30
        },
        repeatpassword: {
            required: true,
            maxlength: 30,
            equalTo: "#password"
        },
        currentpassword: {
            required: true,
            maxlength: 30,
            equalTo: "#checkPassword"
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
<script src="{{ URL::asset('metronic/global/plugins/jquery-validation/js/jquery.validate.js') }}" type="text/javascript"></script>
@endsection