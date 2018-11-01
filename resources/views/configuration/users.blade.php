<?php
use App\Util\Helpers;
?>
@extends('layouts.metronic.main', [
'title' => 'Usuarios y Clientes',
'controller' => null,
'view' => 'Usuarios y Clientes'
])

@section('pagetitle', 'Usuarios y Clientes')

@section('cssfiles')
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ URL::asset('metronic/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('metronic/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('metronic/custom-pedidos-web/css/dt-loading.css') }}" rel="stylesheet" type="text/css" />

<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content')
<div class="row">

    <div class="col-md-12">
    	<div id="filterAdvancedGroup" class="row margin-bottom-20 well hide" style="margin-left: 0px !important; margin-right: 0px !important;">
    		<form autocomplete="off" id="formAdvancedFilter" name="formAdvancedFilter" method="POST">
        		{{ csrf_field() }}
                        	
                <div class="col-md-4">
        			<h5>Mail plan</h5>
                	<div class="input-group">
                		<span class="input-group-addon"><i class="fa fa-filter"></i></span>
                		<select class="form-control" name="filterMailPlan" id="filterMailPlan">  
                			@if(isset($response['showFilter']) && $response['showFilter']) 
                				<option value="<?= base64_encode(str_replace("'", "", $response['mailPlan'])) ?>"><?= str_replace("'", "", $response['mailPlan']) ?></option>
                			@endif	
            				<option value="">--</option>
                				
                			@if(isset($response['mailPlanList'])) 
                                @foreach ($response['mailPlanList'] as $value) 
                              		<option value="<?= base64_encode($value)?>"><?= $value ?></option>
                                @endforeach  
                            @endif
                		</select>	
                	</div>
            	</div>
                        	
            	<div class="col-md-4">
        			<h5>Cédula asesora</h5>
                	<div class="input-group">
                		<span class="input-group-addon"><i class="fa fa-filter"></i></span>
                		<input type="text" placeholder="Cédula asesora" class="form-control" name="filterUserDocument" id="filterUserDocument"> 
                	</div>
            	</div>	
            	
            	<div class="col-md-2" style="top: 35px !important;">
                	<div class="input-group">
                		<a class="btn btn-transparent bgmarca font-white btn-sm btn-circle btn-courseorders" onclick="filterUsers();"><i class="fa fa-search"></i> Buscar</a>	
                	</div>
            	</div>
    		</form>   
    	</div> 
    </div>

    <div class="col-md-12">
        <div class="portlet" id="porletHistoryOrders">            
            <div class="portlet-body">                
                <div>
                    <table class="table table-hover table-light table-checkable order-column table-condensed" id="tableUsers">
                        <thead>
                            <tr>
                                <th class="dt-center"> USUARIO </th>
                                <th class="dt-center"> NOMBRE </th>
                                <th class="dt-center"> PERFIL </th>
                                <th class="dt-center"> ZONA </th>
                                <th class="dt-center"> ESTADO </th>
                                <?php if(Helpers::isAdministrator()) : ?>
                                <th class="dt-center"> ACCIONES </th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>  
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

@if(isset($response['showFilter']) && $response['showFilter'])  
	$('#filterAdvancedGroup').removeClass('hide');  
@endif

$('#tableUsers').DataTable({   
    "iDisplayLength" : 10,
    "aLengthMenu": [[10, 20, 30, 50, 100, -1], [10, 20, 30, 50, 100, "Todos"]],
    "processing": true,
    "bServerSide": true,
    "sAjaxSource": "{!! $response["route"] !!}",
    "aoColumnDefs": [ 
      	{
      		"mData": "Usuario",
      		"width": "15%",
      		"className": "dt-left",
      		"aTargets": [0]
  		},
      	{
      		"mData": "Nombre",
      		"width": "40%",
        	"aTargets": [1]
    	},
        {
        	"mData": "NombrePerfil",
        	"aTargets": [2]
    	},
        {
        	"mData": "CodigoZona",
        	"className": "dt-center",
        	"aTargets": [3]
    	},
        {
        	"mData": "Estado",
        	"className": "dt-center",
        	"mRender": function(resource) {
      			return resource === 0 ? '<span class="font-marca">Activo</span>' : '<span>Inactivo</span>';	
        	},
        	"aTargets": [4]
    	},
    	@if(Helpers::isAdministrator())
		{
        	"mData": "Estado",
        	"className": "dt-center",
        	"orderable": false,
        	"mRender": function(resource, display, data) {
        		var route = '{{ route("DetalleUsuario", ["document" => ""]) }}' + '/' + data.Usuario;  
      			return '<a href="' + route + '" class="btn btn-outline btn-xs bgmarca font-white btn-circle"><i class="fa fa-eye"></i> Ver</a>';	
        	},
        	"aTargets": [5]
    	}    	
    	@endif
    	
    ],
    "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
        $(nRow).addClass('odd gradeX');
        return nRow;
    },
    "fnServerData": function(sUrl, aoData, fnCallback, oSettings) {  
    	$('#tableUsers_filter').css('margin-top', '-20px');
    	$('#tableUsers_filter').children().hide();
    	$('#tableUsers_filter').append(
    		'<br/>' + 
    		'<a id="label-filter" class="font-dark" onclick="$(\'#filterAdvancedGroup\').removeClass(\'hide\')">' +
    			'<h6 class="text-center bold" style="margin-left: 50px !important;"><i class="fa fa-filter"></i> Búsqueda avanzada</h6>' +
    		'</a>' 
    	);  

        var oParams = {};
        $.each(aoData, function (i, value) {
            oParams[value.name] = value.value;
        });

        var data = {
            "$format": "json"
        };

        // If OData service is placed on the another domain use JSONP.
        var bJSONP = oSettings.oInit.bUseODataViaJSONP;

        if (bJSONP) {
            data.$callback = "odatatable_" + (oSettings.oFeatures.bServerSide ? oParams.sEcho : ("load_" + Math.floor((Math.random() * 1000) + 1)));
        }

        $.each(oSettings.aoColumns, function (i, value) {
            var sFieldName = (value.sName !== null && value.sName !== "") ? value.sName : ((typeof value.mData === 'string') ? value.mData : null);
            if (sFieldName === null || !isNaN(Number(sFieldName))) {
                sFieldName = value.sTitle;
            }
            if (sFieldName === null || !isNaN(Number(sFieldName))) {
                return;
            }
            if (data.$select == null) {
                data.$select = sFieldName;
            } else {
                data.$select += "," + sFieldName;
            }
        });

        if (oSettings.oFeatures.bServerSide) {

            data.$skip = oSettings._iDisplayStart;
            if (oSettings._iDisplayLength > -1) {
                data.$top = oSettings._iDisplayLength;
            }

            // OData versions prior to v4 used $inlinecount=allpages; but v4 is uses $count=true
            if (oSettings.oInit.iODataVersion !== null && oSettings.oInit.iODataVersion < 4) {
                data.$inlinecount = "allpages";
            } else {
                data.$count = true;
            }

            var asFilters = [];
            var asColumnFilters = []; //used for jquery.dataTables.columnFilter.js
            $.each(oSettings.aoColumns,
                function (i, value) {

                    var sFieldName = value.sName || value.mData;
                    var columnFilter = oParams["sSearch_" + i]; //fortunately columnFilter's _number matches the index of aoColumns

                    if ((oParams.sSearch !== null && oParams.sSearch !== "" || columnFilter !== null && columnFilter !== "") && value.bSearchable) {
                        switch (value.sType) {
                        case 'string':
                        case 'html':

                            /*if (oParams.sSearch !== null && oParams.sSearch !== "")
                            {
                                // asFilters.push("substringof('" + oParams.sSearch + "', " + sFieldName + ")");
                                // substringof does not work in v4???
                                asFilters.push("indexof(tolower(" + sFieldName + "), '" + oParams.sSearch.toLowerCase() + "') gt -1");
                            }

                            if (columnFilter !== null && columnFilter !== "") {
                                asColumnFilters.push("indexof(tolower(" + sFieldName + "), '" + columnFilter.toLowerCase() + "') gt -1");
                            }*/
                            break;

                        case 'date':
                        case 'numeric':
                            var fnFormatValue = 
                                (value.sType == 'numeric') ? 
                                    function(val) { return val; } :
                                    function(val) { 
                                            // Here is a mess. OData V2, V3, and V4 se different formats of DateTime literals.
                                            switch(oSettings.oInit.iODataVersion){
                                                    // V2 works with the following format:
                                                    // http://services.odata.org/V2/OData/OData.svc/Products?$filter=(ReleaseDate+lt+2014-04-29T09:00:00.000Z)                                                              
                                                    case 4: return (new Date(val)).toISOString(); 
                                                    // V3 works with the following format:
                                                    // http://services.odata.org/V3/OData/OData.svc/Products?$filter=(ReleaseDate+lt+datetimeoffset'2008-01-01T07:00:00')
                                                    case 3: return "datetimeoffset'" + (new Date(val)).toISOString() + "'";  
                                                    // V2 works with the following format:
                                                    // http://services.odata.org/V2/OData/OData.svc/Products?$filter=(ReleaseDate+lt+DateTime'2014-04-29T09:00:00.000Z')
                                                    case 2: return "DateTime'" + (new Date(val)).toISOString() + "'"; 
                                            }
                                    }

                            // Currently, we cannot use global search for date and numeric fields (exception on the OData service side)
                            // However, individual column filters are supported in form lower~upper
                            if (columnFilter !== null && columnFilter !== "" && columnFilter !== "~") {
                                asRanges = columnFilter.split("~");
                                if (asRanges[0] !== "") {
                                    asColumnFilters.push("(" + sFieldName + " gt " + fnFormatValue(asRanges[0]) + ")");
                                }

                                if (asRanges[1] !== "") {
                                    asColumnFilters.push("(" + sFieldName + " lt " + fnFormatValue(asRanges[1]) + ")");
                                }
                            }
                            break;
                        default:
                        }
                    }
                });

            if (asFilters.length > 0) {
                data.$filter = asFilters.join(" or ");
            }

            if (asColumnFilters.length > 0) {
                if (data.$filter !== undefined) {
                    data.$filter = " ( " + data.$filter + " ) and ( " + asColumnFilters.join(" and ") + " ) ";
                } else {
                    data.$filter = asColumnFilters.join(" and ");
                }
            }

            var asOrderBy = [];
            for (var i = 0; i < oParams.iSortingCols; i++) {
                asOrderBy.push(oParams["mDataProp_" + oParams["iSortCol_" + i]] + " " + (oParams["sSortDir_" + i] || ""));
            }

            if (asOrderBy.length > 0) {
                data.$orderby = asOrderBy.join();
            }
        }
        
        $.ajax(jQuery.extend({}, oSettings.oInit.ajax, {
            "url": sUrl,
            "data": data,
            "jsonp": bJSONP,
            "dataType": bJSONP ? "jsonp" : "json",
            "jsonpCallback": data["$callback"],
            "cache": false,
            "success": function (data) {
                var oDataSource = {};

                // Probe data structures for V4, V3, and V2 versions of OData response
                oDataSource.aaData = data.value || (data.d && data.d.results) || data.d;
                var iCount = (data["@odata.count"]) ? data["@odata.count"] : ((data["odata.count"]) ? data["odata.count"] : ((data.__count) ? data.__count : (data.d && data.d.__count)));

                if (iCount == null) {
                    if (oDataSource.aaData.length === oSettings._iDisplayLength) {
                        oDataSource.iTotalRecords = oSettings._iDisplayStart + oSettings._iDisplayLength + 1;
                    } else {
                        oDataSource.iTotalRecords = oSettings._iDisplayStart + oDataSource.aaData.length;
                    }
                } else {
                    oDataSource.iTotalRecords = iCount;
                }

                oDataSource.iTotalDisplayRecords = oDataSource.iTotalRecords;

                fnCallback(oDataSource);
            }
        }));

    },
    "iODataVersion": 4,
    "bUseODataViaJSONP": false,
    "language": {
		"url": "../metronic/custom-pedidos-web/scripts/Spanish.json"
	},
	"initComplete": function(settings, json) {
	
	}
});

});

function filterUsers() {
	var mailPlan = null;
	var userDocument = null;
	
	if($('#filterMailPlan').val() !== '' || $('#filterMailPlan').val() !== null) {
		mailPlan = $('#filterMailPlan').val();  
	}
	
	if($('#filterUserDocument').val() !== '' || $('#filterUserDocument').val() !== null) {
		userDocument = $('#filterUserDocument').val();
	}
	
	var route = "{{route('Usuarios')}}";

	$('#formAdvancedFilter').attr('action', route);
	$('#formAdvancedFilter').submit();    
}   

@endsection

@section('jsfiles')
<script src="{{ URL::asset('metronic/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('metronic/global/plugins/datatables/jquery.dataTables.odata.js') }}" type="text/javascript"></script>

@endsection