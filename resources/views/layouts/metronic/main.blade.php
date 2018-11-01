<!DOCTYPE html>
<?php
$data = session()->get('SessionObject');
$marcaActual = (isset($data[0]['datosMarca']['marcaActual'])) ? $data[0]['datosMarca']['marcaActual'] : 'pcfk';
?>
<!-- 
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.7
Version: 4.7.1
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
Renew Support: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->

    @include('layouts.metronic.includes.meta')

    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-sidebar-fixed page-boxed">
    
        <div id="overlay" style="display: none"></div>  
        <div id="loadingSaveOrder" style="display: none">
            <span>
                <img src="{{ URL::asset('metronic/custom-pedidos-web/img/loader-gif-6.gif') }}" style="width:110px;" />
            </span>
        </div>
        <div class="page-wrapper">

            @include('layouts.metronic.includes.header')            

            <!-- BEGIN HEADER & CONTENT DIVIDER -->
            <div class="clearfix"> </div>
            <!-- END HEADER & CONTENT DIVIDER -->

            <!-- BEGIN CONTAINER -->
            @include('layouts.metronic.includes.container')
            <!-- END CONTAINER -->

            <!-- BEGIN FOOTER -->
            @include('layouts.metronic.includes.footer')
            <!-- END FOOTER -->

        </div>

        <!--[if lt IE 9]>
        <script src="{{ URL::asset('metronic/global/plugins/respond.min.js') }}"></script>
        <script src="{{ URL::asset('metronic/global/plugins/excanvas.min.js') }}"></script> 
        <script src="{{ URL::asset('metronic/global/plugins/ie8.fix.min.js') }}"></script> 
        <![endif]-->

        <!-- BEGIN CORE PLUGINS -->
        <script src="{{ URL::asset('metronic/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('metronic/global/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('metronic/global/plugins/js.cookie.min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('metronic/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->

        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="{{ URL::asset('metronic/global/scripts/app.min.js') }}" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->

        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <script src="{{ URL::asset('metronic/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
        <script src="{{ URL::asset('metronic/custom-pedidos-web/scripts/scripts.js') }}" type="text/javascript"></script>
        <!-- END THEME LAYOUT SCRIPTS -->

        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="{{ URL::asset('metronic/global/plugins/bootstrap-sweetalert/sweetalert.min.js') }}" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->

        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="{{ URL::asset('metronic/global/plugins/bootstrap-confirmation/bootstrap-confirmation2.min.js') }}" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->

        <script src="{{ URL::asset('metronic/global/plugins/bootstrap-toastr/toastr.min.js')}}" type="text/javascript"></script>
        <script src="{{ URL::asset('metronic/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js')}}" type="text/javascript"></script>
        
        <!-- SE CARGA EL ARCHIVO DE CONFIGURACIÓN DE LA MARCA -->
        <script src="{{ URL::asset('metronic/custom-pedidos-web/scripts/config/'.strtolower($marcaActual).'.js') }}" type="text/javascript"></script>
        <!-- SE CARGA EL ARCHIVO DE UTILIDADES -->
        <script src="{{ URL::asset('metronic/custom-pedidos-web/scripts/helpers.js') }}" type="text/javascript"></script>  

        <!-- BEGIN CUSTOM JS IN VIEWS -->
        @yield ('jsfiles')
        <script type="text/javascript">
        <!-- Carrusel de autenticación -->
        jQuery(document).ready(function () {
			//Se carga el boton del chat
            helpers.loadChat();
            //Se carga el icono de la barra de titulo
            helpers.loadIcon();  
        });
        
        /**
         * CARGAR DATOS DIRECTORA
         */
        <?php if (App\Util\Helpers::isAdviser()): ?>
            findZoneDirector('<?= route("Directora"); ?>');
        <?php else: ?>
            $("#blockDirector").hide();
        <?php endif; ?>

        @yield ('scripts')
        @yield ('messageScripts')
        </script>
        <!-- END CUSTOM JS IN VIEWS -->

    </body>
</html>