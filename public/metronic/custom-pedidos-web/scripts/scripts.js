/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function () {
	//Focus
	$('body').on('change', '#newplu', function () {
		setTimeout(function(){
			$('#newcantidad').trigger( "focus" );   
		}, 100);
	});
	
	$('body').on('keydown', '#newplu', function (event) {
		var key = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
		if (key == 9) {
			$('#newcantidad').trigger( "focus" );        
			return false;
		}
		return true;
	});
	//End Focus
	
    /**
     * AGREGAR PRODUCTO Y VALIDAR PLU Boton Nuevo Pedido Formulario
     */
    $('#addProduct').click(function (e) {
    	showMessage = true;
        //PLU NO DEBE SER VACIO
        if ($("#newplu").val() == '') {
        	//fUNCIÓN PARA MOSTRAR MENSAJE DE ERROR 
            showError('error', 'Debes ingresar un código de producto');
            //Limpia el campo autocompletar de plu
            $("#newplu").val(null).trigger("change");
            return;
        }
        //CANTIDAD NO PUEDE SER VACIA NI NEGATIVA
        if ($("#newcantidad").val() == '' || $("#newcantidad").val() <= 0) {
            showError('error', 'Debes ingresar una cantidad válida');
            $("#newplu").val(null).trigger("change");
            return;
        }
        
        //CANTIDAD NO PUEDE SUPERAR LA CANTIDAD PERMITIDA
        //@VAR CANTPERMITIDA SETEADA EN VISTA CREATE ORDER
        if (parseInt($("#newcantidad").val()) > parseInt(cantPermitida)) {
            showError('error', 'La cantidad no puede ser mayor a ' + cantPermitida);
            $("#newplu").val(null).trigger("change");
            return;
        }
        //SI TODO CUMPLE VALIDO EL PRODUCTO SELECCIONADO
        checkProduct(routeCheckProduct, $("#newplu").val(), $("#newcantidad").val());
    });

    /**
     * VALIDAR CANTIDADES ANTES DE ENVIAR EL PEDIDO BOTON CONFIRMAR
     */
    $('#formOrder').submit(function (e) {
        //DEBE INGRESAR AL MENOS UN PRODUCTO
        var total = $(".cantProd").length;
        if (total <= 0) {
            showError('error', 'Debes ingresar al menos un producto');
            e.preventDefault();
            return;
        }
        //VALIDO QUE LAS CANTIDADES NO ESTÉN VACIAS NI NEGATIVAS
        $(".cantProd").each(function (index) {
            if ($(this).val() == '' || $(this).val() <= 0) {
                showError('error', 'Debes ingresar cantidades válidas');
                e.preventDefault();
                return;
            }
        });
    });

    /**
     * MODAL CONFIRM PARA ELIMINAR PEDIDOS O PRODUCTOS
     */
    $('body').confirmation({
        placement: 'top',
        selector: '[data-toggle=confirmation]',
        btnCancelClass: 'btn btn-sm btn-default',
        btnOkClass: 'btn btn-sm btn-default'
    });

    /**
     * ELIMINAR PRODUCTO TEMPORALMENTE CUANDO SE DA EN EL BOTON CONFIRMAR DEL MODAL ELIMINAR EN PEDIDO
     */
    $('body').on('confirmed.bs.confirmation', '.rmvPd', function () {
        removeRow($(this));
    });
    
    
//    $('#newcantidad').keypress(function (e) {
//        if (e.key === '.' || e.key === ',') {
//        	var value = $(this).data('value');    
//        	$(this).val(value);   
//        	return false;
//        }
//        
//        return true;
//    });  
////    
//    function keyPress(val) {
//    	console.log($(this).val());
//    	return this.replace(/^\s+|\s+$/g, '');
//    }
    
    $('body').on('keydown', '#newcantidad', function (event) {
    	if(window.event){//asignamos el valor de la tecla a keynum
    		keynum = event.keyCode; //IE
    	} else{
    		keynum = event.which; //FF
    	} 
		
    	//comprobamos si se encuentra en el rango numérico y que teclas no recibirá.
    	if((keynum > 47 && keynum < 58) || keynum == 8 || keynum == 13 || keynum == 6 ){
    		return true;
    	} else{
    		//Focus
    		var key = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
    		if (key == 9) 
    		{
    			$('#newplu').trigger( "focus" );        
    			return false;
    		}
    		//End focus 
    		return false;
    	} 
    });
    
    $('body').on('keydown', '.cantProd', function (evt) {
    	if(window.event){//asignamos el valor de la tecla a keynum
    		keynum = evt.keyCode; //IE
    	} else{
    		keynum = evt.which; //FF
    	} 
		
    	//comprobamos si se encuentra en el rango numérico y que teclas no recibirá.
    	if((keynum > 47 && keynum < 58) || keynum == 8 || keynum == 13 || keynum == 6 ){
    		return true;
    	} else{
    		return false;
    	} 
    });
    
   /* $('.cantProd').keypress(function (evt) {
    	if(window.event){//asignamos el valor de la tecla a keynum
    		keynum = evt.keyCode; //IE
    	} else{
    		keynum = evt.which; //FF
    	} 
		
    	//comprobamos si se encuentra en el rango numérico y que teclas no recibirá.
    	if((keynum > 47 && keynum < 58) || keynum == 8 || keynum == 13 || keynum == 6 ){
    		return true;
    	} else{
    		return false;
    	} 
    	
//    	
//    	console.log(e);  
//    	alert();
//        if (e.key === '.' || e.key === ',') {
//        	var value = $(this).data('value');    
//        	$(this).val(value);   
//        	return false;
//        }
//        
//        return true;
    });  */

    /**
     * ACTUALIZAR TOTALES AL CAMBIAR LA CANTIDAD DEL PRODUCTO EN PEDIDO
     */
    $('body').on('change', '.cantProd', function () {
    	if($(this).attr('id') !== undefined && $(this).attr('id') !== null) {  
	    	var split = $(this).attr('id').split('-');   
	    	//Obtenemos el plu del atributo id y realizamos un split por - y capturamos la posición 1 ['plu', '123456']
	    	var plu = split[1];
	    	//Variable para identificar si se presenta algun error
	    	var isError = false;
	    	//Variable para capturar el objeto del paquete
	    	var object = findObjectPackage(plu);  
	    	
	    	console.log(object);
	        //Verificamos que el paquete sea diferente de vacio y de indefinido
	    	if(object !== null && object !== undefined) {
	    		//Verificamos que la cantidad no exceda la permitida por paquete
	        	if(parseInt($(this).val()) > object.maxQuantity) {
	        		//Mostramos mensaje de error
	        		showError('error', 'Solo puedes ingresar hasta ' + object.maxQuantity + ' paquetes.');
	        		//Agregamos la cantidad de la suscriptción
	        		$(this).val($(this).data('value'));
	        		//Retornamos para no ejecutar el codigo de verificar PLU
	        		isError = true;
	            }
	    		
	    		//Verificamos la cantidad del paquete no sea menor o igual a cero
	    		if(parseInt($(this).val()) <= 0) {
	    			//Mostramos mensaje de error
	        		showError('error', 'La cantidad que intentas intentas ingresar es incorrecta, por favor intenta números mayores o iguales a 1. Si desea eliminar tu suscripción por favor dirígete a la opción de paquete del ahorro.' );
	        		//Agregamos la cantidad de la suscriptción
	        		$(this).val(object.currentQuantity);
	        		//Retornamos para no ejecutar el codigo de verificar PLU
	        		isError = true;
	    		}
	    	}
	    	
	    	//Verificamos si existe algun error
	    	if(isError) {
	    		//Retornamos para que no se ejecute el resto de codigo
	    		return;
	    	}
	    	
	    	//Verificamos que no exista errores y el objeto contenga datos
	    	if(!isError && object !== null && object !== undefined) {
	    		//Actualizamos la suscripción al paquete
	    		updateSuscription(routeEditarSuscripcion, object.suscriptionId, object.packageId, object.campaingId, $(this).val());
	    	}
    	}

    	console.log('cant number -->: ' + parseInt($(this).val()));    
    	
    	//VALIDDAR EL VALOR DEL CAMPO NO SEA VACIO NI NEGATIVO
        if (parseInt($(this).val()) <= 0 || $(this).val() == '') {
            $(this).val($(this).data('value'));
            showError('error', 'Debes ingresar una cantidad válida');
            return;
        }
        
        if ($(this).val() % 1 !== 0) {
        	$(this).val($(this).data('value'));
        	showError('error', 'Ingresa solo números enteros.');
        	return false;
        }
        
        //Cantidad no puede ser mayor que la permitida
        if (parseInt($(this).val()) > parseInt(cantPermitida)) {
            $(this).val($(this).data('value'));
            $(this).trigger('change');
            showError('error', 'La cantidad no puede ser mayor a ' + cantPermitida);
            return;
        }
        //SETEAMOS EL VALOR
        $(this).data('value', $(this).val());
        
        var plu = $(this).parent().parent().data('plu');
    	
    	var totalPrice = $(this).parent().parent().data('price') * $(this).val();
    	var totalInvoice = $(this).parent().parent().data('invoiceprice') * $(this).val();
    	
    	$('#td_price_' + plu).empty();
    	$('#td_invoice_' + plu).empty();
    	$('#td_price_' + plu).append('$' + addCommas(totalPrice));
    	$('#td_invoice_' + plu).append('$' + addCommas(totalInvoice));
    	
    	console.log('price!!!!!!!!!!!!!!!!1');
    	console.log($(this).parent().parent().data('price'));
    	console.log($(this).parent().parent().data('invoiceprice'));
    	
    	
    	
//    	$(this).parent().parent().children().children().append('Hola');
    	
    	console.log('total!!!!!!!!!!!!');
    	console.log(totalPrice);
    	console.log(totalInvoice);
        
        //RECALCULAR TOTALES
        calcularTotales();
    });

    /**
     * ACTUALIZAR PAQUETE DEL AHORRO AL CAMBIAR LA CANTIDAD FORMULARIO PAQUETE AHORRO
     */
    $('body').on('change', '.cantidad', function () {
        //CANTIDAD NO PUEDE SER NEGATIVA
        if ($(this).val() <= 0 || isNaN($(this).val())) {
            $(this).val(1);
            showError('error', 'La cantidad que intentas ingresar es incorrecta, por favor intenta números mayores o iguales a 1. Si desea eliminar tu suscripción por favor dirígete a la opción de paquete del ahorro.');
            return false;
        }
        
        //CANTIDAD DEL PAQUETE DEL AHORRO NO PUEDE SUPERAR LA CANTIDAD MAXIMA
        if ($(this).val() > $(this).data('cantmax')) {
            $(this).val($(this).data('cantmax'));
            showError('error', 'Solo puedes ingresar hasta ' + $(this).data('cantmax') + ' paquetes');
            return false;
        }
        //ACTUALIZO LA SUSCRIPCION DEL PAQUETE DEL AHORRO
        updateSuscription(routeEditarSuscripcion, $(this).data('id'), $(this).data('pckgid'), $(this).data('campana'), $(this).val());
    });

    /*
     * DESHABILITAR CAMPOS CUANDO NO SE ACTIVE LA CAMPANA SWITCH DE LAS ZONAS
     */
    $('.checkcampana').on('switchChange.bootstrapSwitch', function (event, state) {
    	//CUANDO EL SWITCH ES SI ACTIVA EL CAMPO CAMPAÑA
        if (state === true) {
            $(this).parent()
                    .parent()
                    .parent()
                    .parent()
                    .siblings()
                    .children()
                    .children('input.inputcampana')
                    .attr('readonly', false);
        } else {
            $(this).parent()
                    .parent()
                    .parent()
                    .parent()
                    .siblings()
                    .children()
                    .children('input.inputcampana')
                    .attr('readonly', true);
        }
    });

    /*
     * DESHABILITAR CAMPANAS POR DEFECTO MOSTRAR SWITCH SI / NO
     */
    $('.checkcampana').each(function () {
        if (!$(this).is(':checked')) {
            $(this).parent()
                    .parent()
                    .parent()
                    .parent()
                    .siblings() //HERMANO DEL LADO
                    .children()
                    .children('input.inputcampana')
                    .attr('readonly', true);
        }
    });

    /**
     * BUSCADOR DE ZONAS
     */
    $("#searchZones").keyup(function () {
        var valor = $(this).val();
        $('label.theZones').each(function () {
            if ($(this).is(':contains("' + valor.toUpperCase() + '")')) {
                $(this).parent('div').parent('div').show();
            } else {
                $(this).parent('div').parent('div').hide();
            }
        });
    });

    /**
     * BUSCADOR DE ZONAS X MAIL PLAN
     */
    $("#searchMailPlan").change(function () {
        $("#activarmasiva").val('');
        var valor = $(this).val();
        //Verificamos que el campo mail plan este vacio
        if (valor === '') {
            // Recorremos los elementos
            $('input:hidden.MailPlan').each(function () {
                // Mostramos la zona
                $(this).parent('div').parent('div').show();
            });
        } else {
            //	Recorremos los elementos
            $('input:hidden.MailPlan').each(function () {
                //Verificamos que si el valor del elemento es igual al del selector
                if ($(this).val().toUpperCase() === valor.toUpperCase()) {
                    //Muestra la zona
                    $(this).parent('div').parent('div').show();
                } else {
                    //Oculta la zona
                    $(this).parent('div').parent('div').hide();
                }
            });
        }
    });

    /*
     * CAMBIAR CAMPANAS MASIVAMENTE
     */
    $("#accionesMasivas").click(function () {
        $('.panel:visible').each(function () {
            var input = $(this).children()
                    .children()
                    .children('input.inputcampana');
            if (!input.is('[readonly]')) {
                input.val($("#campanamasiva").val());
            }
        });
    });

    /*
     * ACTIVAR / DESACTIVAR ZONAS MASIVAS OPCION SELECTOR
     */
    $("#activarmasiva").change(function () {
        $('.panel:visible').each(function () {
            var self = $(this).children()
                    .children()
                    .children()
                    .children()
                    .children('input:checkbox.checkcampana');
            if ($("#activarmasiva").val() === 'true') {
                self.bootstrapSwitch('state', true);
            }
            if ($("#activarmasiva").val() === 'false') {
                self.bootstrapSwitch('state', false);
            }
        });
    });

    /**
     * VACIAR CAMPO DE BUSQUEDA DE LAS ZONAS
     */
    $("#emptySearch").click(function () {
        $("#searchZones").val("");
        $("#searchZones").trigger('keyup');
    });

    /**
     * OVERLAY (PANTALLA OSCURA) PARA CAMBIO DE MARCAS
     */
    $('#changeBrand').click(function () {
        $('#overlay').toggle();
    });
    $('#overlay').click(function () {
        $(this).hide();
    });

    /**
     * DIRECTOR CREA PEDIDO
     */
    /*$("#cedasesoras").change(function () {
        var campanaactual = $(this).find(':selected').data('campana');
        var docasesora = $(this).val();
        var zona = $(this).find(':selected').data('zona');
        if ($(this).val() != '') {
            //VALIDO LA ASESORA
            validAdviser(routeValidAsesora, docasesora, campanaactual, zona);
        }
    });*/

    /**
     * CARGAR CAMPANAS SEGUN EL PAQUETE
     * EVENTO CHANGE SELECTOR DE PAQUETES
     **/
    $("#select-package").change(function () {
    	//SI ES VACIO / LIMPIA EL SELECTOR DE CAMPAÑA DE ENTREGA
        if ($(this).val() == '') {
            $("#select-campaing option").remove();
            $("#select-campaing").append($("<option />").val('').text('Seleccione una campaña'));
        } else {
        	//CARGA LAS CAMPAÑAS DE ENTREGA
            cargarCampanaPaquete($(this).val());
        }
    });

    /**
     * AGREGAR PRODUCTOS CON LA TECLA ENTER
     */
//    ("#formOrder").keypress(function (e) {
    $("#newcantidad").keypress(function (e) {
        if (e.which == 13) {
        	//Simulamos el evento click al boton agregar producto
            $("#addProduct").trigger("click");
            showMessageAddproduct = true;
            return false;
        }
    });
    
    //Capturamos el evento key press del campo cantidad de la tabla de productos
    $('.cantProd').keypress(function (e) {
    	//Verificamos si es un enter
        if (e.which == 13) {
        	//Simulamos el evento change del campo cantidad
        	$(this).trigger("change");
            return false;
        }
    });
    
    $('#cedasesoras').keypress(function (e) {
		if (e.which == 13) {
			//Simulamos el evento click al boton buscar asesora
            $("#btn-searchAdviser").trigger("click");
            return false;
    	}
    });
    
    /**
     * CERRAR MODAL DE ALERTAS
     */
    jQuery(document).on('click', '.closeModal', function () {
        jQuery('#alertasPpales').hide();
    });

});

//Fin Document Ready

/**
 * Metodo para actualizar el paquete del ahorro
 * 
 * @param string route
 * @param stringid
 * @param string packageId
 * @param string campaingId
 * @param string cantidad
 * @returns html
 */
function updateSuscription(route, id, packageId, campaingId, cantidad) {
    $.ajax({
        url: route,
        type: 'GET',
        cache: false,
        datatype: 'json',
        data: {
            id: id,
            packageId: packageId,
            campaingId: campaingId,
            quantity: cantidad
        },
        beforeSend: function () {
            $("#loadingSaveOrder").show();
        },
        complete: function () {
            $("#loadingSaveOrder").hide();
        },
        success: function (data) {

        },
        error: function (xhr, textStatus, thrownError) {
            showError('error', 'Error recuperando paquetes de ahorro');
        }
    });
}

/**
 * Metodo para agregar los paquetes del ahorro al pedido
 * 
 * @param string route
 * @param string campana
 * @returns html
 */
function cargarSuscripciones(route, campana) {
	if($('#cedasesoras').val() !== undefined && $('#cedasesoras').val() !== '' && $('#cedasesoras').val() !== null) {
		route = route + '?adviserDocument=' + $('#cedasesoras').val();
		console.log(route);
	}
	
	if($('#adviserCampaing').val() !== undefined && $('#adviserCampaing').val() !== '' && $('#adviserCampaing').val() !== null) {
		campana = $('#adviserCampaing').val().trim();  
	}
	
    $.ajax({
        url: route,
        type: 'GET',
        cache: false,
        datatype: 'json',
        success: function (data) {
            if (!data.error && data.packages !== null) {
                //Variable para almacenar las suscripciones al paquete del ahorro vigentes
                var packages = [];
                $.each(data.packages, function (i, item) {
                    if (item.campanaEntrega == campana) {
                        //Agregamos la suscripcion a la lista 
                        packages.push({suscriptionId: item.paqueteAhorroSuscripcionesId, packageId: item.paqueteAhorroEstrategiaId, campaingId: item.paqueteAhorroFechasEntregaId, plu: item.plu, currentQuantity: item.cantidad, maxQuantity: item.cantidadMaxima, campaing: item.campanaEntrega});
                        //Eliminamos la lista del session storage	
                        sessionStorage.removeItem('packages');
                        //Agregamos la lista al session storage
                        sessionStorage.setItem('packages', JSON.stringify(packages));

                        //CREO EL PRODUCTO
                        checkProduct(routeCheckProduct, item.plu, item.cantidad);
                    }
                });
            }
        },
        error: function (xhr, textStatus, thrownError) {
            showError('error', 'Error recuperando paquetes de ahorro');
        }
    });
}

/**
 * Metodo para cargar las campañas de un paquete especifico
 * 
 * @param int id
 * @returns html
 */
function cargarCampanaPaquete(id) {
    $.ajax({
        url: routeCampanaPaquete,
        type: 'GET',
        data: {
            'id': id
        },
        cache: false,
        datatype: 'json',
        beforeSend: function () {

        },
        complete: function () {

        },
        success: function (data) {
            if (!data.error && data.campaings !== null) {
                var dropdown = $("#select-campaing");
                $("#select-campaing option").remove();
                dropdown.append($("<option />").val('').text('Seleccione una campaña'));
                $.each(data.campaings, function () {
                    dropdown.append($("<option />").val(this.paqueteAhorroEstrategiaId).text(this.campana));
                });
            } else {

            }
        },
        error: function (xhr, textStatus, thrownError) {
            showError('error', 'Error recuperando las campañas del paquete');
        }
    });
}
/**
 * Metodo para validar la información de la asesora CUANDO SE LE CREA PEDIDO A LA ASESORA
 * 
 * @param string routeValidAsesora
 * @param string document
 * @param string campana
 * @param string zona
 * @returns html
 */
function validAdviser(routeValidAsesora, document, campana, zona) {
    $.ajax({
        url: routeValidAsesora,
        type: 'GET',
        data: {
            'document': document,
            'campaing': campana
        },
        cache: false,
        datatype: 'json',
        beforeSend: function () {

        },
        complete: function () {

        },
        success: function (data) {
        	console.log(data);
            if (!data.error && data.monto !== null) {
            	//Setea los datos del monto minimo y maximo de la asesora
                min = data.monto.MontoMin;
                max = data.monto.MontoMax;
                //LLENADO LAS FLECHAS DEL TERMOMETRO
                $(".montomin span").html('$' + addCommas(min));
                $(".cupomax span").html('$' + addCommas(max));
                //LLENAN LOS CAMPOS OCULTOS
//                $("#campanaactual").val(campana);
//                $("#zonaactual").val(zona);
//                $("#document").val(document);
                //Muestra el div con los datos de envio de la asesora
                $('#adviserAddressInfo').show();
                //Desabilitamos el botón buscar
                $('#btn-searchAdviser').attr("disabled", true);
                //Eliminamos el evento click del botón
                $('#btn-searchAdviser').removeAttr('onclick');
                //MUESTRA FORMULARO PARA AGREGAR PLU
                $("#panelAgregarPD").show();
                //CARGA LOS PLU
                cargarPlus(routeProductos, document);
                //SE VUELVE EL SELECT 2 (AUTOCOMPLETAR)
                $("#newplu").select2({
                    allowClear: true,
                    placeholder: 'Código o descripción',
                    width: '100%',
                    language: {
                        noResults: function (term) {
                            return "No se encontraron resultados";
                        }
                    }
                });
                //DESABILUTAR EL CAMPO ASESORA <CAMBIAR POR READONLY>
                $("#cedasesoras").attr('disabled', true);
                //SI TIENE ORDENES LAS RECUPERO EN LA TABLA
                if (typeof data.order !== 'undefined' && data.order) {
                	//CARGA EL DETALLE DEL PEDIDO EN LA TABLA
                    loadOrderAdviser(data);
                }
                
                cargarSuscripciones(routeSuscripcionesPaquetes, campana);
            } else {
            	//LIMPIO CAMPOS, ESCONDE PANEL Y MUESTRA EL MENSAJE DE ERROR Y LIMPIAR CAMPO ASESORA
                $("#campanaactual").val('');
                $("#zonaactual").val('');
                $("#document").val('');
                $("#panelAgregarPD").hide();
                showError('error', data.messages);
                //CAMBIAR POR VAL('')
                $("#cedasesoras").val('');
                //Limipamos el div con la información de envio de la asesora
                $('#adviserPersonalData').empty(); 
//                $("#cedasesoras").val(null).trigger("change");
            }
        },
        error: function (xhr, textStatus, thrownError) {
            showError('error', 'Error recuperando información de la asesora');
        }
    });
}

/**
 * Metodo para cargar todo el pedido de una asesora
 * @param json data
 * @returns html
 */
function loadOrderAdviser(data) {
	//SETEA EL ID DEL PEDIDO
    $("#pedidoId").val(data.order.pedidoEncabezado.pedidoId);
    //RECORRE LA LISTA DE PLU DEL PEDIDO
    $.each(data.order.pedidoDetalle, function (i, item) {
        var jsonData = {};
        //SETEA EL PLU
        jsonData['producto'] = {
            'PLU': item.plu,
            'quantity': item.cantidad,
            'InvoicePrice': item.precioFactura,
            'Price': item.precioCatalogo,
            'puntosPrenda': item.puntosPrenda,
            'Description': item.descripcion
        };
        //PINTA EN LA TABLA
        checkProduct(routeCheckProduct, item.plu, item.cantidad);
    });
}

/**
 * Metodo para agregar productos a la tabla de detalle
 * 
 * @param string routeCheckProduct
 * @param string plu
 * @param string quantity
 * @param string zone
 */
function checkProduct(routeCheckProduct, plu, quantity) {
    $.ajax({
        url: routeCheckProduct + '/' + plu + '/' + quantity,
        data: {
            'zone': $("#zonaactual").val()
        },
        type: 'GET',
        cache: false,
        datatype: 'json',
        success: function (data) {
            if (!data.error && data.producto !== null) {
                //AGREGO EL PRODUCTO 
                if (addProduct(plu, quantity, data)) {
                    //SI TODO SALIO BIEN MUESTRO EL MENSAJE Y VACIO LOS CAMPOS
                    //PARA UNA NUEVA BUSQUEDA
                	if(showMessageAddproduct) {
                		toastr.success("Producto agregado con éxito");
                	}
                    $("#newcantidad").val("");
                    $("#newplu").val(null).trigger("change");
                }/* else {
                	 showError('error', 'Error verificando el producto');
                }*/
            } else {
                //SI DEVUELVE UN ERROR LO MUESTRO EN PANTALLA
                showError('error', data.messages);
            }
        },
        error: function (xhr, textStatus, thrownError) {
            showError('error', 'Error recuperando el producto');
        }
    });
}

/**
 * Metodo para validar si el producto ya existe y agregarlos
 * @param string plu
 * @param string quantity
 * @param string data
 * @returns boolean
 */
function addProduct(plu, quantity, data) {
	var intents = 0;
	
    //VALIDO QUE EL PLU ESTE AGREGADO
    var existe = false;
    var cant = 0;
    //Variable para captura el id del campo a editar
    var input = null;
    
    var tdExist = '';
    
    jQuery("#tblPdDetails tbody tr").each(function (index) {
    	var plutd = jQuery(this).find('td').data('plu');
    	tdExist = jQuery(this).find('td');  
        if (plutd == plu) {
            //Seteamos el id del campo a editar
            input = "#plu-" + plutd;
            existe = true;
            cant = jQuery(this).find('input').attr("id", "plu-" + plutd).val();
            return true;
        }
    });

    //Capturamos la respuesta del metodo verificar paquete
    var isError = checkPackage(plu, quantity, data, cant, existe, input);
    //Si existe error
    if (isError !== null && first === true) {   
    	first = false;  
    	firstValidation = false;  
        //Retornamos el resultado
        return isError;
    }
    //SI EL PRODUCTO YA EXISTE MOSTRAMOS MENSAJE PARA ADICIONAR MAS CANTIDADES
    if (existe) {
        swal({
            title: '',
            text: 'Ya tienes registrado  ' + data.producto.Description + ', ¿desea adicionar más productos?',
            type: 'info',
            html: true,
            showConfirmButton: true,
            showCancelButton: true,
            confirmButtonText: 'Agregar',
            confirmButtonClass: 'bgmarca font-white ',
            cancelButtonText: 'Cancelar',
            cancelButtonClass: 'btnnewped font-white'
        }, function (isConfirm) {
            if (isConfirm) {
            	var objectPlu = findObjectPackage(plu);
            	
            	if(objectPlu != null) {
            		if((parseInt(quantity) + parseInt(cant)) > objectPlu.maxQuantity) {  
            			setTimeout(function () {
            				showError('error', 'Debe ingresar máximo ' + objectPlu.maxQuantity + ' paquetes del ahorro.');
            				$('#newplu').val(null).trigger("change");
            				$('#newcantidad').val('');     
						}, 500);  
            			
            			return false;
            		}
            	}
            	
            	var totalItems = parseInt($('#plu-' + plu).val()) + parseInt(quantity);
            	var totalPrice = parseFloat($('#td_price_' + plu).parent().data('price')) * totalItems;
            	var totalInvoice = parseFloat($('#td_invoice_' + plu).parent().data('invoiceprice')) * totalItems; 
            	
            	$('#td_price_' + plu).empty();
            	$('#td_invoice_' + plu).empty();
            	$('#td_price_' + plu).append('$' + addCommas(totalPrice));
            	$('#td_invoice_' + plu).append('$' + addCommas(totalInvoice));
            	
                //SI ACEPTA AGREGAR CANTIDADES REALIZO ESTA 
                sumCantProduct(plu, quantity, data);
            } else {
                $("#newcantidad").val("");
                $("#newplu").val(null).trigger("change");
            }
        });
        return false;
    }

    //CREO EL PRODUCTO EN LA TABLA DE PEDIDO
    createProduct(plu, quantity, data);
    return true;
}

/**
 * Función para obtener el objeto del paquete del session storage
 * @param 	plu
 * @returns null | array 
 */
function findObjectPackage(plu) {
    //Objeto para guardar el paquete del ahorro en el pedido
    var object = null;

    //Obtenemos la lista de paquetes del session storage
    var list = JSON.parse(sessionStorage.getItem('packages'));
    //Recorremos la lista
    $.each(list, function (key, value) {
        //Verificamos si el plu ingresado es igual a un paquete del ahorro
        if (plu === value.plu.trim()) {
            //Seteamos el paquete a la variable object
            object = value;
            return;
        }
    });

    return object;
}

var first = true;
var firstValidation = true;
var isNew; 
/**
 * Función para verificar el PLU del paquete
 * @param 	plu
 * @param 	quantity
 * @param 	data
 * @param 	currentQuantity
 * @param 	exist
 * @param 	dPlu
 * @returns null | boolean
 */
function checkPackage(plu, quantity, data, currentQuantity, exist, input) {
    //Objeto para guardar el paquete del ahorro en el pedido
    var object = findObjectPackage(plu);

    //Verificamos que el objeto del pauqete sea direferente de vacio y de indefinido
    if (object !== null && object !== undefined) {
        //Parseamos a int las cantidades para verificar que la cantidad ingresada + la actual no sea mayor a la permitida
        if (parseInt(quantity) > object.maxQuantity) {
        	first = true;
            //Mostramos mensaje de error
            showError('error', 'Debe ingresar máximo ' + object.maxQuantity + ' paquetes del ahorro.');
            //Retornamos para no ejecutar el codigo de verificar PLU
            return false;
        }
    }
      
    //Verificamos que el objeto del pauqete sea direferente de vacio y de indefinido
    /*if (object !== null && object !== undefined && firstValidation === false) {
        //Parseamos a int las cantidades para verificar que la cantidad ingresada + la actual no sea mayor a la permitida
        if (parseInt(quantity + currentQuantity) > object.maxQuantity) {
        	first = true;
            //Mostramos mensaje de error
            showError('error', 'Debe ingresar máximo ' + object.maxQuantity + ' paquetes del ahorro.');
            //Retornamos para no ejecutar el codigo de verificar PLU
            return false;
        }
    }*/

    //Verificamos que el objeto del paquete sea diferfente de vacio y el plu existe en la tabla
    if (object !== null && exist) {
        //Limpiamos el valor del campo de texto de la cantidad del paquete
//        $(input).val(0);
//        //Sumamos la cantidad al paquete
//        sumCantProduct(plu, quantity, data);
        //Actualizamos la suscripción al paquete
        updateSuscription(routeEditarSuscripcion, object.suscriptionId, object.packageId, object.campaingId, quantity);
        if(isNew) {
        	first = false;
//        	isNew = false;
        }
        
        if(!isNew && firstValidation) {
        	first = true;
//        	isNew = true;
        }
        //Retornamos falso
        return true;
    }

    //Verificamos que el obejeto del paquete sea diferente de vacio y el plu no exista en la tabla
    if (object !== null && !exist) {
        //Creamos el producto en la tabla
        createProduct(plu, quantity, data);
        //Actualizamos la suscripción al paquete
        updateSuscription(routeEditarSuscripcion, object.suscriptionId, object.packageId, object.campaingId, quantity);
        //Retornamos verdadero
        return true;
    }

    return null;
}


/**
 * Metodo para sumar cantidades de productos
 * @param string plu
 * @param string quantity
 */
function sumCantProduct(plu, quantity, data) {
	console.log('---------------------------');
	console.log(plu);
	console.log(quantity);
	console.log(data);
	console.log('---------------------------');
    //SUMO EL NUEVO VALOR AL VIEJO EXISTENTE
    var oldCant = $("#plu-" + plu).val();
    var newCant = parseInt(oldCant) + parseInt(quantity);
    $("#plu-" + plu).val(newCant);
    $("#newcantidad").val("");
    $("#newplu").val(null).trigger("change");
    //MODIFICO LA DATA DE EL REGISTRO
    var qtity = parseInt($("#plu-" + plu).parent("td").parent("tr").data("quantity")) + parseInt(quantity);
    $("#plu-" + plu).parent("td").parent("tr").attr("data-quantity", qtity);
    //RECALCULAR LOS TOTALES
    calcularTotales();
}

/**
 * Metodo para adicionar productos a la tabla
 * @param string plu
 * @param string quantity
 * @param string data
 * @returns html
 */
function createProduct(plu, quantity, data) {
	console.log("======================================================");
	console.log(data);
	console.log(plu);
	console.log("======================================================");
    var ispackage = $("#newplu").find(':selected').data('ispackage');
    var pluExcluded = data.producto.PluExcluido;  
    var readOnly = '', clbtnBorrar = '';
    if (data.producto.esPaquete) {
        //clbtnBorrar = 'style="display: none"';
        ispackage = 1;
    } else {
    	ispackage = 0;
    }
    
    //CREO EL PRODUCTO COMO UN REGISTRO DE LA TABLA PEDIDOS
    jQuery("#tblPdDetails tbody").append(
            $("<tr>")
            .attr("data-plu", data.producto.PLU)
            .attr("data-quantity", quantity)
            .attr("data-invoiceprice", data.producto.InvoicePrice)
            .attr("data-price", data.producto.Price)
            .attr("data-puntos", data.puntosPrenda)
            .attr("data-ispackage", ispackage)
            .attr("data-pluExcluded", pluExcluded)          
            .append($("<td data-plu='" + data.producto.PLU + "'>")
                    .text(data.producto.PLU)
                    )
            .append($("<td>")
                    .text(data.producto.Description)
                    )
            .append($("<td>")
                    .append($("<input " + readOnly + ">")
                            .attr("type", "number")
                            .attr("class", "form-control cantProd")
                            .attr("id", "plu-" + plu)
                            .attr("name", "products[" + plu + "][cantidad]")
                            .attr("value", quantity)
                            .attr("data-value", quantity)
                            .attr("min", "0")
                            )
                    )
            .append($("<td id='td_invoice_" + data.producto.PLU + "'>")
                    .attr("class", "text-center")
                    .text("$" + addCommas(data.producto.InvoicePrice * quantity))
                    )
            .append($("<td id='td_price_" + data.producto.PLU + "'>")
                    .attr("class", "text-center")
                    .text("$" + addCommas(data.producto.Price * quantity))
                    )
            .append($("<td>")
                    .append($("<a " + clbtnBorrar + " >")
                            .attr("href", "javascript:void(0)")
                            .attr("class", "font-marca rmvPd")
                            .attr("data-toggle", "confirmation")
                            .attr("data-original-title", "¿Estás segura que quieres borrar el producto?")
                            .attr("title", "")
                            .attr("data-btn-ok-label", "Si")
                            .attr("data-btn-cancel-label", "No")
                            .html("<i class='fa fa-trash-o'></i>")
                            )
                    )
            );
    //RECALCULO TODOS LOS TOTALES CON EL NUEVO REGISTRO AGREGADO
    calcularTotales();
}

/**
 * Metodo para calcular los totales del pedido
 * @returns html
 */
function calcularTotales() {
    var calculadoPedido = 0, totalCatalogoPedido = 0, totalFacturaPedido = 0,
            totalPuntos = 0, ispackage = 0, totalCatalogoPedidoAhorro = 0,
            totalFacturaPedidoAhorro = 0, gananciaPaquete = 0;
    
    var isPluExcluded = false;
    //SUMO LOS TOTALES
    jQuery("#tblPdDetails tbody tr").each(function (index) {
        var quantity = $(this).children('td').children('input').val();
        var price = $(this).data('price');
        var invoiceprice = $(this).data('invoiceprice');
        var puntos = $(this).data('puntos');
        ispackage = $(this).data('ispackage');
        isPluExcluded = $(this).data('pluexcluded');
        
//        alert($(this).data('plu'));

        //SI 'NO' ES PAQUETE DEL AHORRO SUMO NORMALMENTE LOS TOTOLES
        if (ispackage == 0 && !isPluExcluded) {
        	console.log('entra!!!!!!!!!!!!!!!!!');
            if (typeof quantity !== 'undefined' &&
                    typeof puntos !== 'undefined' &&
                    typeof price !== 'undefined' &&
                    typeof invoiceprice !== 'undefined') {
            	console.log('entra dos!!!!');
                totalCatalogoPedido += parseInt(quantity) * parseInt(price);
                totalFacturaPedido += parseInt(quantity) * parseInt(invoiceprice);
                totalPuntos += (parseInt(quantity) * parseInt(puntos));
                
                $('#hidnTotalFacturaPedido').val(totalFacturaPedido);
                $('#hidnTotalCatalogoPedido').val(totalCatalogoPedido);
                
                validarMonto();
                
                console.log(totalCatalogoPedido);
                console.log(totalFacturaPedido);
                console.log(totalPuntos);
            }
        } else {
            //ACUMULO LOS TOTALES DE LOS PAQUETES DEL AHORRO
            if (typeof quantity !== 'undefined' &&
                    typeof puntos !== 'undefined' &&
                    typeof price !== 'undefined' &&
                    typeof invoiceprice !== 'undefined') {
                totalCatalogoPedidoAhorro += parseInt(quantity) * parseInt(price);
                totalFacturaPedidoAhorro += parseInt(quantity) * parseInt(invoiceprice);
                var ganancia = (parseInt(quantity) * parseInt(price)) - (parseInt(quantity) * parseInt(invoiceprice));
                gananciaPaquete += ganancia;
                console.log('Ganancia Paquete --->: ' + ganancia);  
            }
            //VALIDO SI SOLO ESTÁ EL PAQUETE DEL AHORRO EN LA TABLA Y PONGO TODO EN CERO
            if (jQuery("#tblPdDetails tbody tr").length == '1') {
                totalCatalogoPedido = 0;
                totalFacturaPedido = 0;
                totalPuntos = 0;
                calculadoPedido = totalCatalogoPedido - totalFacturaPedido + gananciaPaquete;
                $('#totalFacturaPedido').html('$' + addCommas(totalFacturaPedido));
                $('#totalCatalogoPedido').html('$' + addCommas(totalCatalogoPedido));
                $('#calculadoPedido').html('$' + addCommas(calculadoPedido));
                $('#hidnTotalFacturaPedido').val(totalFacturaPedido);
                $('#hidnTotalCatalogoPedido').val(totalCatalogoPedido);
                $('#hidnCalculadoPedido').val(calculadoPedido);
                $('#hidnPuntos').val(totalPuntos);
                $('#puntos').html(addCommas(totalPuntos));

                if(!isPluExcluded || isRemovePlu == true) {  
                	//VALIDO LOS MONTOS Y MUESTRO EN EL TERMOMETRO
                	validarMonto();
                }
            }
        }
    });
    
    //PINTO LOS TOTALES SI 'NO' ES PAQUETE DEL AHORRO
//    if (ispackage == 0) {
    calculadoPedido = totalCatalogoPedido - totalFacturaPedido + gananciaPaquete;
    $('#totalFacturaPedido').html('$' + addCommas(totalFacturaPedido + totalFacturaPedidoAhorro));
    $('#totalCatalogoPedido').html('$' + addCommas(totalCatalogoPedido + totalCatalogoPedidoAhorro));
    $('#calculadoPedido').html('$' + addCommas(calculadoPedido));
    $('#hidnTotalFacturaPedido').val(totalFacturaPedido);
    $('#hidnTotalCatalogoPedido').val(totalCatalogoPedido);
    $('#hidnCalculadoPedido').val(calculadoPedido);
    $('#hidnPuntos').val(totalPuntos);
    $('#puntos').html(addCommas(totalPuntos));  
    if(!isPluExcluded || isRemovePlu == true) {    
    	validarMonto();
	} 
    
    isRemovePlu = false; 
        //VALIDO LOS MONTOS Y MUESTRO EN EL TERMOMETRO
//    } else {*/
    //MUESTRO LOS TOTALES DEL PAQUETE DEL AHORRO SEPARADOS DE LOS DEMAS
    /*$('#totalCatalogoPedidoAhorro').html('$' + addCommas(totalCatalogoPedidoAhorro));
    $('#totalFacturaPedidoAhorro').html('$' + addCommas(totalFacturaPedidoAhorro));
    $('#paneltotalFacturaPedidoAhorro').show();
    $('#paneltotalCatalogoPedidoAhorro').show();*/
//    }
    //GUARDO EL PEDIDO
    saveOrder();
}

/**
 * Metodo para validar el monto y mostrar en el termometro
 * @returns html
 */
function validarMonto() {
    var now = parseInt($("#hidnTotalCatalogoPedido").val());
    var porc = (now * 90) / max;
    if (!isNaN(porc)) {
    	//SI SUPERO EL 100% PONGO SIEMPRE LA BARRA AL 100%
        if ((parseInt(porc) >= 100)) {
            porc = 100;
        }
        //SI ES MENOR QUE EL MONTO MIN O MAYOR AL CUPO MAX PONGO EN ROJO EL TERMOMETRO
        if ((parseInt(porc) <= 12) || (parseInt(porc) >= 90)) {
        	//TERMOMETRO VERDE
            $("#montoPermitido").removeClass('montoPermitido');
            //TERMOMETRO ROJO
            $("#montoPermitido").addClass('montoPermitido_warning');
        } else {
        	//TERMOMETRO VERDE
            $("#montoPermitido").addClass('montoPermitido');
            //TERMOMETRO ROJO
            $("#montoPermitido").removeClass('montoPermitido_warning');
        }
    } else {
        now = 0;
        porc = 0;
    }
    //PONGO EL PORCENTAJE DEL TERMOMETRO
    $("#montoPermitido").css('width', Math.round(porc) + '%');
    //VALOR TOOLTIP DEL TERMOMETRO
    $(".toolFactura").html("$" + addCommas(now));
}

/**
 * Metodo para encontrar toda la informacion de la directora
 * @param string route
 * @returns html
 */
function findZoneDirector(route) {
    $.ajax({
        url: route,
        type: 'GET',
        cache: false,
        datatype: 'json',
        beforeSend: function () {
            $("#loadingDirectora").show();
        },
        complete: function () {
            $("#loadingDirectora").hide();
        },
        success: function (data) {
            if (!data.error) {
                jQuery("#divDirectora")
                        .append($("<address>")
                                .append($("<span style='font-size: 14px; line-height: 10px;'>").text(data.directora.Nombre))
                                .append($("<br><b class='font-marca' style='font-size: 17px;'>Zona: </b>"))
                                .append($('<span class="font-marca" style="font-size: 17px;">').text(' ' + data.directora.Zona))
                                .append($("<br><span class='font-marca'><i class='fa fa-phone'></i></span>"))
                                .append($('<span class="font-marca">').text(' ' + data.directora.Celular))
                                .append($("<br>"))
                                .append($('<a>')
                                        .attr('href', 'mailto:' + data.directora.Email)
                                        .attr('class', 'font-marca')
                                        .attr('style', 'word-wrap: break-word')
                                        .append($("<i class='icon-envelope'></i>"))
                                        .append(' ' + data.directora.Email))
                                );
            }
        },
        error: function (xhr, textStatus, thrownError) {
            showError('error', 'Error cargando la información de la directora de zona.');
        }
    });
}
/**
 * Metodo para encontrar todo los puntos y campanas
 * 
 * @param string route
 * @returns html
 */
function findHistoryPoints(route) {
    $.ajax({
        url: route,
        type: 'GET',
        cache: false,
        datatype: 'json',
        beforeSend: function () {
            $("#loadingPuntos").show();
        },
        complete: function () {
            $("#loadingPuntos").hide();
        },
        success: function (data) {
            if (!data.error) {
                if (data.puntos.CampanaActual != null) {
                    $("#_CampanaActual").text(data.puntos.CampanaActual);
                } else {
                    $("#_CampanaActual").text('-');
                }
                if (data.puntos.PuntosCampanaActual != null) {
                    $("#_PuntosCampanaActual").text(data.puntos.PuntosCampanaActual);
                } else {
                    $("#_PuntosCampanaActual").text('0');
                }
                if (data.puntos.CampanaAnterior != null) {
                    $("#_CampanaAnterior").text(data.puntos.CampanaAnterior);
                } else {
                    $("#_CampanaAnterior").text('-');
                }
                if (data.puntos.PuntosCampanaAnterior != null) {
                    $("#_PuntosCampanaAnterior").text(data.puntos.PuntosCampanaAnterior);
                } else {
                    $("#_PuntosCampanaAnterior").text('0');
                }
                if (data.puntos.CampanaTrasanterior != null) {
                    $("#_CampanaTrasanterior").text(data.puntos.CampanaTrasanterior);
                } else {
                    $("#_CampanaTrasanterior").text('-');
                }
                if (data.puntos.PuntosCampanaTrasanterior != null) {
                    $("#_PuntosCampanaTrasanterior").text(data.puntos.PuntosCampanaTrasanterior);
                } else {
                    $("#_PuntosCampanaTrasanterior").text('0');
                }
            }
        },
        error: function (xhr, textStatus, thrownError) {
            showError('error', 'Error cargando la información de la directora de zona.');
        }
    });
}
/**
 * Metodo para encontrar las fechas claves
 * 
 * @param string route
 * @returns html
 */
function findKeyDates(route, urlNotificaciones) {
    $.ajax({
        url: route,
        type: 'GET',
        cache: false,
        datatype: 'json',
        async: true,
        beforeSend: function () {
            $("#loadingDates").show();
            $(".fechasImportantesHome").hide();
        },
        complete: function () {
            $("#loadingDates").hide();
            $(".fechasImportantesHome").show();
        },
        success: function (data) {
            if (!data.error) {
            	//Si hay fechas
                if (data.FechasConferencias.length > 0) {
                    var countFC = 1;
                    //recorre la lista de fechas y las agrega al carrusel
                    $.each(data.FechasConferencias, function (i, item) {
                        createDatesCarousel(item.FechaConferencia, item.LugarConferencia, countFC, '#FeConfCarousel', 'Fecha y lugar de conferencia');
//                        createDatesCarousel(item.FechaConferencia, item.LugarConferencia, countFC, '#FeConfCarousel', 'Fecha y lugar de conferencia');
                        countFC++;
                    });
                    //createNavegationCarousel('#FeConfCarousel');
                } else {
                	//Oculta el carrusel
                    $('#FeConfCarousel').hide();
                    //Muestra el panel sin fecha
                    $('.simpleDateConf').show();
                }

                if (data.FechasCambios.length > 0) {
                    var countFCa = 1;
                    $.each(data.FechasCambios, function (i, item) {
                        createDatesCarousel(item.FechaCambiosDevoluciones, item.LugarCambios, countFCa, '#FeCyDCarousel', 'Fecha y lugar de cambios y devoluciones');
                        countFCa++;
                    });
//                    createNavegationCarousel('#FeCyDCarousel');
                } else {
                    $('#FeCyDCarousel').hide();
                    $('.simpleDateCamb').show();
                }

                //Fechas individuales
                if (data.FechaEntregaPedido != null) {
                	//Mes
                    $("#_FechaEntregaPedidoMes").text(data.FechaEntregaPedido[1]);
                    //Año
                    $("#_FechaEntregaPedidoDia").text(data.FechaEntregaPedido[2]);
                    //Dia
                    $("#_FechaEntregaPedidoAnio").text(data.FechaEntregaPedido[0]);
                    //Esconder el panel de no aplica
                    $("#_FechaEntregaPedidoNA").hide();
                } else {
                	//Esconde las fechas
                    $("#_FechaEntregaPedidoMes").hide();
                    $("#_FechaEntregaPedidoDia").hide();
                    $("#_FechaEntregaPedidoAnio").hide();
                    //Muestra panel no aplica
                    $("#_FechaEntregaPedidoNA").show();
                }

                if (data.FechaPagoPedido != null) {
                    $("#_FechaPagoPedidoMes").text(data.FechaPagoPedido[1]);
                    $("#_FechaPagoPedidoDia").text(data.FechaPagoPedido[2]);
                    $("#_FechaPagoPedidoAnio").text(data.FechaPagoPedido[0]);
                    $("#_FechaPagoPedidoNA").hide();
                } else {
                    $("#_FechaPagoPedidoMes").hide();
                    $("#_FechaPagoPedidoDia").hide();
                    $("#_FechaPagoPedidoAnio").hide();
                    $("#_FechaPagoPedidoNA").show();
                }

                if (data.FerchaLimitePedido != null) {
                    $("#_FerchaLimitePedidoMes").text(data.FerchaLimitePedido[1]);
                    $("#_FerchaLimitePedidoDia").text(data.FerchaLimitePedido[2]);
                    $("#_FerchaLimitePedidoAnio").text(data.FerchaLimitePedido[0]);
                    $("#_FerchaLimitePedidoNA").hide();
                } else {
                    $("#_FerchaLimitePedidoMes").hide();
                    $("#_FerchaLimitePedidoDia").hide();
                    $("#_FerchaLimitePedidoAnio").hide();
                    $("#_FerchaLimitePedidoNA").show();
                }
                //CARGO LAS NOTIFICACIONES del calendario
                $.ajax({
                    url: urlNotificaciones,
                    type: 'GET',
                    cache: false,
                    datatype: 'html',
                    async: true,
                    success: function (data) {
                    	//Pinta las fechas en el header
                        $('#listNotifications').html(data);
                        //Agrega el Scroll
                        $('.scroller').slimScroll({
                            height: '250px'
                        });
                    }
                });
            }
        },
        error: function (xhr, textStatus, thrownError) {
            showError('error', 'Error cargando la información de fechas claves.');
        }
    });
}

/**
 * Metodo para alimentar el carrousel de lugar de conferencia y devolucio
 * 
 * @param array dates
 * @param string places
 * @param int numbertItem
 * @param string divCarousel
 * @param string msj
 * @returns html
 */
function createDatesCarousel(dates, places, numbertItem, divCarousel, msj) {
	console.log(divCarousel);
	
	var margin = '';
	var margin2 = '';
	if(numbertItem > 1) {
		margin = 'style="margin-top: -7px !important;"';  
		margin2 = '; margin-top: -16px !important;';
	} 
	
	$(divCarousel).find('#KeyDateContent').append(
		'<div class="ribbon ribbon-shadow ribbon-color-default uppercase"' + margin + '>' + 
			'<span class="photo">' +
				'<div class="uppercase profile-stat-text text-center font-mini">' + dates[1] + '</div>' +
				'<div class="uppercase profile-stat-title text-center bold">' + dates[2] + '</div>' +
				'<div class="uppercase profile-stat-text text-center font-mini">' + dates[0] + '</div>' +
			'</span>' +
		'</div>' +
		'<div class="ribbon-content" style="clear: initial' + margin2 + '">' +
			'<span class="subject">' +
				'<h5 class="from bold dark">' + msj + '</h5>' +
			'</span>' +
			'<span class="message font-dark">' + places.split('. ')[0] + '.<br/>' + places.split('. ')[1] + '</span>' +
		'</div>'
	);
	
//	countDates = countDates + 1;     
	
	
	/*$($("<div>", {
        'class': 'ribbon ribbon-shadow ribbon-color-default uppercase',
    }).append($("<span>", {
        'class': 'photo'
    }).append($("<div>", {
        'class': 'uppercase profile-stat-text text-center font-mini',
        'html': dates[1]
    })).append($("<div>", {
        'class': 'uppercase profile-stat-title text-center bold',
        'html': dates[2]
    })).append($("<div>", {
        'class': 'uppercase profile-stat-text text-center font-mini',
        'html': dates[0]
    })))).append($("<div>", {
        'class': 'ribbon-content',
        'style': 'clear: initial'
    }).append($("<span>", {
        'class': 'subject'
    }).append($("<h5>", {
        'class': 'from bold dark',
        'text': msj
    }))).append($("<span>", {
        'class': 'message font-dark',
        'text': places
    }))).appendTo(divCarousel + ' > .carousel-inner > .mt-element-ribbon');*/
	
	
	
	
	

    /*var classItem = '';
    //Si es ==1 lo pone al inicio
    if (numbertItem == 1) {
        classItem = 'active';
    }*/
    //CREO ITEM DE CADA UNA DE LAS FECHAS
    /*$("<div>", {
        'class': 'item ' + classItem
    }).append($("<div>", {
        'class': 'mt-element-ribbon'
    }).append($("<div>", {
        'class': 'ribbon ribbon-shadow ribbon-color-default uppercase',
    }).append($("<span>", {
        'class': 'photo'
    }).append($("<div>", {
        'class': 'uppercase profile-stat-text text-center font-mini',
        'html': dates[1]
    })).append($("<div>", {
        'class': 'uppercase profile-stat-title text-center bold',
        'html': dates[2]
    })).append($("<div>", {
        'class': 'uppercase profile-stat-text text-center font-mini',
        'html': dates[0]
    })))).append($("<div>", {
        'class': 'ribbon-content',
        'style': 'clear: initial'
    }).append($("<span>", {
        'class': 'subject'
    }).append($("<h5>", {
        'class': 'from bold dark',
        'text': msj
    }))).append($("<span>", {
        'class': 'message font-dark',
        'text': places
    })))
    ).appendTo(divCarousel + ' > .carousel-inner'); */
}

/**
 * Metodo para crear las flechas de navegacion izquierda / derecha
 * 
 * @param string divCarousel
 * @returns html
 */
function createNavegationCarousel(divCarousel) {
    $("<a>", {
        'class': 'carousel-control left',
        'href': divCarousel,
        'data-slide': 'prev'
    }).append($("<span>", {
        'class': 'glyphicon glyphicon-chevron-left'
    })).appendTo(divCarousel);
    $("<a>", {
        'class': 'carousel-control right',
        'href': divCarousel,
        'data-slide': 'next'
    }).append($("<span>", {
        'class': 'glyphicon glyphicon-chevron-right'
    })).appendTo(divCarousel);

    $(divCarousel).addClass('carousel slide');
}
/**
 * Metodo para listar las ultimas 5 ordenes en el inicio
 * 
 * @param string route
 * @param string urlDetail
 * @param string urlDelete
 */
function listOrders(route, urlDetail, urlDelete) {
    $.ajax({
        url: route,
        type: 'GET',
        cache: false,
        datatype: 'json',
        beforeSend: function () {
            $("#loadingOrder").show();
        },
        complete: function () {
            $("#loadingOrder").hide();
        },
        success: function (data) {
            if (!data.error && data.orders.length > 0) {
                $.each(data.orders, function (key, name) {
                    var btnDel = '';
                    var clPro = '';
                    var txtPro = '';
                    //Si el pedido es procesado 0 / 2
                    if (name.Procesado == '0' || name.Procesado == '2') {
                    	//Verifica que el pedido sea editable
                        if (name.Editable) {
                        	//Agrega el boton eliminar
                            btnDel = $("<a>")
                                    .attr("href", urlDelete + "/" + name.PedidoId + "/true")
                                    .attr("class", "btn-xs font-marca")
                                    .attr("data-toggle", "confirmation")
                                    .attr("data-original-title", "¿Estás segura que quieres borrar el pedido?")
                                    .attr("title", "")
                                    .attr("data-btn-ok-label", "Si")
                                    .attr("data-btn-cancel-label", "No")
                                    .html("<i class='fa fa-trash-o'></i>");
                        }
                    }

                    //Estados dwe pedidos para mostrar en el listado de los ultimos 5
                    if (name.Procesado == '0') {
                    	//Nombre enviado y color gris
                        clPro = 'font-grey-gallery';
                        txtPro = 'Enviado';
                    } else if (name.Procesado == '1') {
                        clPro = 'font-grey-gallery';
                        txtPro = 'Enviado';
                    } else if (name.Procesado == '2') {
                        clPro = 'font-grey-gallery';
                        txtPro = 'Guardado';
                    } else if (name.Procesado == '3') {
                        clPro = 'font-marca';
                        txtPro = 'Facturado';
                    }

                    //Boton ver
                    var btnView = $("<a>")
                            .attr("href", urlDetail + "/" + name.PedidoId + "/" + name.Procesado)
                            .attr("class", "btn btn-outline btn-xs bgmarca font-white btn-circle")
                            .html("<i class='fa fa-eye'></i> Ver");
                    //LLENO LA TABLA CON LOS ULTIMOS 5 PEDIDOS
                    $("#tblListOrders").find("tbody")
                            .append($("<tr>")
                                    .append($("<td>")
                                            .text(name.PedidoId)
                                            )
                                    .append($("<td>")
                                            .text(name.CampanaId)
                                            )
                                    .append($("<td>")
                                            .text(name.Fecha.substr(0, 10))
                                            )
                                    .append($("<td>")
                                            .text(name.CodigoZona)
                                            )
                                    .append($("<td>")
                                            .append($("<span>")
                                                    .attr("class", "label " + clPro + "")
                                                    .html(txtPro)
                                                    )
                                            )
                                    .append($("<td>")
                                            .append(btnView)
                                            .append(btnDel)
                                            )
                                    );
                });
                //Muestra la tabla
                $("#tblListOrders").show();
                //Mostrar boton ver todos los pedidos / Dashboard
                $("#showAllOrders").show();
                //VUELVO LA TABLA DE PEDIDOS UN DATATABLE PARA ORDENAR POR FECHA
                //ERliminar datatable para mostrar la ayuda 
                $('#tblListOrders').dataTable({
                    "language": {
                        "url": "metronic/custom-pedidos-web/scripts/Spanish.json"
                    },
                    "bFilter": false,
                    "lengthChange": false,
                    "searching": false,
                    "bPaginate": false,
                    "info": false,
                    "order": [[2, "desc"]] //Ordenado por fecha posición 2 fecha inicia en 0
                });
            } else {
            	//Mostrar mensaje
                $("#porletHistoryOrders .portlet-body .table-scrollable")
                        .append('<div id="alertResult" class="alert alert-danger">No se encontraron resultados</div>');
                //Oculta tabla listado pedido
                $("#tblListOrders").hide();
                //Oculta boton ver todos los pedidos
                $("#showAllOrders").hide();
            }
            //Mostramos div que contiene la tabla ed pedidos
            $("#porletHistoryOrders").show();
            //Toolip para eliminar
            $('[data-toggle=confirmation]').confirmation({
                rootSelector: '[data-toggle=confirmation]',
                placement: 'top',
                btnCancelClass: 'btn btn-sm btn-default',
                btnOkClass: 'btn btn-sm btn-default'
            });
        },
        error: function (xhr, textStatus, thrownError) {
            showError('error', 'Error listando ordenes');
        }
    });
}

/**
 * Metodo para cargar la lista de plus y activar el auto completar
 * 
 * @param string route
 * @param string document
 * @returns html
 */
function cargarPlus(route, document) {
	if(document === undefined || document === '') {
		document = $('#cedasesoras').val();  
	}
    $.ajax({
        url: route,
        type: 'GET',
        data: {
            'document': document
        },
        cache: false,
        datatype: 'json',
        beforeSend: function () {
            $("#loadingSaveOrder").show();
        },
        complete: function () {
            $("#loadingSaveOrder").hide();
        },
        success: function (data) {
            if (!data.error) {
                $("#newplu").append('<option></option>');
                $.each(data.products, function (key, name) {
                    $("#newplu").append('<option value=' + name.code_plu + ' data-ispackage=' + name.is_package + '>(' + name.code_plu + ') - ' + name.name_plu + '</option>');
                });
            } else {
                showError('error', 'Error listando Códigos');
            }
        },
        error: function (xhr, textStatus, thrownError) {
            showError('error', 'Error listando Códigos');
        }
    });
}
/**
 * Metodo para carggar los usuarios para crearles pedidos como administrador
 * @param string route
 * @returns html
 */
function cargarUsuarios(route) {

    $.ajax({
        url: route,
        type: 'GET',
        data: {
        	adviser: $("#cedasesoras").val()
        },
        cache: false,
        datatype: 'json',
        beforeSend: function () {
            $("#loadingSaveOrder").show();
        },
        complete: function () {
            $("#loadingSaveOrder").hide();
        },
        success: function (data) {
        	console.log(data);   
            if (!data.error) {
            	//Mostramos los datos del usuario
            	showUserData(data.user);
            	//Validamos la asesora
            	validAdviser(routeValidAsesora, data.user.Cedula, data.user.Campana, data.user.CodigoZona);
            } else {
                showError('error', data.message);
            }
        },
        error: function (xhr, textStatus, thrownError) {
            showError('error', 'Error listando usuarios');
        }
    });
}

/**
 * Función para mostrar los datos del usuario al momento de crear pedido dz a la asesora
 * @param user
 * @returns
 */
function showUserData(user) {
	//Seteamos el valor del campo de busqueda
	$('#adviserName').val(user.Nombre);
	$('#adviserCampaing').val(user.Campana);
	$('#panelAdviserName').show();
	$('#panelAdviserCampaing').show();
	$('#panelSeparator').show();
	//Seteamos el valor de los campos ocultos
	//Campaña actual
	$('#campanaactual').val(user.Campana);
	//Documento
	$('#document').val(user.Cedula);
	//Zona actual
	$('#zonaactual').val(user.CodigoZona);
	//Agregamos los datos de envio de la asesora
	$('#adviserPersonalData').append(
		'Nombre: ' + user.Nombre + '<br/>' + 
		'Dirección: ' + user.Direccion + '<br/>' + 
		'Barrio: ' + user.Barrio + '<br/>' +
		'Municipio: ' + user.Municipio + '<br/>' +
		'Departamento: ' + user.Departamento + '<br/>' +
		'Télefono: ' + user.Telefono + '<br/>'
	);
	
	//Muestra el div con los datos de envio de la asesora
    $('#adviserAddressInfo').show();
}

/**
 * METODO PARA GUARDAR LOS PEDIDOS 
 * @returns boolean
 */
function saveOrder() {
    //VARIABLE PRODUCTOS
    var jsonPds = {};
    $(".cantProd").each(function (index) {
        var plu = $(this).parent('td').parent('tr').data('plu');
        jsonPds[plu] = {
            'cantidad': $(this).val()
        };
    });
    $.ajax({
        url: SaveOrder,
        type: 'POST',
        data: {
            '_token': $("input[name='_token']").val(),
            'pedidoId': $("#pedidoId").val(),
            'totalFacturaPedido': $('#hidnTotalFacturaPedido').val(),
            'totalCatalogoPedido': $('#hidnTotalCatalogoPedido').val(),
            'calculadoPedido': $('#hidnCalculadoPedido').val(),
            'mispuntos': $('#hidnPuntos').val(),
            'document': $('#cedasesoras').val(),
            'campanaactual': $('#campanaactual').val(),
            'products': jsonPds
        },
        cache: false,
        datatype: 'json',
        async: true,
        beforeSend: function () {
            $(":submit").attr("disabled", true);
        },
        complete: function () {
            $(":submit").attr("disabled", false);
        },
        success: function (data) {
        	console.log(data);
        	console.log($('#cedasesoras').val());  
        	
            if (!data.error) {
                if (data.orderId != 'null') {
                    $("#pedidoId").val(data.orderId);
                }
                return true;
            } else {
                showError('error', data.messages);
                $("#loadingSaveOrder").hide();
                return false;
            }

        },
        error: function (xhr, textStatus, thrownError) {
            showError('error', 'Error guardando el pedido');
            return false;
        }
    });
    return true;
}

var isRemovePlu = false;  

/**
 * Metodo para eliminar una fila
 * 
 * @param string thisRow
 */
function removeRow(thisRow) {
	isRemovePlu = true;  
    thisRow.parent("td").parent("tr").remove();
	calcularTotales();
}

/**
 * Metodo para mostrar un error con sweet alert
 * 
 * @param string type
 * @param string msg
 */

function showError(type, msg) {
    var title;
    var url = '';
    var animation = true;
    if (type === 'error') {
        title = 'Error';
    } else if (type === 'warning') {
        title = 'Advertencia';
        type = 'info';
    } else {
        title = '';
    }
    if (type === 'error' || type === 'info') {
    	if(type === 'info') {
    		type = '';
    		url = 'https://image.flaticon.com/icons/svg/189/189664.svg';
    		animation = false;
		}
        swal({
            title: title,
            imageUrl: url,
            animation: animation,
            text: msg,
            type: type,
            html: true,
            closeOnConfirm: false,
            showConfirmButton: true,
            confirmButtonText: 'Aceptar',
            confirmButtonClass: 'bgmarca font-white'
        });
    } else {
        toastr.success(msg);
    }
}

/**
 * Metodo que formatea los numeros y pone las unidades de miles
 * 
 * @param string nStr
 * @returns string
 * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
 * @copyright 2017 Linea Directa
 */
function addCommas(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + '.' + '$2');
    }
    return x1 + x2;
}

/**
 * Metodo para crear la cookie de las alertas
 * 
 * @returns {undefined}
 * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
 * @copyright 2017 Linea Directa
 */
function setCookieInfo(cookieName) {
	console.log(cookieName);  
    var now = new Date();
    var time = now.getTime();
    time += 24 * 3600 * 1000;
    now.setTime(time);  
	
    document.cookie = cookieName + '=_' + (Date.now().toString(36) + Math.random().toString(36).substr(2, 5)) + '; expires=' + now.toUTCString() + '; path=/';
}

/**
 * Metodo para obtener la cookie de las alertas
 * @param string ckname
 * @returns {String}
 */
function getCookie(cookieName) {
    var name = cookieName + "=";
    var ca = document.cookie.split(';');

    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1);
        }

        if (c.indexOf(name) === 0) {
            return c.substring(name.length,c.length);
        }
    }    

    return "";
}

function loadHistoryOrdersTable(listRoute, tableId, routeDetail, enabledControls, showQuantity, filter) {    
	console.log(filter);
	var dt = $('#' + tableId).DataTable({   
        "iDisplayLength" : showQuantity,
        "paging":   enabledControls,
        "info": enabledControls,
        "searching": enabledControls,
        "ordering": enabledControls,
        "bFilter": enabledControls,
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": listRoute,
        "aLengthMenu": [[10, 20, 30, 50, 100, -1], [10, 20, 30, 50, 100, "Todos"]],
        "order": [[ 2, "desc" ]],
        "aoColumnDefs": [ 
          	{
          		"mData": "PedidoId",
          		"width": "15%",
          		"className": "dt-center",
          		"aTargets": [0],
          		'createdCell':  function (td, cellData, rowData, row, col) {
          			console.log(col);
       				$(td).attr('id', 'td_' + cellData);
       				$(td).attr('data-update', rowData.Procesado);      
    			}
      		},
      		{
          		"mData": "Fecha",
          		"width": "25%",
          		"className": "dt-center",
          		"aTargets": [1],
          		"mRender": function(resource) {
          			return resource.substr(0, 10)
          		}
      		},
      		{
          		"mData": "CampanaId",
          		"width": "10%",
          		"className": "dt-center",
            	"aTargets": [2]
        	},
        	{
          		"mData": "CodigoZona",
          		"width": "10%",
          		"className": "dt-center",
            	"aTargets": [3]
        	},
      		{
				"mData": "NumeroDcto",  
				"className": "dt-left",
            	"aTargets": [4]
        	},
          	{
          		"mData": "NombreAsesora",
          		"width": "35%",
            	"aTargets": [5]
        	},
            {
            	"mData": "MontoMin",
            	"className": "dt-center",
            	"aTargets": [6],
            	"mRender": function(resource, display, data) {
            		return resource === 0 ? '<span class="font-marca">Si</span>' : '<span>No</span>';
            	}
        	},
            {
            	"mData": "CupoMax",
            	"className": "dt-center",
            	"aTargets": [7],
            	"mRender": function(resource, display, data) {
            		return resource === 0 ? '<span class="font-marca">Si</span>' : '<span>No</span>';
            	}
        	},
            {
            	"mData": "Procesado",
            	"className": "dt-center",
            	"width": "15%",
            	"aTargets": [8],
            	"mRender": function(resource) {
            		console.log('Estado pedido -->: ' + resource);   
          			var status = '';
          			var statusClass = '';
          			
          			//Estados de pedidos
                	if (resource === '0') {
                		//Nombre enviado y color gris
                    	statusClass = 'font-grey-gallery';
                    	status = 'Enviado';
   	             	} 
   	             	
   	             	if (resource === '1') {
                    	statusClass = 'font-grey-gallery';
                    	status = 'Enviado';
                	} 
                	
                	if (resource === '2') {
                    	statusClass = 'font-grey-gallery';
                    	status = 'Guardado';
                	} 
                	
                	if (resource === '3') {     
                    	statusClass = 'font-marca';
                    	status = 'Facturado';
                	}

					return '<span class="' + statusClass + ' "><label>' + status + '</label></span>';

            	}                	
        	},
        	{
        		"mData": "Respuesta",
        		"className" : " details-control",
        		"orderable": false,
				"mRender": function(resource, display, data) {
					return '<a href="' + routeDetail + "/" + data.PedidoId + "/" + data.Procesado + "/" + data.NumeroDcto + '" class="btn btn-outline btn-xs bgmarca font-white btn-circle">' +
								'<i class="fa fa-eye"></i> Ver' + 
						   '</a>';
				},
				"aTargets": [9]            	
        	}
        ],
        "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
            $(nRow).addClass('odd gradeX');
            return nRow;
        },
        "initComplete": function(settings, json) {
        	$('.dt-loader').hide();
        	$("#loadingOrder").hide();
        },
        "fnServerData": function(sUrl, aoData, fnCallback, oSettings) {  
        	$('#tableOrders_filter').css('margin-top', '-20px');
	    	$('#tableOrders_filter').children().hide();
	    	$('#tableOrders_filter').append(
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
	        
	        if(filter !== null && filter !== "") {  
	        	data.$filter = filter;  
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
    	}
    });
}

/**
 * Función pedidos en curso administrador / dz
 * @param route
 * @param urlDetail
 * @param urlDelete
 * @returns
 */
function loadCourseOrdersTable(listRoute, tableId, detailRouteTable, detailRoute, deleteRoute, enabledControls, showQuantity, filter) {
	
//	console.log(fnServerOData);
	
	var dt = $('#' + tableId).DataTable({   
        "paging":   enabledControls,
        "info": enabledControls,
        "searching": enabledControls,
        "ordering": enabledControls,
        "bFilter": enabledControls,
        "aLengthMenu": [[10, 20, 30, 50, 100, -1], [10, 20, 30, 50, 100, "Todos"]],
        "order": [[ 2, "desc" ]],
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": listRoute,
        "aoColumnDefs": [ 
          	{
          		"mData": "PedidoId",
          		"width": "15%",
          		"className": "dt-center",
          		"aTargets": [0],
          		"searchable": true,
          		'createdCell':  function (td, cellData, rowData, row, col) {
          			console.log(cellData);  
       				$(td).attr('id', 'td_' + cellData);
       				$(td).attr('data-update', rowData.Procesado);      
    			}
      		},
      		{
          		"mData": "CampanaId",
          		"className": "dt-center",
          		"width": "10%",
            	"aTargets": [1]
        	},
        	{
          		"mData": "CodigoZona",
          		"className": "dt-center",
          		"width": "10%",
            	"aTargets": [2]
        	},
          	{
          		"mData": "NombreAsesora",
          		"width": "35%",
            	"aTargets": [3]
        	},
            {
            	"mData": "MontoMin",
            	"width": "5%",
            	"className": "dt-center",
            	"aTargets": [4],
            	"mRender": function(resource, display, data) {
            		return resource === 0 ? '<span class="font-marca">Si</span>' : '<span>No</span>';
            	}
        	},
            {
            	"mData": "CupoMax",
            	"width": "5%",
            	"className": "dt-center",
            	"aTargets": [5],
            	"mRender": function(resource, display, data) {
            		return resource === 0 ? '<span class="font-marca">Si</span>' : '<span>No</span>';
            	}
        	},
        	{
				"mData": "SaldoPagar",   
				"className": "dt-left",
            	"aTargets": [6],
            	"mRender": function(resource) {
            		return resource === null ? '0' : addCommas(resource);
            	}         	
        	},
            {
            	"mData": "Procesado",
            	"className": "dt-center",
            	"aTargets": [7],
            	"mRender": function(resource) {
          			var status = '';
          			var statusClass = '';
          			
          			//Estados de pedidos
                	if (resource === '0') {
                		//Nombre enviado y color gris
                    	statusClass = 'font-grey-gallery';
                    	status = 'Enviado';
   	             	} 
   	             	
   	             	if (resource === '1') {
                    	statusClass = 'font-grey-gallery';
                    	status = 'Enviado';
                	} 
                	
                	if (resource === '2') {
                    	statusClass = 'font-grey-gallery';
                    	status = 'Guardado';
                	} 
                	
                	if (resource === '3') {
                    	statusClass = 'font-marca';
                    	status = 'Facturado';
                	}

					return '<span class="' + statusClass + ' "><label>' + status + '</label></span>';
            	}                	
        	},
        	{
        		"mData": "Respuesta",
        		"bSearchable": false,  
        		"orderable": false,
        		"className" : " details-control",
				"mRender": function() {
					return '<a class="btn btn-outline btn-xs bgmarca font-white btn-circle"><i class="fa fa-eye"></i> Ver</a>';
				},
				"aTargets": [8]            	
        	}
        ],
        "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
            $(nRow).addClass('odd gradeX');
            return nRow;
        },
        "initComplete": function(settings, json) {
        	
        	$('#tableUsers_length').parent().removeClass('col-sm-6 col-md-6');
//    		$('#tableUsers_filter').parent().removeClass('col-sm-6 col-md-6');
//    		$('#tableUsers_length').parent().addClass('col-sm-4 col-md-4');
//    		$('#tableUsers_filter').parent().addClass('col-sm-4 col-md-4');
        	
        	
        	
        	$('.dt-loader').hide();
        	$("#loadingOrder").hide();
//        	$('#tableOrders_filter').css('margin-top', '-20px');
//        	$('#tableOrders_filter').children().hide();
//        	$('#tableOrders_filter').append(
//    			'<br/>' + 
//				'<a id="label-filter" class="font-dark" onclick="$(\'#filterAdvancedGroup\').removeClass(\'hide\')">' +
//					'<h6 class="text-center bold" style="margin-left: 50px !important;"><i class="fa fa-filter"></i> Búsqueda avanzada</h6>' +
//				'</a>' 
//        	);
        },
        "fnServerData": function(sUrl, aoData, fnCallback, oSettings) {  
        	$('#tableOrders_filter').css('margin-top', '-20px');
        	$('#tableOrders_filter').children().hide();
        	$('#tableOrders_filter').append(
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
            
            if(filter !== null && filter !== "") {  
            	data.$filter = filter;
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
        "iDisplayLength" : showQuantity,
        "iODataVersion": 4,
        "bUseODataViaJSONP": false,
        "language": {
    		"url": "../metronic/custom-pedidos-web/scripts/Spanish.json"
    	}
    });

 	var detailRows = [];
 	var tr_old = null;
 	var row_old = null;
	var idx_old = null;
         
    $('#' + tableId + ' tbody').on( 'click', 'tr td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = dt.row( tr );
        var idx = $.inArray( tr.attr('id'), detailRows );
        
        if ( tr_old !== null && row_old !== null && idx_old !== null ) {
            tr_old.removeClass( 'details' );
            row_old.child.hide();
            // Remove from the 'open' array
            detailRows.splice( idx_old, 1 );
            
            tr_old = null;
 			row_old = null;
			idx_old = null;
        }
        
        if(tr_old === null) {
        	tr_old = tr;
        	row_old = row;
        	idx_old = idx;
        }
        
        tr.addClass( 'details' );
        row.child( orderDetailFormat( row.data(), detailRouteTable, detailRoute, deleteRoute ) ).show();

        // Add to the 'open' array
        if ( idx === -1 ) {
            detailRows.push( tr.attr('id') );
        }
    });
 
    // On each draw, loop over the `detailRows` array and show any child rows
    dt.on( 'draw', function () {
        $.each( detailRows, function ( i, id ) {
            $('#'+id+' td.details-control').trigger( 'click' );
        } );
    } );
}

/**
 * Función para mostrar el detalle del pedido en la tabla
 * @param data
 * @returns
 */
function orderDetailFormat ( data, routeDetailRow, routeDetail, routeDelete ) {
	console.log(routeDetailRow);
	
	var td_ = "td_loader_" + data.PedidoId + "_";
	var routeDetailRow = routeDetailRow + "='" + data.PedidoId + "')";
	var preloader = '<div id="' + td_ + '" class="portlet box" style="margin-bottom: 0px !important;"> ' +
		   				'<div class="portlet-body">' +
		   					'<i class="fa fa-circle-o-notch fa-spin fa-1x fa-fw"></i> Cargando...' +
   						'</div>' +
					'</div>';
	
	$.ajax({
		url: routeDetailRow,
		method: 'GET',
		dataType: 'json',
		cache: false,
        success: function (data) {
        	console.log(data);
        	var buttons = '';
       	 	var html = '';
        	
       	 	if(data.value.length <= 0) {
        		html = '<div id="bootstrap_alerts_demo"> <div id="prefix_953166307522" class="custom-alerts alert alert-info fade in" style="margin-bottom: 0px !important;"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>No se encontró el detalle del pedido..</div></div>';
        		$("#" + td_).empty();
            	$("#" + td_).append(html);
            	return;
        	}
        	if(data.value[0].Procesado === '0' && data.value[0].Editable || data.value[0].Procesado === '2' && data.value[0].Editable) {
        		buttons = '<a href="' + routeDetail + "/" + data.value[0].PedidoId + "/" + data.value[0].Procesado + "/" + data.value[0].NumeroDcto + '" class="btn btn-sm btn-circle red easy-pie-chart-reload">' +
        				  	'<i class="fa fa-pencil"></i> Editar ' + 
        				  '</a>' +
        				  '&nbsp;' + 
        				 '<a data-btn-cancel-label="No" data-btn-ok-label="Sí" title="" data-original-title="¿Estás segura que quieres borrar el pedido?" data-toggle="confirmation" href="' + routeDelete + "/" + data.value[0].PedidoId + "/true?document=" + data.value[0].NumeroDcto + '" class="btn btn-sm btn-circle red easy-pie-chart-reload">' +
        				  	'<i class="fa fa-trash"></i> Eliminar ' + 
        				  '</a>';
        	} else {
        		buttons = '<a href="' + routeDetail + "/" + data.value[0].PedidoId + "/" + data.value[0].Procesado + "/" + data.value[0].NumeroDcto + '" class="btn btn-transparent bgmarca font-white btn-sm btn-circle">' +
        					'<i class="fa fa-eye"></i> Detalle ' + 
        				  '</a>';
        	}
        	
        	html = '<div class="portlet box btnnewped" style="margin-bottom: 0px !important;">' +
						'<div class="portlet-title">' + 
							'<div class="caption">' +
			                    'Detalle pedido' +
							'</div>' +
							'<div class="actions">' +
			                	buttons +
			                '</div>' +
						'</div>' +
						'<div class="portlet-body">' + 
							'<div class="row">' +
								'<div class="col-md-4">' +
			    					'<ul style="text-align: left;">' +
//			    						'<li>' + 
//			        						'<strong class="dtr-title">Código zona: </strong>' + 
//			        						'<span class="dtr-data">' + data.value[0].CodigoZona + '</span>' +
//			        					'</li>' +
				            			'<li>' + 
				            				'<strong class="dtr-title">Fecha pedido: </strong>' + 
				            				'<span class="dtr-data">' + data.value[0].Fecha.substr(0, 10) + '</span>' +
				            			'</li>' +
				            			'<li>' +
				            				'<strong class="dtr-title">Total pedido: </strong>' + 
				            				'<span class="dtr-data">$' + addCommas(data.value[0].TotalFactura) + '</span>' +
				            			'</li>' + 
										'<li>' + 
				            				'<strong class="dtr-title">Cupo: </strong>' + 
				            				'<span class="dtr-data">$' + addCommas(data.value[0].CupoCatalogo) + '</span>' +
				            			'</li>' +
				                	'</ul>' +
								'</div>' +
								
								'<div class="col-md-4">' +
			    					'<ul style="text-align: left;">' +
				                		'<li>' +
				                			'<strong class="dtr-title">Cédula: </strong>' +
				                			'<span class="dtr-data">' + data.value[0].NumeroDcto + '</span>' +
				            			'</li>' +
				            			'<li>' +
			                				'<strong class="dtr-title">Teléfono: </strong>' +
			                				'<span class="dtr-data">' + data.value[0].Telefono + '</span>' +
			                			'</li>' +
				            			'<li>' + 
				            				'<strong class="dtr-title">Clasificación: </strong>' + 
				            				'<span class="dtr-data">' + data.value[0].ClasificacionValor + '</span>' +
				            			'</li>' +
				                	'</ul>' +
								'</div>' +
								
								'<div class="col-md-4">' +
			    					'<ul style="text-align: left;">' +
				            			'<li>' + 
				            				'<strong class="dtr-title">Mail plan: </strong>' + 
				            				'<span class="dtr-data">' + data.value[0].MailPlan + '</span>' +
				            			'</li>' +
				            			'<li>' +
				        					'<strong class="dtr-title">División: </strong>' + 
				        					'<span class="dtr-data">' + data.value[0].Division + '</span>' +
				        				'</li>' + 
				            			'<li>' +
				            				'<strong class="dtr-title">Stencil: </strong>' + 
				            				'<span class="dtr-data">' + data.value[0].EstadoStencil + '</span>' +
				            			'</li>' + 
				                	'</ul>' +
								'</div>' +    					
							'</div>' +
						'</div>' + 
					'</div>';
        	
        	$("#" + td_).empty();
        	$("#" + td_).append(html);
        },
        error: function (xhr, textStatus, thrownError) {
        	console.log(xhr);
        	console.log(textStatus);
        	console.log(thrownError);
        	$("#" + td_).empty();
            $("#" + td_).append('<div id="bootstrap_alerts_demo"> <div id="prefix_905644058818" class="custom-alerts alert alert-danger fade in" style="margin-bottom: 0px !important;"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>Error al obtener el detalle del pedido.</div></div>');
        }
	});
	
	return preloader;
}


/**
 * Pedidos Historicos Aministrador DZ
 * @param route
 * @param urlDetail
 * @param urlDelete
 * @returns
 */
/*function listOrdersHistoryAdministrator(route, urlDetail, urlDelete) {
    $.ajax({
        url: route,
        type: 'GET',
        cache: false,
        datatype: 'json',
        beforeSend: function () {
            $("#loadingOrder").show();
        },
        complete: function () {
            $("#loadingOrder").hide();
        },
        success: function (data) {
        	console.log(data); 
        	
            if (!data.error && data.orders.length > 0) {
                $.each(data.orders, function (key, name) {
                    var btnDel = '';
                    var clPro = '';
                    var txtPro = '';
                    //Si el pedido es procesado 0 / 2
                    if (name.Procesado == '0' || name.Procesado == '2') {
                    	//Verifica que el pedido sea editable
                        if (name.Editable) {
                        	//Agrega el boton eliminar
                            btnDel = $("<a>")
                                    .attr("href", urlDelete + "/" + name.PedidoId + "/true?document=" + name.NumeroDcto)
                                    .attr("class", "btn-xs font-marca")
                                    .attr("data-toggle", "confirmation")
                                    .attr("data-original-title", "¿Estás segura que quieres borrar el pedido?")
                                    .attr("title", "")
                                    .attr("data-btn-ok-label", "Si")
                                    .attr("data-btn-cancel-label", "No")
                                    .html("<i class='fa fa-trash-o'></i>");
                        }
                    }

                    //Estados dwe pedidos para mostrar en el listado de los ultimos 5
                    if (name.Procesado == '0') {
                    	//Nombre enviado y color gris
                        clPro = 'font-grey-gallery';
                        txtPro = 'Enviado';
                    } else if (name.Procesado == '1') {
                        clPro = 'font-grey-gallery';
                        txtPro = 'Enviado';
                    } else if (name.Procesado == '2') {
                        clPro = 'font-grey-gallery';
                        txtPro = 'Guardado';
                    } else if (name.Procesado == '3') {
                        clPro = 'font-marca';
                        txtPro = 'Facturado';
                    }

                    //Boton ver
                    var btnView = $("<a>")
                            .attr("href", urlDetail + "/" + name.PedidoId + "/" + name.Procesado + "/" + name.NumeroDcto)
                            .attr("class", "btn btn-outline btn-xs bgmarca font-white btn-circle")
                            .html("<i class='fa fa-eye'></i> Ver");
                    //LLENO LA TABLA CON LOS ULTIMOS 5 PEDIDOS
                    $("#tblListOrdersHistory").find("tbody")
                        .append($("<tr>")
                    		.append(
                    			$("<td style='cursor: pointer;'" + 
                    			       "class='details-control' " + 
                    			       "id='td_" + name.PedidoId + "' " + 
                    			       "data-max='" + name.CupoMax + "' " + 
                    			       "data-min='" + name.MontoMin + "' " +
                    			       "data-name='" + name.NombreAsesora + "' " + 
                    			       "data-document='" + name.NumeroDcto + "' " +
                    			       "data-zone='" + name.CodigoZona + "' " +
                    			       "data-date='" + name.Fecha.substr(0, 10) + "' " +
                    			       "data-campaing='" + name.CampanaId + "' " +
                    			       "data-division='" + name.Division + "' " +
                    			       "data-mailplan='" + name.MailPlan + "' " +
                    			       "data-total='" + name.SaldoPagar + "'" +
                    			"'>")
                    		  	.text(name.PedidoId)
                            ).append(
                    			$("<td>").
                    			text(name.CampanaId)
                            ).append(
                        		$("<td>")
                                .text(name.NombreAsesora)
                            ).append(
                        		$("<td>")
                                .text(name.CodigoZona)
                            ).append($("<td>")
                                .append($("<span>")
                                    .attr("class", "label " + clPro + "")
                                    .html(txtPro)
                                )
                            )
                            .append($("<td>")
                        		.append(btnView)
                        		.append(btnDel)
                            )
                    );
                });
                //Muestra la tabla
                $("#tblListOrdersHistory").show();
                //Mostrar boton ver todos los pedidos / Dashboard
                $("#showAllOrders").show();
                //VUELVO LA TABLA DE PEDIDOS UN DATATABLE PARA ORDENAR POR FECHA
                //ERliminar datatable para mostrar la ayuda 
                var dt = $('#tblListOrdersHistory').DataTable({
                    "language": {
                        "url": "metronic/custom-pedidos-web/scripts/Spanish.json"
                    },
                    "bFilter": false,
                    "lengthChange": false,
                    "searching": false,
                    "bPaginate": false,
                    "info": false,
                    "order": [[2, "desc"]] //Ordenado por fecha posición 2 fecha inicia en 0
                });
                
                
             // Array to track the ids of the details displayed rows
                var detailRows = [];
             
                $('#tblListOrdersHistory tbody').on( 'click', 'tr td.details-control', function () {
                    var tr = $(this).closest('tr');
                    console.log(dt);
                    var row = dt.row( tr );
                    var idx = $.inArray( tr.attr('id'), detailRows );
             
                    if ( row.child.isShown() ) {
                        tr.removeClass( 'details' );
                        row.child.hide();
             
                        // Remove from the 'open' array
                        detailRows.splice( idx, 1 );
                    }
                    else {
                        tr.addClass( 'details' );
                        row.child( format( row.data() ) ).show();
             
                        // Add to the 'open' array
                        if ( idx === -1 ) {
                            detailRows.push( tr.attr('id') );
                        }
                    }
                } );
             
                // On each draw, loop over the `detailRows` array and show any child rows
                dt.on( 'draw', function () {
                    $.each( detailRows, function ( i, id ) {
                        $('#'+id+' td.details-control').trigger( 'click' );
                    } );
                } );
                
                
            } else {
            	//Mostrar mensaje
                $("#tblListOrdersHistory > tbody")
                        .append('<tr><td colspan="6"><div id="alertResult" class="alert alert-danger">No se encontraron resultados</div></td></tr>');
                //Oculta tabla listado pedido
//                $("#tblListOrdersHistory").hide();
                //Oculta boton ver todos los pedidos
                $("#showAllOrders2").hide();
            }
            //Mostramos div que contiene la tabla ed pedidos
            $("#porletHistoryOrders").show();
            //Toolip para eliminar
            $('[data-toggle=confirmation]').confirmation({
                rootSelector: '[data-toggle=confirmation]',
                placement: 'top',
                btnCancelClass: 'btn btn-sm btn-default',
                btnOkClass: 'btn btn-sm btn-default'
            });
        },
        error: function (xhr, textStatus, thrownError) {
            showError('error', 'Error listando ordenes');
        }
    });
} */



/*$('.btn-historyorders').on('click', function () {
	$('.capaingcourse').hide();
	$('.historyorders').show();
	$('#tblListOrders').hide();
});*/

$('.btn-courseorders').on('click', function () {
	$('.historyorders').hide();
	$('.capaingcourse').show();
	$('#tblListOrders').show();
	$('#tblListOrdersHistory').hide();
});




function findAllZones(route) {
	$.ajax({
		url: route,
		method: 'GET',
		dataType: 'json',
		cache: false,
		data: {
			'mailPlan': $('#filterMailPlan').val()  
		},
        success: function (data) {
        	console.log(data);
        	
        	var campaingZone = [];
        	
        	$("#filterZone").empty();
        	$("#filterCampaing").empty();
			$("#filterZone").append($("<option />").val('').text('--'));
			$("#filterCampaing").append($("<option />").val('').text('--'));
    		$.each(data, function (key, value) {
    			$("#filterZone").append($("<option />").val(value.codZona).text(value.codZona));
    			
    			if(jQuery.inArray(value.campana, campaingZone) === -1) {
    				$("#filterCampaing").append($("<option />").val(value.campana).text(value.campana));
    				campaingZone.push(value.campana.toString());
    			}
    		});
    		
    		console.log(jQuery.inArray("12334", campaingZone));
    		console.log(campaingZone);
        },
        error: function (xhr, textStatus, thrownError) {
        	console.log(xhr);
        	console.log(textStatus);
        	console.log(thrownError);
        }
	});
}
