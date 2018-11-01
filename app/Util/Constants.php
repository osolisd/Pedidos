<?php

/**
 * Clase que contiene las constantes de la aplicación
 */

namespace App\Util;

class Constants {

    //Nombre de verbos HTTP
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    //Nombre estado OK Llamado servicio
    const HTTP_OK = 200;
    const HTTP_UNAUTHORIZED = 401;
    //Nombre mensajes sesión flash
    const ERROR_MESSAGES = "ErrorMessages";
    const SUCCESS_MESSAGES = "SuccessMessages";
    const WARNING_MESSAGES = "WarningMessages";
    const INFO_MESSAGES = "InfoMessages";  
    const WARNING_MESSAGES_TOASTR = "ToastrWarningMessages";
    //Nombre sesión
    const SESSION_NAME = "SessionObject";
    const DATE_FORMAT_Y_M_D = 'Y/m/d';
    const DATE_FORMAT_D_M_Y = 'd/m/Y';
    //Estados de usuarios
    const USER_ACTIVATE = 1;
    const USER_LOCKED = 0;
    //Perfiles de usuario
    const PROFILE_ADMINISTRATOR = 'Administrador(a)';
    const PROFILE_DIRECTOR = 'Director(a)'; 
    const PROFILE_EMTELCO = 'Emtelco'; 
    const PROFILE_ADVISER = 'Asesor(a)';
    const PROFILE_EMPLOYEE = 'Empleado(a)';
    //Constantes Booleaneas
    const TRUE = true;
    const FALSE = false;
    //Configuración S3 Bucket Amazon
    const S3_CONFIG = [
        'key' => 'AKIAJT7OPSMVCPUFNDSQ',
        'secret' => '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'  
    ];
    //Header para escapar los acentos en formato JSON
    const JSON_HEADER = [
        'Content-Type' => 'application/json; charset=UTF-8',
        'charset' => 'utf-8'
    ];
    //Constamnte para asignar el limite de memoria
    const MEMORY_LIMIT = '1G';
    //Path cargar imagen de perfil
    const PROFILE_PATH = '/upload/profile/';
    //Path cargar imagen de alerta
    const ALERT_PATH = '/upload/alert/';  
    //Constante de la ruta del S3 Amazon donde se carga la foto de perfil
    const S3_PROFILE_PATH = 'static-pedidos-web/upload/profile-images/';
    //Constante de la ruta del S3 Amazon donde se carga la imagen de la alerta
    const S3_ALERT_PATH = 'static-pedidos-web/upload/alerts/';
    //Constante del formato de la foto de perfil
    const JPG_FORMAT = '.jpg';
    //Constante del nivel de dominio  
    const DOMAIN_LEVEL = '.com.co';
    //Constante para la foto de perfil por defecto
    const PROFILE_DEFAULT = 'public/metronic/pages/media/profile/photo3.jpg';
    //Constantes de los buckes privados y publicos de las marcas en S3 Amazon
    const S3_PRIVATE_BUCKET = 'files.carmel.com.co';
    const S3_PRIVATE_BUCKET_PCFK = 'files.pcfk.com.co';
    const S3_PRIVATE_BUCKET_LOGUIN = 'files.loguin.com.co';
    const S3_PUBLIC_BUCKET = 'http://files.carmel.com.co/';
    const S3_PUBLIC_BUCKET_PCFK = 'http://files.pcfk.com.co/';
    const S3_PUBLIC_BUCKET_LOGUIN = 'http://files.loguin.com.co/';  
    const S3_PUBLIC_READ = 'public-read';    
    const BRAND_PCFK = 'pcfk';  
    const BRAND_PCFK_COMPLETE = 'pacifika';
    //Constantes de los prefijos de las alertas principales
    const ALERT_NEW_ORDER = 'NP';
    const ALERT_DASHBOARD = 'A';
    //Constantes de los nombres de las cookies
    const NEW_ORDER_ALERT_COOKIE = '_Ck_LDOrderAlertNP';
    const DASHBOARD_ALERT_COOKIE = '_Ck_LDOrderAlertDS';
    
}   


