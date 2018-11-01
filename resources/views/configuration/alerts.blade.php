@extends('layouts.metronic.main', [
'title' => 'Alertas',
'controller' => null,
'view' => 'Alertas'
])

@section('pagetitle', 'Alertas')

@section('cssfiles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ URL::asset('metronic/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('metronic/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title" style="border-top: 0;">
                <div class="caption font-dark">
                    <i class="icon-bell font-dark"></i>
                    <span class="caption-subject bold uppercase"> Administrar alertas </span>
                </div>
                <div class="actions">
                    <a class="btn btn-transparent font-white btn-circle btn-sm btnnewped" href="{{ route('CrearAlerta') }}">
                        <i class="fa fa-plus"></i> Nueva alerta
                    </a>                    
                </div>
            </div>
            <div class="portlet-body">                
                <div>
                    <table class="table table-hover table-light table-checkable order-column table-condensed" id="tableAlerts">
                        <thead>
                            <tr>
                                <th> TITULO </th>
                                <th> PRINCIPAL </th>
                                <th> FECHA INICIO </th>
                                <th> FECHA FIN </th>
                                <th> ACCIONES </th>
                            </tr>
                        </thead>
                        <tbody>  
                            <?php if (isset($response['alerts'])) : ?>
                                <?php foreach ($response['alerts'] as $alert) : ?>
                                    <tr class="odd gradeX">                                
                                        <td> {{$alert->Titulo}} </td>
                                        <td class="text-center"> <?=
                                            ($alert->AlertaPrincipal == 1) ?
                                                    '<span class="font-marca">SI</span>' :
                                                    '<span>NO</span>'
                                            ?>
                                        </td>
                                        <td> {{substr($alert->FechaInicio, 0, 10)}} </td>
                                        <td> {{substr($alert->FechaFin, 0, 10)}}</td>
                                        <td>
                                            <a href="{{ route('DetalleAlerta', ['id' => $alert->AlertaId]) }}" 
                                               class="btn btn-outline btn-xs bgmarca font-white btn-circle">
                                                <i class="fa fa-pencil"></i> Editar
                                            </a>
                                            <a href="{{ route('EliminarAlerta', ['id' => $alert->AlertaId]) }}" 
                                               class="font-marca" 
                                               data-toggle="confirmation"
                                               data-original-title="¿Estás seguro que quieres borrar esta alerta?"
                                               title=""
                                               data-btn-ok-label="Si"
                                               data-btn-cancel-label="No">
                                                <i class="fa fa-trash-o"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
jQuery(document).ready(function () {

/**
* DATATABLE DE ORDENES
*/
$('#tableAlerts').dataTable({
"language": {
"url": "metronic/custom-pedidos-web/scripts/Spanish.json"
},
"iDisplayLength": 20,
"aLengthMenu": [[20, 30, 50, 100, -1], [20, 30, 50, 100, "Todos"]],
"order": [[ 0, "desc" ]]
});
});
@endsection

@section('jsfiles')
<script src="{{ URL::asset('metronic/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
@endsection