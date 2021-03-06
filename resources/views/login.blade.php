<?php
$marca = \App\Util\Helpers::loginBrand($response['marca']);
$ObjGuest = new App\Http\Controllers\Pedidos\GuestController();
$banners = $ObjGuest->findImagesUrl($response['marca']);   
?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <head>
        <meta charset="utf-8" />
        <meta name="csrf_token" content="{ csrf_token() }" />  
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="Pagina Pedidos Web" name="description" />
        <meta content="" name="author" />        
        <?php if (strtolower($response['marca']) == 'carmel'): ?>
            <link href="http://fonts.googleapis.com/css?family=Karla:400,400i,700,700i" rel="stylesheet" /> 
            <link href="https://fonts.googleapis.com/css?family=Lora" rel="stylesheet" /> 
            <link href="https://fonts.googleapis.com/css?family=Karla:400,700" rel="stylesheet" /> 
        <?php elseif (strtolower($response['marca']) == 'pcfk'): ?>
            <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
            <link href="http://fonts.googleapis.com/css?family=Roboto:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <?php else: ?>
            <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <?php endif; ?>
        <link href="{{ URL::asset('metronic/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ URL::asset('metronic/global/plugins/bootstrap-sweetalert/sweetalert.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ URL::asset('metronic/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />        
        <link href="{{ URL::asset('metronic/global/css/components.min.css') }}" rel="stylesheet" id="style_components" type="text/css" />        
        <link href="{{ URL::asset('metronic/pages/css/login-5.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ URL::asset('metronic/global/plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ URL::asset('metronic/custom-pedidos-web/css/'.strtolower($response['marca']).'.css') }}" rel="stylesheet" type="text/css" id="style_color" />
    </head>    
    <body class="login">
        <div class="user-login-5">
            <div class="row bs-reset">
                <div class="col-md-4 login-container bs-reset">                    
                    <div class="login-content">
                        <div class="text-center">
                            <img class="text-center {{$marca['class']}}" 
                                 src="{{ URL::asset('metronic/custom-pedidos-web/img/' . $marca['logo']) }}" 
                                 style="width: {{$marca['width']}}" />
                        </div>
                        <h3 class="bold login-intro text-center font-marca uppercase">Portal asesora</h3>
                        <p class="login-intro text-center"> 
                            En todas las campa&ntilde;as tendr&aacute;s la 
                            oportunidad de acumular puntos y ganar beneficios 
                            extras. 
                        </p>
                        <form action="{{route('AuthUser')}}" class="login-form" method="post" autocomplete="off">
                            {{ csrf_field() }}

                            {!! 
                            $errors->first('credentials', '
                            <div class="alert alert-danger">
                                <span class="has-error">:message</span>  
                            </div>
                            ')
                            !!}

                            <div class="alert alert-danger display-hide emptyform">
                                <button class="close" data-close="alert"></button>
                                <span>Ingrese usuario y contrase&ntilde;a. </span>
                            </div>
                            <div class="row">
                                <div class="col-md-10 col-md-offset-1">
                                    <input class="form-control form-control-solid placeholder-no-fix form-group" 
                                           type="text" 
                                           autocomplete="off" 
                                           placeholder="Nombre de usuario" 
                                           name="user" required autocomplete="off" /> 
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-10 col-md-offset-1">
                                    <input class="form-control form-control-solid placeholder-no-fix form-group" 
                                           type="password" 
                                           autocomplete="off" 
                                           placeholder="Contrase&ntilde;a" 
                                           name="password" required autocomplete="off" />
                                </div>
                            </div>
                            <div class="row">                                
                                <div class="col-md-12 text-center">                                    
                                    <button class="btn btn-{{$response['marca']}}" type="submit" style="">Ingresar</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="forgot-password text-center">
                                        <a href="javascript:;" id="forget-password" class="forget-password">
                                            Recuperar contrase&ntilde;a
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!-- BEGIN FORGOT PASSWORD FORM -->
                        <div class="forget-form">
                            <h4>Recupera tu contrase&ntilde;a por:</h4>
                            <div class="tabbable-line">
                                <ul class="nav nav-tabs display-hide">
                                    <li class="active title-email">
                                        <a href="#tab_correo" data-toggle="tab" aria-expanded="true">
                                            <i class="icon-envelope"></i>
                                            <span class="hidden-xs">
                                                Correo electrónico
                                            </span> 
                                        </a>
                                    </li>
                                    <li class="title-question display-hide">
                                        <a href="#tab_preguntas" data-toggle="tab" aria-expanded="false">
                                            <i class="icon-question"></i>
                                            <span class="hidden-xs">
                                                Responde unas preguntas
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="tab_correo">
                                        <div class="paso1correo">
                                            <p> Ingresa tu documento de identidad. </p>
                                            <form action="javascript:;" class="formuser" method="post" autocomplete="off">
                                                <div class="form-group">
                                                    <input class="form-control placeholder-no-fix" 
                                                           type="text" 
                                                           autocomplete="off" 
                                                           placeholder="Documento" 
                                                           name="documento" 
                                                           id="documento"
                                                           required />
                                                </div>
                                                <div class="form-actions">
                                                    <button type="button" id="back-btn" class="btn btn-{{$response['marca']}} btn-outline">Atr&aacute;s</button>
                                                    <button type="submit" id="buscar-doc" class="btn btn-{{$response['marca']}} uppercase pull-right">
                                                    	<i class="fa fa-circle-o-notch fa-spin fa-fw" style="display: none;"></i> Buscar
                                                	</button>
                                                </div>
                                            </form>

                                        </div>
                                        <div class="paso2correo display-hide">
											<input type="hidden" id="key">                                        
                                            <p> El correo eletr&oacute;nico que tenemos asociado a tu cuenta es:.</p>
                                            <p id="emailAdviser" class="bold font-dark" style="word-wrap: break-word"></p>
                                            <p>Si este es tú correo, presione el botón enviar y se te enviar&aacute; una nueva contrase&ntilde;a</p>
                                            <label id="errorSendEmail" class="control-label text-danger"></label>
                                            <div class="form-actions">
                                                <button type="button" id="back-btn" class="btn btn-{{$response['marca']}} btn-outline">Atr&aacute;s</button>
                                                <button type="button" id="btn-send-email" class="btn btn-{{$response['marca']}} uppercase pull-right">
                                                	<i class="fa fa-circle-o-notch fa-spin fa-fw" style="display: none;"></i> Enviar
                                            	</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_preguntas">
                                    	<div class="preload display-hide" style="right: 45%; bottom: 35%; z-index: 99; position: absolute;">
                                    		<i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
											<span class="sr-only">Loading...</span>
										</div>
                                    
                                        <div class="paso1pregunta display-hide">
                                            <form class="formupreguntas" action="javascript:;" method="post" role="form">
                                                <p> Responde las siguientes preguntas para recuperar tú contrase&ntilde;a. </p>
                                                <label id="errorQuestion" class="control-label text-danger"></label>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="bold txtpregunta">Nombres</label>
                                                            <div class="mt-radio-list questionAdviserName" data-error-container="nombre_error">
                                                                <div id="nombre_error"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="bold txtpregunta">C&eacute;dula</label>
                                                            <div class="mt-radio-list questionAdviserDocument" data-error-container="cedula_error">
                                                                <div id="cedula_error"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="bold txtpregunta">Fecha Expedición</label>
                                                            <div class="mt-radio-list questionAdviserDocumentDate" data-error-container="nacimiento_error">
                                                                <div id="nacimiento_error"></div>
                                                            </div>
                                                        </div>
                                                    </div>  
                                                </div>
                                                
                                                <div class="form-actions">
                                                    <button type="button" id="back-btn3" class="btn btn-{{$response['marca']}} btn-outline">Atr&aacute;s</button>
                                                    <button type="submit" id="check-question" class="btn btn-{{$response['marca']}} uppercase pull-right">
                                                    	<i class="fa fa-circle-o-notch fa-spin fa-fw" style="display: none;"></i> Enviar
                                                	</button>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="paso2pregunta display-hide">
                                            <form class="formnuevaclave" action="javascript:;" method="post" role="form">
                                                <div class="form-group">
                                                    <input class="form-control "  
                                                           name="nuevaclave" 
                                                           id="nuevaclave" 
                                                           aria-required="true" 
                                                           type="password" 
                                                           placeholder="Nueva contraseña">
                                                </div>
                                                <div class="form-group">
                                                    <input class="form-control"  
                                                           name="repetirclave" 
                                                           id="repetirclave" 
                                                           aria-required="true" 
                                                           type="password" 
                                                           placeholder="Repetir contraseña">                                                    
                                                </div>
                                                
                                                <label id="errorRecoveryPassword" class="control-label text-danger"></label>
                                                
                                                <div class="form-actions">
                                                    <button type="button" id="back-btn2" class="btn btn-{{$response['marca']}} btn-outline">Atr&aacute;s</button>
                                                    <button type="submit" id="btn-recovery-password" class="btn btn-{{$response['marca']}} uppercase pull-right">
                                                    	<i class="fa fa-circle-o-notch fa-spin fa-fw" style="display: none;"></i> Enviar
                                                	</button>
                                                </div>
                                                
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END FORGOT PASSWORD FORM -->
                    </div>
                    <div class="login-footer">
                        <div class="row bs-reset">                            
                            <div class="col-md-10 col-md-offset-1">
                                <div class="login-copyright text-center">
                                    <p>TODOS LOS DERECHOS RESERVADOS CARMEL - PACIFIKA - LOGUIN {{date('Y')}}</p>
                                    <p>Marcas unidas de L&iacute;nea Directa S.A.S</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Div Carrusel -->
                <div class="col-md-8 bs-reset">
                    <div class="login-bg"> </div>
                </div>
            </div>
        </div>
        <!--[if lt IE 9]>
        <script src="{{ URL::asset('metronic/global/plugins/respond.min.js') }}"></script>
        <script src="{{ URL::asset('metronic/global/plugins/excanvas.min.js') }}"></script> 
        <script src="{{ URL::asset('metronic/global/plugins/ie8.fix.min.js') }}"></script> 
        <![endif]-->
        <script src="{{ URL::asset('metronic/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('metronic/global/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('metronic/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('metronic/global/plugins/backstretch/jquery.backstretch.min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('metronic/pages/scripts/login-5.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('metronic/custom-pedidos-web/scripts/config/'.strtolower($response['marca']).'.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('metronic/custom-pedidos-web/scripts/helpers.js') }}" type="text/javascript"></script>  
        <script src="{{ URL::asset('metronic/global/plugins/bootstrap-sweetalert/sweetalert.min.js') }}" type="text/javascript"></script>
        <script type="text/javascript">
        	var checkDocumentRoute = '<?= route('ValidarDocumentoAsesora'); ?>';
        	var findQuestionsRoute = '<?= route('PreguntaAsesora'); ?>';
        	var checkQuestionsRoute = '<?= route('ValidarPreguntaAsesora'); ?>';
        	var sendEmailRoute = '<?= route('EnviarClaveAsesora'); ?>';
        	var recoveryPasswordRoute = '<?= route('RecuperarClaveAsesora'); ?>';
        	var intents = 1;
        
        	<!-- Carrusel de autenticación -->
            jQuery(document).ready(function () {
                $('.login-bg').backstretch(<?= json_encode($banners['images']); ?>, {
                    fade: 1000,
                    duration: 2000
                });

				//Se carga el boton del chat
                helpers.loadChat();
                //Se carga el icono de la barra de titulo
                helpers.loadIcon();
                //Se carga el titulo de la ventana
                helpers.loadTitle();
            });
        </script>
    </body>
</html>