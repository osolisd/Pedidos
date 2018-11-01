<?php
/**
 * Clase la cual contiene las url de los servicios
 */
namespace App\Util;

class WebServiceParameters {
    const END_POINT_SERVER = 'http://10.244.9.70:9199'; //End point (Servidor) de los servicios  
    
    /****************************************************************************************************************/
    // SERVICIOS AUTENTICACIÓN OSIRIS 3.0
    /****************************************************************************************************************/
    const WS_USER_AUTHENTICATION = 'Api/Usuarios/UserAuthentication'; //Servicio para autenticar el usuario desde Osiris 3.0 
    const WS_USER_FUNCTIONS = 'odata/Asegurables/fn.GetAsegurablesByRolId'; //Servicio para obtener los asegurables del usuario desde Osiris 3.0
    const WS_USER_ROL = 'api/Rols'; //Servicio para obtener el rol del usuario desde Osiris 3.0
    const WS_USER_BRANDS = 'api/persons/GetAllMarcas'; //Servicio para obtener las marcas de la asesora
    /****************************************************************************************************************/
    // SERVICIOS ZONAS 
    /****************************************************************************************************************/
    const WS_ZONES = 'api/Zones'; //Servicio de zonas
    const WS_MAIL_PLAN = 'api/Zones/MailPlan'; //Servicio para obtener el mail plan
    const WS_CAMPAING_YEAR = 'api/orders/GetBilledCampaignsByYear';
    const WS_DIVISION = 'api/orders/GetConsultDivisiones';
    /****************************************************************************************************************/
    // SERVICIOS DE CLIENTES 
    /****************************************************************************************************************/
    const WS_USERS = 'odata/GetUserData'; //Servicio para obtener los usuario / asesoras  
    /****************************************************************************************************************/
    // SERVICIOS DE PRODUCTOS
    /****************************************************************************************************************/
    const WS_PRODUCTS = 'api/PLUS'; //Servicio para obtener la lista de productos / plus de la campañade la asesora
    /****************************************************************************************************************/
    // SERVICIOS DE PAQUETE DEL AHORRO
    /****************************************************************************************************************/
    const WS_PACKAGES = 'api/SavingPackage/ConsultSavingPackageStrategy'; //Servicio para las estrategias disponibles 
    const WS_PACKAGE_CAMPAINGS = 'api/SavingPackage/ConsultDeliveryCampains'; //Servicio para obtener la lista de campañas para un paquete del ahorro
    const WS_PACKAGE_SUSCRIPTION_ADMINISTRATOR = 'api/SavingPackage/SuscriptionsAdmin'; //Servicio para obtener las suscripciones a paquetes del ahorro
    /****************************************************************************************************************/
    // SERVICIOS DE PEDIDOS
    /****************************************************************************************************************/
    const WS_FIND_ALL_ORDERS = 'api/Orders/GetAllOrder'; //Servicio para obtener el listado de los pedidos
    const WS_FIND_ORDER = 'api/Orders/GetOrderbyCampana'; //Servicio para obtener pedido por campaña, asesora y marca
    const WS_ORDER_DETAIL = 'api/Orders/GetOrderbyID'; //Servicio para obtener el detalle del pedido 
    const WS_PLU_VALIDATE = 'api/Plus'; //Servicio para validar el PLU
    const WS_DETAIL_BILL = 'api/Orders/GetBilledOrderbyId'; //Servicio para obtener le detalle de la factura
    const WS_ORDER = 'api/Orders'; //Servicio para crear (POST) / editar (PUT) pedido
    const WS_ORDER_CONFIRM = 'api/Orders/ConfirmOrder'; //Servicio para confirmar el pedido
    const WS_ORDER_DELETE = 'api/Orders/Delete'; //Servicio para eliminar el pedido
    /****************************************************************************************************************/
    // SERVICIOS DE MONTOS
    /****************************************************************************************************************/
    const WS_AMMOUNT = 'api/amounts'; //Servicio para obtener los mono minimo y maximo como la tolerancia a los mismos de la asesora
    /****************************************************************************************************************/
    // SERVICIOS DE PUNTOS
    /****************************************************************************************************************/
    const WS_POINTS = 'api/Points'; //Servicio para consultar los puntos de la asesora
    /****************************************************************************************************************/
    // SERVICIOS DE DIRECTORA DE ZONA
    /****************************************************************************************************************/
    const WS_ZONE_DIRECTOR = 'api/ZoneDirectors'; //Servicio para obtener los datos de la directora de zona de la asesora
    /****************************************************************************************************************/
    // SERVICIOS DE FECHAS CLAVES
    /****************************************************************************************************************/
    const WS_KEY_DATES = 'api/dates'; //Servicio para obtener las fechas claves de la zona de la asesora
    /****************************************************************************************************************/
    // SERVICIOS DE PERSONAS
    /****************************************************************************************************************/
    const WS_PERSON = 'api/persons/GetAllPersons'; //Servicio para obtener los datos generales de la asesora
    /****************************************************************************************************************/
    // SERVICIOS DE CATALOGOS
    /****************************************************************************************************************/
    const WS_CATALOG = 'api/Catalogs'; //Servicio para obtener el catalogo de la zona y campaña
    /****************************************************************************************************************/
    // SERVICIOS DE CUPOS
    /****************************************************************************************************************/
    const WS_QUOTA_CREDIT = 'api/QuotaSales'; //Servicio para obtener el cupo de la asesora  
    /****************************************************************************************************************/
    // SERVICIOS DE IMAGENES HOME
    /****************************************************************************************************************/
    const WS_IMAGES_HOME = 'api/GeneralParameters/GetImagesByMark'; //Servicio para obtener las imagenes a mostrar en el carrusel de autenticación
    /****************************************************************************************************************/
    // SERVICIOS DE ALERTAS
    /****************************************************************************************************************/
    const WS_ALERTS = 'api/alerts'; //Servicio para gestionar las alertas listar / crear / editar / eliminar
    const WS_STENCIL_STATUS = 'api/alerts/StateStencil'; //Servicio para obtener la lista de estados estencil
    const WS_VALUE_CLASIFICATION = 'api/alerts/ClasificationsValue'; //Servicio para obtener la lista de clasificación por valor
    /****************************************************************************************************************/
    // SERVICIOS ODATA DE HISTORICOS PEDIDOS ASESORA / ADMINISTRADOR
    /****************************************************************************************************************/
    const WS_ORDER_HISTORY_DIRECTOR = 'odata/GetOrderbyDirZona'; //Servicio historico pedidos en curso directora de zona
    const WS_ORDER_HISTORY_ADMINISTRATOR = 'odata/GetOrderToAdmin'; //Servicio historico pedidos y en curso administrador  
    /****************************************************************************************************************/
    // SERVICIOS DE CAMBIAR CONTRASEÑA
    /****************************************************************************************************************/
    const WS_UPDATE_PASSWORD_ADMINISTRATOR = 'api/Configurations/UpdatePassword'; //Servicio para actualizar la contraseña de la asesora rol administrador
}

