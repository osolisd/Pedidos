<?php

/**
 * Clase que contiene las utilidades del sistema
 */

namespace App\Util;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class Helpers {

    /**
     * @description Metodo para obtener la marca
     * @return      string
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Octubre 30 de 2017
     */
    public static function checkBrand($host) {
        return 'carmel';
        return explode('.', $host)[1];      
    }
    
    /**
     * @description Metodo para obtener los mensajes del archivo messages.properties 
     * @param       string $key
     * @return      string
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Septiembre 14 de 2017
     */
    public static function getMessage($key) {
        //obtiene el archivo de propiedades
        $file = parse_ini_file('messages.properties', false);
        //retorna el mensaje del archivo de propiedades
        return utf8_encode($file[$key]);
    }

    /**
     * @description Metodo para verificar si dos valores son iguales
     * @param       string $value
     * @param       string $equal
     * @return      boolean
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Noviembre 03 de 2017
     */
    public static function isEquals($value, $equal) {
        //Verificamos que los dos valores sean iguales
        if ($value == $equal) {
            return true;
        }

        return false;
    }

    /**
     * @description Metodo para convertir un array a objeto
     * @param       array $array
     * @return      object
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Octubre 23 de 2017
     */
    public static function arrayToObject($array) {
        //Casteamos el array en un objeto
        return (object) $array;
    }

    /**
     * @description Metodo para agregar datos a la sesión
     * @param       array $object
     * @return      void
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Septiembre 22 de 2017
     */
    public static function sessionCreate($object) {
        //Eliminamos la sesión
        Session::forget(Constants::SESSION_NAME);
        //Agregamos a la sesion el objeto
        Session::push(Constants::SESSION_NAME, $object);
    }

    /**
     * @description Metodo para obtener los datos de la sesión ya sean todos o uno en especifico
     * @param       Request $request
     * @param       string $key
     * @return      object
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Septiembre 22 de 2017
     */
    public static function getSessionObject($key = null) {
        //Si no viene el nombre de un objecto en especifico retorna todos los datos de la sesión
        $object = Session::get(Constants::SESSION_NAME);

        //Verifica si el nombre del objeto no esta vacio
        if (!empty($key) && isset($key)) {
            //Retornamos el objeto po el nombre enviado por parametro
            return (isset($object[0][$key])) ? $object[0][$key] : null;
        }

        return $object[0];
    }

    /**
     * @description Metodo para identificar si el usuario es un administrador
     * @return      boolean
     * @author      Andres.Castellanos <andres.castellanos@softwareestartegico.com>
     * @date        Noviembre 02 de 2017
     */
    public static function isAdministrator() {
        //Verificamos si el perfil autenticado es administrador
        if (self::isEquals(strtolower(auth()->user()->profileName), strtolower(Constants::PROFILE_ADMINISTRATOR))) {
            return true;
        }

        return false;
    }

    /**
     * @description Metodo para identificar si el usuario es un asesor
     * @return      boolean
     * @author      Andres.Castellanos <andres.castellanos@softwareestartegico.com>
     * @date        Noviembre 02 de 2017
     */
    public static function isAdviser() {
        //Verificamos si el perfil autenticado es administrador
        if (self::isEquals(strtolower(auth()->user()->profileName), strtolower(Constants::PROFILE_ADVISER))) {
            return true;
        }

        return false;
    }

    /**
     * @description Metodo para identificar si el usuario es un empleado
     * @return      boolean
     * @author      Andres.Castellanos <andres.castellanos@softwareestartegico.com>
     * @date        Noviembre 02 de 2017
     */
    public static function isEmployee() {
        //Verificamos si el perfil autenticado es administrador
        if (self::isEquals(strtolower(auth()->user()->profileName), strtolower(Constants::PROFILE_EMPLOYEE))) {
            return true;
        }

        return false;
    }

    /**
     * @description Metodo para identificar si el usuario es un Emtelco
     * @return      boolean
     * @author      Andres.Castellanos <andres.castellanos@softwareestartegico.com>
     * @date        Noviembre 02 de 2017
     */
    public static function isEmtelco() {
        //Verificamos si el perfil autenticado es administrador
        if (self::isEquals(strtolower(auth()->user()->profileName), strtolower(Constants::PROFILE_EMTELCO))) {
            return true;
        }

        return false;
    }

    /**
     * @description Metodo para identificar si el usuario es una directora
     * @return      boolean
     * @author      Andres.Castellanos <andres.castellanos@softwareestartegico.com>
     * @date        Noviembre 14 de 2017
     */
    public static function isDirector() {
        //Verificamos si el perfil autenticado es director
        if (self::isEquals(strtolower(auth()->user()->profileName), strtolower(Constants::PROFILE_DIRECTOR))) {
            return true;
        }

        return false;
    }

    public static function isUnlockAdviser($object) {
        //Verificamos que la asesora no este bloqueada por stencil
        if ($object->Bloqueado == 'True' || $object->BloqueoStencil == 'True' && $object->Bloqueado == 'True') {
            return true;
        }

        return false;
    }

    /**
     * @description Metodo para obtener la url de la foto de perfil 
     * @return      string
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 12 de 2017
     */
    public static function findProfilePhoto() {
        //Seteamos la foto de perfil por defecto
        $profile = 'http://files.carmel.com.co/static-pedidos-web/upload/profile-images/default.jpg';
        //Incializamos la configuración del S3 Amazon
        $s3 = S3Client::factory(Constants::S3_CONFIG);
        try {
            //Verificamos si existe la foto de perfil cargada en S3 de Amazon
            $existObject = $s3->doesObjectExist(
                    Constants::S3_PRIVATE_BUCKET, Constants::S3_PROFILE_PATH . md5(auth()->user()->document) . Constants::JPG_FORMAT
            );

            //Verificamos el resultado
            if (Helpers::isEquals($existObject, Constants::TRUE)) {
                //Seteamos la imagen de perfil cargada en S3 de Amazon
                $profile = Constants::S3_PUBLIC_BUCKET . Constants::S3_PROFILE_PATH . md5(auth()->user()->document) . Constants::JPG_FORMAT;
            }
        } catch (S3Exception $ex) {
            Logger::errorMySQL($ex->getMessage());
        }

        //Retornamos la url de la foto de perfil
        return $profile;
    }

    /**
     * @description Metodo para contar los elementos de una lista
     * @param       array $list 
     * @return      number
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Septiembre 15 de 2017
     */
    public static function countList($list) {
        //Contamos los objetos de una lista y le restamo un elemento para los ciclos
        return count($list) - 1;
    }

    /**
     * @description Metodo para dar formato tipo moneda a un número
     * @param       int $number
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Septiembre 19 de 2017
     */
    public static function numberFormat($number) {
        //Damos formato al número
        return number_format($number, 0, ',', '.');
    }

    /**
     * @description Metodo para eliminar formato tipo moneda a un número
     * @param       int $number
     * @return      int $number
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Octubre 23 de 2017
     */
    public static function removeNumberFormat($number) {
        return str_replace(".", "", $number);
    }

    /**
     * @description Metodo para pasar un número negativo a positivo
     * @param       string $number
     * @return      string
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Septiembre 27 de 2017
     */
    public static function negativeToPositive($number) {
        //Eliminamos el signo negativo
        return str_replace('-', '', $number);
    }

    /**
     * @description Metodo para dar fomato a una fecha
     * @param       date $date
     * @return      date
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Septiembre 27 de 2017
     */
    public static function dateFormat($date, $format = Constants::DATE_FORMAT_Y_M_D) {
        //Verificamos si la fecha esta vacia
        if (empty($date) || $date == '') {
            //Retornamos la fecha enviada
            return $date;
        }

        //Creamos una nueva fecha
        $date = new \DateTime($date);
        //Datos formato a la fecha
        return $date->format($format);
    }

    /**
     * @description Metodo para validar monto minimo
     * @param       number $value
     * @return      number
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Octubre 04 de 2017
     */
    public static function checkMinAmmount($value = 0) {
        $object = Session::get('monto');
        //Validamos que el valor enviado sea menor al monto minimo
        if($value < (trim($object->MontoMin) - trim($object->ToleranciaMontoMin))) {
            return true;
        }

        return false;
    }

    /**
     * @description Metodo para validar em monto maximo
     * @param       number $value
     * @return      number
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Octubre 04 de 2017
     */
    public static function checkMaxAmmount($value = 0) {     
        //Capturamos el objeto de la sesión
        $object = Session::get('monto');
        //Validamos que el valor sea mayor al valor del monto maximo
        if ($value > (trim($object->MontoMax) + trim($object->ToleranciaCupoMax))) {
            return true;  
        }

        return false;
    }

    /**
     * @description Metodo para obtener los montos
     * @return      string
     * @author      Felipe.Echeverri <felipe.echeverri@ingeneo.com.co>
     * @date        Septiembre 06 de 2017
     */
    public static function getAmmount() {
        //Capturamos el objeto de la sesión
        return Session::get('monto');
    }

    /**
     * @description Metodo para calcular los puntos
     * @param       number $price
     * @param       number $quantity
     * @return      number
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Octubre 04 de 2017
     */
    public static function calculatePoints($price = 0, $quantity = 0) {
        return round(($quantity * $price / 0.75) / 1000);
    }

    /**
     * @description Metodo para obtener el catálogo
     * @param       Request $request
     * @return      string
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Septiembre 27 de 2017
     */
    public static function getCatalog() {
        //Capturamos el objeto de la sesión
        $object = Session::get(Constants::SESSION_NAME);
        //Retornamos el resultado
        return $object[0]['datosMarca']['catalogo'];
    }

    /**
     * @description metodo para obtener la campaña actual de la sesión
     * @return      string
     * @author      Andres.Castellanos <andres.castellanos@softwareestartegico.com>
     * @date        Septiembre 27 de 2017
     */
    public static function getCurrentCampaign() {
        //Capturamos el objeto de la sesión
        $object = Session::get(Constants::SESSION_NAME);
        //Retornamos el resultado
        return $object[0]['datosMarca']['campana'];
    }

    /**
     * @description Metodo para agregar los mensajes en sesión
     * @param       string $name
     * @param       array $data
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Septiembre 25 de 2017
     */
    public static function setSessionFlash($name, $data) {
        //Agregamos el mensaje a la sesión
        session()->flash($name, $data);
    }

    /**
     * @description Metodo para obtener el documento de la sesion
     * @return      string
     * @author      Andres.Castellanos <andres.castellanos@softwareestartegico.com>
     * @date        Septiembre 27 de 2017
     */
    public static function getZone() {
        //Capturamos el objeto de la sesión
        $object = Session::get(Constants::SESSION_NAME);
        //Retornamos el resultado
        return $object[0]['datosMarca']['zona'];
    }

    /**
     * @description Metodo para obtener los datos de la asesora
     * @return      object
     * @author      Andres.Castellanos <andres.castellanos@softwareestartegico.com>
     * @date        Septiembre 27 de 2017
     */
    public static function getAdviser() {
        //Capturamos el objeto de la sesión
        $object = Session::get(Constants::SESSION_NAME);
        //Retornamos el resultado
        return $object[0]['asesora'];
    }

    /**
     * @description Metodo para verificar las variables
     * @param string $var
     * @return boolean
     * @author      Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @date        Septiembre 28 de 2017
     */
    public static function validEmptyVar($var) {
        return (isset($var) && !is_null($var) && !empty($var));
    }

    /**
     * @description Metodo para separar las fechas en anio mes y dia
     * @param string $date
     * @param string $delimiter
     * @return array
     * @author      Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @date        Septiembre 28 de 2017
     */
    public static function explodeDate($date, $delimiter = '/') {
        return (Helpers::validEmptyVar($date)) ?
                explode($delimiter, $date) :
                null;
    }

    /**
     * @description Metodo para separar las fechas en anio mes y dia
     * @param array $dates
     * @return array
     * @author      Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @date        Septiembre 28 de 2017
     */
    public static function importantDates($dates) {
        $response = [
            'FechasConferencias' => [],
            'FechasCambios' => [],
            'FechaPagoPedido' => null,
            'FerchaLimitePedido' => null,
            'FechaEntregaPedido' => null
        ];

        if (!empty($dates['FechasConferencias'])) {
            foreach ($dates['FechasConferencias'] as $fechaCo) {
                if (!is_null($expConf = Helpers::explodeDate($fechaCo['FechaConferencia']))) {
                    $response['FechasConferencias'][] = [
                        'FechaConferencia' => [
                            $expConf[0],
                            substr(Helpers::nameMonths($expConf[1]), 0, 3),
                            $expConf[2],
                        ],
                        'LugarConferencia' => $fechaCo['LugarConferencia']
                    ];
                }
            }
        }

        if (!empty($dates['FechasCambios'])) {
            foreach ($dates['FechasCambios'] as $fechaCa) {
                if (!is_null($expCamb = Helpers::explodeDate($fechaCa['FechaCambiosDevoluciones']))) {
                    $response['FechasCambios'][] = [
                        'FechaCambiosDevoluciones' => [
                            $expCamb[0],
                            substr(Helpers::nameMonths($expCamb[1]), 0, 3),
                            $expCamb[2],
                        ],
                        'LugarCambios' => $fechaCa['LugarCambios']
                    ];
                }
            }
        }

        if (!is_null($expPago = Helpers::explodeDate($dates['FechaPagoPedido']))) {
            $response['FechaPagoPedido'] = $expPago;
            $response['FechaPagoPedido'][1] = substr(Helpers::nameMonths($response['FechaPagoPedido'][1]), 0, 3);
        }

        if (!is_null($expLim = Helpers::explodeDate($dates['FerchaLimitePedido']))) {
            $response['FerchaLimitePedido'] = $expLim;
            $response['FerchaLimitePedido'][1] = substr(Helpers::nameMonths($response['FerchaLimitePedido'][1]), 0, 3);
        }

        if (!is_null($expEnt = Helpers::explodeDate($dates['FechaEntregaPedido']))) {
            $response['FechaEntregaPedido'] = $expEnt;
            $response['FechaEntregaPedido'][1] = substr(Helpers::nameMonths($response['FechaEntregaPedido'][1]), 0, 3);
        }

        return $response;
    }

    /**
     * @description Metodo para separar las fechas en anio mes y dia 
     * @param       string $month
     * @return      string
     * @author      Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @date        Septiembre 28 de 2017
     */
    public static function nameMonths($month) {
        $language = 'es_ES.UTF-8';
        putenv("LANG=$language");
        setlocale(LC_ALL, $language);
        $name = strftime("%B", mktime(0, 0, 0, $month, 1, 2000));
        return $name;
    }

    public static function formatCampaing($campaing) {
        return substr($campaing, 0, 4) . '-' . substr($campaing, 4, 2);
    }

    public static function statusProductInvoiced($status) {
        $strStatus = '';
        switch ($status) {
            case 'D':
                $strStatus = '<span class="font-green-jungle">Despachado</span>';
                break;
            case 'A':
                $strStatus = '<span class="font-red-flamingo">Agotado</span>';
                break;
            case 'F':
                $strStatus = '<span class="font-yellow-gold">Fuera de catálogo</span>';
                break;
            default:
                $strStatus = 'Desconocido';
                break;
        }
        return $strStatus;
    }

    public static function statusOrder($status) {

        $response = [];
        switch ($status) {
            case '0':
                $response['strStatus'] = 'Enviado';
                $response['bg'] = 'font-grey-gallery';
                break;
            case '1':
                $response['strStatus'] = 'Enviado';
                $response['bg'] = 'font-grey-gallery';
                break;
            case '2':
                $response['strStatus'] = 'Guardado';
                $response['bg'] = 'font-grey-gallery';
                break;
            case '3':
                $response['strStatus'] = 'Facturado';
                $response['bg'] = 'font-marca';
                break;
            default:
                $response['strStatus'] = 'Desconocido';
                $response['bg'] = 'label-default';
                break;
        }

        return $response;
    }

    public static function loginBrand($marca) {
        switch ($marca) {
            case 'pcfk':
                $logo = 'pcfk-logo-original.png';
                $btn = 'dark';
                $width = '120px';
                $banner = 'login-pcfk.jpg';
                $class = 'login-6';
                break;
            case 'carmel':
                $logo = 'carmel-logo-original.png';
                $btn = 'yellow';
                $width = '150px';
                $banner = 'login-pcfk.jpg';
                $class = 'login-6-carmel';
                break;
            case 'loguin':
                $logo = 'loguin-logo-original.png';
                $btn = 'blue-chambray';
                $width = '120px';
                $banner = 'login-loguin.jpg';
                $class = 'login-6';
                break;
            default:
                $logo = 'carmel-logo-original.png';
                $btn = 'dark';
                $width = '150px';
                $banner = 'login-pcfk.jpg';
                $class = 'login-6';
                break;
        }
        return [
            'logo' => $logo,
            'btn' => $btn,
            'width' => $width,
            'banner' => $banner,
            'class' => $class
        ];
    }
    
    /**
     * Metodo para mostrar los grupos seleccionados de las alertas
     * @param array $array
     * @param string $codZona
     * @param string $nombreZona
     * @param string $prefix
     * @return html
     * @author      Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @date        Enero 04 de 2018
     */
    public static function selectGroup($array, $codZona, $nombreZona, $prefix) {        
        $selected = in_array((string)$codZona, $array, true)? 'selected="selected"' : '';
        return '<option value="' . $prefix . '|' . $codZona . '" '
                . $selected . ' >'
                . (($prefix == 'Z') ? $codZona . ' - ' : '') . $nombreZona . '</option>';
    }

}
