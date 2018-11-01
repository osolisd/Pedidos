<?php
/**
 * @description Archivo de rutas de la aplicación
 * @author      Andres.Castellanos <andres.castellanos@softwareestartegico.com>
 * @date        Septiembre 13 de 2017
 * -------------------------------------------------------------------------------------------------------
 * AUDITORIA DE CAMBIOS
 * -------------------------------------------------------------------------------------------------------
 * Fecha Cambio     | Nombre Autor                      | Detalles Cambio
 * -------------------------------------------------------------------------------------------------------
 * 13/09/2017       | Andres.Castellanos                | Se agrega la ruta findGeneraldata
 * -------------------------------------------------------------------------------------------------------
 */

Route::group(['middleware' => 'web'], function () {
    /**********************************************************************************************/
    // RUTAS DE AUTENTICACIÓN
    /**********************************************************************************************/
    Route::get('/', 'Auth\LoginController@showLoginForm'); // Cargar Formulario Autenticación
    Route::post('Auth', 'Auth\LoginController@authenticate')->name('AuthUser'); // Autenticar Usuario
    Route::get('Logout', 'Auth\LoginController@logout')->name('Logout'); // Finalizar Sesión
    Route::get('CambioMarca/{brand}', 'Auth\LoginController@changeBrand')->name('CambioMarca'); // Cambio de Marca
    /**********************************************************************************************/
    // RUTAS PARA DATOS GENERALES
    /**********************************************************************************************/
    Route::get('Inicio', 'Pedidos\MainController@index')->name('Inicio'); //Datos Generales  
    Route::get('Puntos', 'Pedidos\MainController@findHistoryPoints')->name('Puntos'); //Historico Puntos
    Route::get('Directora', 'Pedidos\MainController@findZoneDitector')->name('Directora'); //Directora Zona
    Route::get('Fechas', 'Pedidos\MainController@findKeyDates')->name('Fechas'); //Fechas Claves
    Route::get('Notificaciones', 'Pedidos\MainController@showNotifications')->name('Notificaciones'); //Fechas Claves
    Route::get('Montos', 'Pedidos\MainController@findAmmount')->name('Montos'); //Fechas Claves
    /**********************************************************************************************/
    // RUTAS PARA ZONAS
    /**********************************************************************************************/
    Route::get('Zonas', 'Pedidos\ZoneController@findAll')->name('Zonas'); // Lista de Zonas
    Route::post('EditarZona', 'Pedidos\ZoneController@update')->name('EditarZona'); // Editar Zonas
    
    Route::get('FindAllZones/{ajax}', 'Pedidos\AlertController@findAllZones')->name('FindAllZones');   
    /**********************************************************************************************/
    // RUTAS PARA AYUDA
    /**********************************************************************************************/
    Route::get('Ayuda', 'Pedidos\MainController@showVideo')->name('Ayuda'); // Video de Ayuda
    /**********************************************************************************************/
    // RUTAS PARA PEDIDOS
    /**********************************************************************************************/
    Route::get('Pedidos', 'Pedidos\OrderController@findAll')->name('Pedidos'); // Lista de Pedidos sin Parametro
    Route::get('Pedidos/{quantity}', 'Pedidos\OrderController@findAll')->name('FindOrders'); // Lista de Pedidos con Parametro
    Route::get('NuevoPedido', 'Pedidos\OrderController@create')->name('NuevoPedido'); // Nuevo Pedido
    Route::get('checkProduct/{plu}/{quantity}', 'Pedidos\OrderController@checkProduct')->name('CheckProduct'); // Verificar PLU
    Route::post('saveOrder', 'Pedidos\OrderController@save')->name('SaveOrder'); //Ruta para guardar pedido temporal
    Route::get('NuevoPedidoAsesora', 'Pedidos\OrderController@createByAdministrator')->name('NuevoPedidoAsesora'); //Ruta para crear pedido a una asesora por administrador / Directora / Emtelco
    /**********************************************************************************************/
    // RUTAS PARA AUTO COMPLETADO
    /**********************************************************************************************/
    Route::get('Productos', 'Pedidos\ProductController@findAll')->name('Productos'); //Ruta para obtener la lista de PLUS para crear pedido
    Route::get('FiltroProductos/{filter}', 'Pedidos\ProductController@findByFilter')->name('FiltroProductos'); //Ruta para filtar los PLUS
    Route::get('EliminarProductos', 'Pedidos\ProductController@deleteAll')->name('EliminarProductos');  //Ruta para eliminar todos los PLUS 
    /**********************************************************************************************/
    // RUTAS PARA IMAGENES DEL HOME
    /**********************************************************************************************/
    Route::get('ImagenesInicio', 'Pedidos\GuestController@findImagesUrl')->name('ImagenesInicio'); //Ruta para obtener las imagenes del carrusel vista de autenticación
    /**********************************************************************************************/
    // RUTAS PARA LA GESTIÓN DE DATOS PERSONALES
    /**********************************************************************************************/    
    Route::get('Perfil', 'Pedidos\MainController@profile')->name('Perfil'); //Ruta para cargar el formulario de prueba carga foto perfil
    Route::post('FotoPerfil', 'Pedidos\MainController@uploadProfile')->name('FotoPerfil'); //Ruta para cargar la foto de perfil
    /**********************************************************************************************/
    // RUTAS PARA LA GESTIÓN DE PAQUETE DEL AHORRO
    /**********************************************************************************************/
    Route::get('Paquetes', 'Pedidos\PackageController@findAll')->name('Paquetes'); //Ruta para obtener el listado de las estrategias disponibles
    Route::get('CampanaPaquete', 'Pedidos\PackageController@findAllCampaingById')->name('CampanaPaquete'); //Ruta para obtener las camapñas de entrega
    Route::get('GuardarSuscripcion', 'Pedidos\PackageController@save')->name('GuardarSuscripcion'); //Ruta para guardar la suscripcion
    Route::get('EditarSuscripcion', 'Pedidos\PackageController@update')->name('EditarSuscripcion'); //Ruta para editar la suscripción
    Route::get('EliminarSuscripcion', 'Pedidos\PackageController@remove')->name('EliminarSuscripcion'); //Ruta para eliminar la suscripción
    Route::get('SuscripcionesPaquetes', 'Pedidos\PackageController@findAllSuscriptions')->name('SuscripcionesPaquetes'); //Ruta para eliminar la suscripción
    Route::get('PaquetesEliminados', 'Pedidos\PackageController@findAllRemove')->name('PaquetesEliminados'); //Ruta para eliminar la suscripción
    /**********************************************************************************************/
    // RUTAS PARA LA GESTIÓN DE ALERTAS
    /**********************************************************************************************/
    Route::get('Alertas', 'Pedidos\AlertController@findAll')->name('Alertas'); //Ruta para obtener la lista de alertas
    Route::get('AlertasAsesora', 'Pedidos\AlertController@findAllByAdviser')->name('AlertasAsesora'); //Ruta para obrtener la lista de alertas por asesora
    Route::get('CrearAlerta', 'Pedidos\AlertController@create')->name('CrearAlerta'); //Ruta para crear alerta
    Route::get('DetalleAlerta/{id}', 'Pedidos\AlertController@findById')->name('DetalleAlerta'); //Ruta para obtener alerta por id
    Route::post('GuardarAlerta', 'Pedidos\AlertController@save')->name('GuardarAlerta'); //Ruta para guardar la alerta
    Route::post('EditarAlerta', 'Pedidos\AlertController@update')->name('EditarAlerta'); //Ruta para editar la alerta
    Route::get('EliminarAlerta/{id}', 'Pedidos\AlertController@remove')->name('EliminarAlerta'); //Ruta para eliminar la alerta
    Route::get('AlertaPrincipal', 'Pedidos\AlertController@findMainAlert')->name('AlertaPrincipal'); //Ruta para obtener la alertas principales
    Route::get('AlertaSecundaria', 'Pedidos\AlertController@findAllAlertSecondary')->name('AlertaSecundaria'); //Ruta para obtener las alertas secundarias / campana
    /**********************************************************************************************/
    // RUTAS PARA LA GESTIÓN DE USUARIOS
    /**********************************************************************************************/
    Route::get('Usuarios/{mailPlan}', 'Pedidos\UserController@findByProfile');//Ruta para cargar lista de usuarios
    Route::post('Usuarios', 'Pedidos\UserController@findByProfile');//Ruta para cargar lista de usuarios
    Route::get('Usuarios', 'Pedidos\UserController@findByProfile')->name('Usuarios');//Ruta para cargar lista de usuarios
//     Route::get('Usuarios/{ajax}', 'Pedidos\UserController@findByProfile')->name('Usuarios');//Ruta para cargar lista de usuarios
    Route::get('DetalleUsuario/{document}', 'Pedidos\UserController@detail')->name('DetalleUsuario'); //Ruta para obtener el detalle del usuario
    Route::get('BuscarAsesora', 'Pedidos\UserController@findByDocument')->name('BuscarAsesora'); //Ruta para obtener la asesora a crear el pedido
    /**********************************************************************************************/
    // RUTAS PARA LA GESTIÓN DE SEGURIDAD CAMBIO CONTRASEÑA
    /**********************************************************************************************/
    Route::post('EditarClaveAdministrador', 'Auth\ResetPasswordController@resetByAdministrator')->name('EditarClaveAdministrador'); //Ruta para editar clave asesora por el administrador
    Route::get('CambiarClave', 'Auth\ResetPasswordController@reset')->name('CambiarClave'); //Ruta para cargar vista de cambiar clave asesora
    Route::post('EditarClaveAsesora', 'Auth\ResetPasswordController@resetByAdviser')->name('EditarClaveAsesora'); //Ruta para cambiar clave por asesora
    /**********************************************************************************************/
    // RUTAS PARA LA GESTIÓN DE SEGURIDAD RECUPERAR CLAVE
    /**********************************************************************************************/
    Route::get('ValidarDocumentoAsesora', 'Auth\ForgotPasswordController@checkAdviserDocument')->name('ValidarDocumentoAsesora'); //Ruta para validar la cedula de la asesora
    Route::get('PreguntaAsesora', 'Auth\ForgotPasswordController@findQuestion')->name('PreguntaAsesora'); //Ruta para obtener las preguntas para cambiar clave
    Route::get('ValidarPreguntaAsesora', 'Auth\ForgotPasswordController@checkQuestion')->name('ValidarPreguntaAsesora'); //Ruta para validar las respuestas para cambiar clave
    Route::get('EnviarClaveAsesora', 'Auth\ForgotPasswordController@resetByEmail')->name('EnviarClaveAsesora'); //Ruta para reiniciar la clave y enviar email
    Route::get('RecuperarClaveAsesora', 'Auth\ForgotPasswordController@resetByChange')->name('RecuperarClaveAsesora'); //Ruta para recuperar clave preguntas asesora
    /**********************************************************************************************/
    // RUTAS PARA LA GESTIÓN DE HISTORICO PEDIDOS DZ Y ADMINISTRADOR
    /**********************************************************************************************/
    Route::get('PedidosCurso/{history}', 'Pedidos\OrderController@findAllByAdministrator')->name('PedidosCurso');
    Route::post('PedidosCurso/{history}', 'Pedidos\OrderController@findAllByAdministrator')->name('PedidosCurso');
    Route::get('HistoricoPedidos/{history}', 'Pedidos\OrderController@findAllByAdministrator')->name('HistoricoPedidos');
    Route::post('HistoricoPedidos/{history}', 'Pedidos\OrderController@findAllByAdministrator')->name('HistoricoPedidos');
    /**********************************************************************************************/
    // RUTAS PARA EXPORTAR PEDIDOS
    /**********************************************************************************************/
    Route::get('ExportHistoryOrders', 'Pedidos\ExportController@exportHistoryOrders')->name('ExportHistoryOrders');
    Route::get('ExportCurrentOrders/{zone}', 'Pedidos\ExportController@exportCurrentOrders')->name('ExportCurrentOrders');
    Route::get('ExportCurrentOrders', 'Pedidos\ExportController@exportCurrentOrders')->name('ExportCurrentOrders');
}); 
  
// Route::group(['middleware' => 'guest'], function () {
Route::get('Logger', 'Pedidos\GuestController@showLogger');        
// });

/**
 * @description Ruta para obtener el detalle del pedido
 * @param       int $id
 * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
 * @date        Septiembre 19 de 2017
 */
Route::get('findOrderById/{id}/{status}/{document}', 'Pedidos\OrderController@findById')->name('DetallePedido');
Route::get('findOrderById/{id}/{status}', 'Pedidos\OrderController@findById')->name('OrderDetail');
Route::get('findOrderById/{id}', 'Pedidos\OrderController@findById')->name('OrderDetail');
/**
 * @description Ruta para eliminar el pedido 
 * @param       int $id
 * @author      Andres.Castellanos <andres.castellanos@software estrategico.com>
 * @date        Septiembre 19 de 2017
 */
Route::get('deleteOrder/{id}/{isDashboard}', 'Pedidos\OrderController@delete')->name('DeleteOrder');
Route::get('deleteOrder/{id}', 'Pedidos\OrderController@delete')->name('DeleteOrder');
/**
 * @description Ruta para confirmar el pedido
 * @param       int $id
 * @param       double $totalOrder
 * author       Andres.Castellanos <andres.castellanos@softwareestrategico.com>
 * @date        Octubre 11 de 2017
 */
Route::post('confirmOrder', 'Pedidos\OrderController@confirm')->name('ConfirmOrder');
