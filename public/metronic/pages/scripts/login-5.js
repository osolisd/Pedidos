var Login = function () {

    var handleLogin = function () {

        $('.login-form').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                username: {
                    required: true
                },
                password: {
                    required: true
                },
                remember: {
                    required: false
                }
            },

            messages: {
                username: {
                    required: "Username is required."
                },
                password: {
                    required: "Password is required."
                }
            },

            invalidHandler: function (event, validator) { //display error alert on form submit                   
                $('.alert-danger').hide();
                $('.emptyform', $('.login-form')).show();
            },

            highlight: function (element) { // hightlight error inputs
                $(element)
                        .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function (label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function (error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function (form) {
                form.submit(); // form validation success, call ajax form submit
            }
        });

        $('.login-form input').keypress(function (e) {
            if (e.which == 13) {
                if ($('.login-form').validate().form()) {
                    $('.login-form').submit(); //form validation success, call ajax form submit
                }
                return false;
            }
        });

        $('.forget-form input').keypress(function (e) {
            if (e.which == 13) {
                if ($('.forget-form').validate().form()) {
                    $('.forget-form').submit();
                }
                return false;
            }
        });

        $('#forget-password').click(function () {
            $('.login-form').hide();
            $('.login-intro').hide();
            $('.forget-form').show();
        });

        $('#back-btn, #back-btn1, #back-btn2, #back-btn3').click(function () {
        	//Recargamos la pagina
        	location.reload();
            /*$('.login-form').show();
            $('.login-intro').show();
            $('.forget-form').hide();*/
        });

        $('.formuser').submit(function () {
            if ($(this).valid()) {
            	var button = $('#buscar-doc');
            	//Inhabilitamos el botón
            	$(button).attr('disabled', 'disabled');
            	//Mostramos anicación cargando
            	$(button).children().show();
            	//Eliminamos el label de mensajes de error
            	$('#errorLabel').remove();
            	
            	//Llamamos el servicio para validar cedula
            	$.ajax({
			        url: checkDocumentRoute,
			        type: 'GET',
			        cache: false,
			        datatype: 'json',
			        data: {
			        	document: $('#documento').val()
			        },
			        beforeSend: function () {
			            
			        },
			        complete: function () {
			        	//Habilitamos el botón
			        	$(button).removeAttr('disabled');
			        	//Ocultamos animación cargando
						$(button).children().hide();
			        },
			        success: function (data) {
			        	console.log(data);
			        	
			        	if(data.error) {
			        		$('#documento').parent().append('<label id="errorLabel" class="control-label text-danger">' + data.messages + '</label>');
			        	}
			        	
						if(!data.error) {
							if(data.email) {
								//Mostramos el tab 
								$('#recoveryTabs').show();
								//Mostramos el titulo del tab correo
								$('#recoveryEmail').show();
								//Ocultamos el formulario de escribir documento
								$('.paso1correo').hide();
								//Agregamos el correo
								$('#emailAdviser').append(data.content);
								//Mostramos los tabs
								$('.nav').show();  
								//Ocultamos el tab de preguntas
								$('.title-question').hide();
								//Mostramos el formulario de enviar correo
								$('.paso2correo').show();
								
								$('#key').val(data.key);
								
							} else {
								$('.paso1correo').hide();
								$('.title-email').hide();
								$('.title-email').removeClass('active');
								$('.nav').show();  
								$('#tab_correo').removeClass('active');
								$('#tab_preguntas').addClass('active');
								$('.title-question').addClass('active');
								$('.title-question').show();
								$('.preload').show();
								//Buscamos las preguntas
								findQuestions();
							}									
						}
				        
			        },
			        error: function (xhr, textStatus, thrownError) {
			            showError('error', 'Error validando tú documento.');
			        }
			    });
            	
            	
//                $('.paso1correo').hide();
//                $('.paso2correo').show();
            }
        });

        $('.formuser').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'font-red', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                documento: {
                    required: true
                }
            },

            messages: {
                documento: {
                    required: "Ingrese un documento válido."
                }
            }
        });
        
        $('#btn-send-email').on('click', function () {
        	var button = this;
        	
        	//Llamamos el servicio para enviar el correo
        	$.ajax({
                url: sendEmailRoute,
                type: 'GET',
                cache: false,
                datatype: 'json',
                data: {
                	document: $("#documento").val(),
                	key: $('#key').val()  
                },
                beforeSend: function () {
                	//Inhabilitamos el botón
                	$(button).attr('disabled', 'disabled');
                	//Mostramos anicación cargando
                	$(button).children().show();
                },
                complete: function () {
                	//Habilitamos el botón
                	$(button).removeAttr('disabled');
                	//Ocultamos animación cargando
        			$(button).children().hide();
                },
                success: function (data) {
                	console.log(data);
                	if(data.error) {
                		$('#errorSendEmail').empty();
                		$('#errorSendEmail').append(data.messages);
                	}
                	
                	if(!data.error) {
                		swal({
                            title: "Correo Enviado!",
                            text: data.messages,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonColor: "#5bc0de",
                            confirmButtonText: "Aceptar",
                            closeOnConfirm: false,
                            html: true,
                            animation: "slide-from-top"
                        }, function (isConfirm) {
                            if (isConfirm) {
                                //Recargamos la pagina
                                location.reload();
                            }
                        });
                	}
                },
                error: function (xhr, textStatus, thrownError) {
                    showError('error', 'Error validando tú documento.');
                }
            });
        });
        
        $('.formupreguntas').submit(function () {
            if ($(this).valid()) {
            	checkQuestions();
            	
            	console.log($("input[name='questionName']:checked").val());
            }
        });

        $('.formupreguntas').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'font-red', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
            	questionName: {
                    required: true
                },
                questionDocument: {
                    required: true
                },
                questionLastName: {
                    required: true
                }
            },

            messages: {
            	questionName: {
                    required: "Seleccione una opción."
                },
                questionDocument: {
                    required: "Seleccione una opción."
                },
                questionLastName: {
                    required: "Seleccione una opción."
                }
            },
            errorPlacement: function (error, element) { // render error placement for each input type
                if (element.parents('.mt-radio-list') || element.parents('.mt-checkbox-list')) {
                    if (element.parents('.mt-radio-list')[0]) {
                        error.appendTo(element.parents('.mt-radio-list')[0]);
                    }
                    if (element.parents('.mt-checkbox-list')[0]) {
                        error.appendTo(element.parents('.mt-checkbox-list')[0]);
                    }
                } else if (element.parents('.mt-radio-inline') || element.parents('.mt-checkbox-inline')) {
                    if (element.parents('.mt-radio-inline')[0]) {
                        error.appendTo(element.parents('.mt-radio-inline')[0]);
                    }
                    if (element.parents('.mt-checkbox-inline')[0]) {
                        error.appendTo(element.parents('.mt-checkbox-inline')[0]);
                    }
                } else if (element.parent(".input-group").size() > 0) {
                    error.insertAfter(element.parent(".input-group"));
                } else if (element.attr("data-error-container")) {
                    error.appendTo(element.attr("data-error-container"));
                } else {
                    error.insertAfter(element); // for other inputs, just perform default behavior
                }
            }
        });

        $('.formnuevaclave').submit(function () {
            if ($(this).valid()) {
                
            	var button = $('#btn-recovery-password');
            	
            	//Llamamos el servicio para validar cedula
            	$.ajax({
                    url: recoveryPasswordRoute,
                    type: 'GET',
                    cache: false,
                    datatype: 'json',
                    data: {
                    	document: $('#documento').val(),
                    	password: $('#nuevaclave').val(),
                    	repeatpassword: $('#repetirclave').val()
                    },
                    beforeSend: function () {
                    	//Inhabilitamos el botón
                    	$(button).attr('disabled', 'disabled');
                    	//Mostramos anicación cargando
                    	$(button).children().show();
                    },
                    complete: function () {
                    	//Habilitamos el botón
                    	$(button).removeAttr('disabled');
                    	//Ocultamos animación cargando
            			$(button).children().hide();
                    },
                    success: function (data) {
                    	console.log(data);
                    	
                    	if(data.error) {
                    		$('#errorRecoveryPassword').empty();
                    		$('#errorRecoveryPassword').append(data.messages);
                    	}
                    	
                    	if(!data.error) {
                    		swal({
                                title: "Clave Recuperada!",
                                text: data.messages,
                                type: "success",
                                showCancelButton: false,
                                confirmButtonColor: "#5bc0de",
                                confirmButtonText: "Aceptar",
                                closeOnConfirm: false,
                                html: true,
                                animation: "slide-from-top"
                            }, function (isConfirm) {
                                if (isConfirm) {
                                    //Recargamos la pagina
                                    location.reload();
                                }
                            });
                    	}
                    },
                    error: function (xhr, textStatus, thrownError) {
                        showError('error', 'Error validando tú documento.');
                    }
                });
            }
        });
        
        $('.formnuevaclave').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'font-red', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                nuevaclave: {
                    required: true
                },
                repetirclave: {
                    required: true,
                    equalTo: "#nuevaclave"
                }
            },

            messages: {
                nuevaclave: {
                    required: "Ingrese una contraseña."
                },
                repetirclave: {
                    required: "Ingrese un contraseña.",
                    equalTo: "Las contraseñas no coinciden"
                }
            }
        });
    }

    return {
        //main function to initiate the module
        init: function () {

            handleLogin();

            // init background slide images
            /*$('.login-bg').backstretch([
             "../assets/pages/img/login/bg1.jpg",
             "../assets/pages/img/login/bg2.jpg",
             "../assets/pages/img/login/bg3.jpg"
             ], {
             fade: 1000,
             duration: 8000
             }
             );*/

            $('.forget-form').hide();

        }

    };

}();

function findQuestions() {
	var button = $('#buscar-doc');
	
	//Llamamos el servicio para validar cedula
	$.ajax({
        url: findQuestionsRoute,
        type: 'GET',
        cache: false,
        datatype: 'json',
        data: {
        	document: $('#documento').val()
        },
        beforeSend: function () {
        	//Inhabilitamos el botón
        	$(button).attr('disabled', 'disabled');
        	//Mostramos anicación cargando
        	$(button).children().show();
        },
        complete: function () {
        	//Habilitamos el botón
        	$(button).removeAttr('disabled');
        	//Ocultamos animación cargando
			$(button).children().hide();
        },
        success: function (data) {
        	console.log(data);
        	
        	if(!data.error) {
        		$('.login-footer').hide();
        		
        		for(var i = 0; i < data.questions.length; i ++) {
        			$('.questionAdviserName').append(
    					'<label class="mt-radio">' +
        					data.name[i] + 
        					'<input value="' + data.name[i] + '" name="questionName" aria-required="true" type="radio">' +
        					'<span></span>' +    					
    					'</label>'   					
        			);
        			
        			$('.questionAdviserDocument').append(
    					'<label class="mt-radio">' +
        					data.document[i] + 
        					'<input value="' + data.document[i] + '" name="questionDocument" aria-required="true" type="radio">' +
        					'<span></span>' +    					
    					'</label>'   					
        			);
        			
        			$('.questionAdviserDocumentDate').append(
    					'<label class="mt-radio">' +
        					data.date[i] + 
        					'<input value="' + data.date[i] + '" name="questionDocumentDate" aria-required="true" type="radio">' +
        					'<span></span>' +    					
    					'</label>'   					
        			);
        		}
        		
        		/*$.each(data.questions, function (key, value) {
        			$('.questionAdviserName').append(
    					'<label class="mt-radio">' +
        					value.name + 
        					'<input value="' + value.name + '" name="questionName" aria-required="true" type="radio">' +
        					'<span></span>' +    					
    					'</label>'   					
        			);
        			
        			$('.questionAdviserDocument').append(
    					'<label class="mt-radio">' +
        					value.id + 
        					'<input value="' + value.id + '" name="questionDocument" aria-required="true" type="radio">' +
        					'<span></span>' +    					
    					'</label>'   					
        			);
        			
        			$('.questionAdviserLastName').append(
    					'<label class="mt-radio">' +
        					value.lastName + 
        					'<input value="' + value.lastName + '" name="questionLastName" aria-required="true" type="radio">' +
        					'<span></span>' +    					
    					'</label>'   					
        			);
    			
        			console.log(value);
        		});*/
        		
        		$('.preload').hide();
        		$('.paso1pregunta').show();
        	}
        	
        },
        error: function (xhr, textStatus, thrownError) {
            showError('error', 'Error validando tú documento.');
        }
    });
}

function checkQuestions() {
	var button = $('#check-question');
	
	//Llamamos el servicio para validar cedula
	$.ajax({
        url: checkQuestionsRoute,
        type: 'GET',
        cache: false,
        datatype: 'json',
        data: {
        	questionName: $("input[name='questionName']:checked").val(),
        	questionDocument: $("input[name='questionDocument']:checked").val(),
        	questionDocumentDate: $("input[name='questionDocumentDate']:checked").val()
        },
        beforeSend: function () {
        	//Inhabilitamos el botón
        	$(button).attr('disabled', 'disabled');
        	//Mostramos anicación cargando
        	$(button).children().show();
        },
        complete: function () {
        	//Habilitamos el botón
        	$(button).removeAttr('disabled');
        	//Ocultamos animación cargando
			$(button).children().hide();
        },
        success: function (data) {
        	if(!data.error) {
        		if(data.valid) {
        			$('.paso1pregunta').hide();  
           		 	$('.paso2pregunta').show();
        		} else {
        			$('#errorQuestion').empty();
        			$('#errorQuestion').append(data.messages);
        			if(intents == 2) {
        				location.reload(); 
        			}
        			
        			intents ++;
        		}
        	}
        	
        	if(data.error) {
        		$('#errorQuestion').empty();
    			$('#errorQuestion').append(data.messages);
        	}
        },
        error: function (xhr, textStatus, thrownError) {
            showError('error', 'Error validando tú documento.');
        }
    });
}

jQuery(document).ready(function () {
    Login.init();
});