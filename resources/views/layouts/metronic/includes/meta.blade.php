<?php
$data = session()->get('SessionObject');
$marcaActual = (isset($data[0]['datosMarca']['marcaActual'])) ? $data[0]['datosMarca']['marcaActual'] : 'pcfk';
?>
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8" />
    <title>@yield('pagetitle')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="Pagina Pedidos Web" name="description" />
    <meta content="" name="author" />
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <?php if ($marcaActual == 'carmel'): ?>
        <link href="http://fonts.googleapis.com/css?family=Karla:400,400i,700,700i" rel="stylesheet" /> 
        <link href="https://fonts.googleapis.com/css?family=Lora" rel="stylesheet" /> 
        <link href="https://fonts.googleapis.com/css?family=Karla:400,700" rel="stylesheet" /> 
    <?php elseif ($marcaActual == 'pcfk'): ?>
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="http://fonts.googleapis.com/css?family=Roboto:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
    <?php else: ?>
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
    <?php endif; ?>
    <link href="{{ URL::asset('metronic/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('metronic/global/plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('metronic/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="{{ URL::asset('metronic/global/css/components.min.css') }}" rel="stylesheet" id="style_components" type="text/css" />
    <link href="{{ URL::asset('metronic/global/css/plugins.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- END THEME GLOBAL STYLES -->

    <!-- BEGIN THEME MARCA STYLES -->    
    <link href="{{ URL::asset('metronic/custom-pedidos-web/css/'.strtolower($marcaActual).'.css') }}" rel="stylesheet" type="text/css" id="style_color" />
    <!-- END THEME MARCA STYLES -->

    <!-- BEGIN THEME LAYOUT STYLES -->
    <link href="{{ URL::asset('metronic/layouts/layout/css/layout.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- END THEME LAYOUT STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{ URL::asset('metronic/global/plugins/bootstrap-sweetalert/sweetalert.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="{{ URL::asset('metronic/pages/css/profile.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL STYLES -->
    <link href="{{ URL::asset('metronic/global/plugins/bootstrap-toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('metronic/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
    <!-- BEGIN CUSTOM STYLES IN VIEWS -->
    @yield('cssfiles')
</head>
<!-- END HEAD -->