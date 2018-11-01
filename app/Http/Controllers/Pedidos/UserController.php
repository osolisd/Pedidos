<?php

/**
 * Controlador para gestionar todo lo relacionado con usuarios lista y cambio de contraseña
 */

namespace App\Http\Controllers\Pedidos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Util\CallWebService;
use App\Util\Helpers;
use App\Util\Constants;
use App\Util\WebServiceParameters;
use App\Util\Logger;

class UserController extends Controller {

    private $client;
    private $request;
    private $response;

    public function __construct() {
        //Verificamos que el usuario este autenticado
        $this->middleware('auth');
        //Creamos una nueva instancia de la clase para llamado de servicios REST
        $this->client = new CallWebService();
    }

    /**
     * @description Metodo para listar los usuarios según los perfiles
     * @param       Request $request
     * @return      boolean[]|string[]|\Illuminate\Http\JsonResponse
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 05 de 2017
     */
    public function findByProfile(Request $request, $mailP = null, $ajax = false) {
        //Seteamos el limite de memoria
        /*ini_set('memory_limit', Constants::MEMORY_LIMIT);
        //Capturamos el nombre de la marca
        $brand = (Helpers::isEquals(strtolower(auth()->user()->brand), strtolower(Constants::BRAND_PCFK))) ? Constants::BRAND_PCFK_COMPLETE : auth()->user()->brand;
        //Caturamos el documento de la asesora
        $adviser = (!empty($request->get('adviser'))) ? "'" . $request->get('adviser') . "'" : 'null';
        //Capturamos el mail plan
        $mailPlan = (!empty($mailP)) ? base64_decode($mailP) : auth()->user()->mail_plain;
        
        //Llamamos el servicio REST  de usuarios
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_USERS . "(key='" . auth()->user()->document . "',marca='" . $brand . "',idAsesora=" . $adviser . ",mailPlan='" . $mailPlan . "')", 
            []   
        );

        //Verificamos que no haya ocurrido algun error
        if (!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Seteamos el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('users.find.error')]);
            //Redireccionamos al inicio
            return redirect()->route('Inicio');
        }*/
        
        //Capturamos el nombre de la marca
        $brand = (Helpers::isEquals(strtolower(auth()->user()->brand), strtolower(Constants::BRAND_PCFK))) ? Constants::BRAND_PCFK_COMPLETE : auth()->user()->brand;
        $showFilter = false;
        $mailPlan = auth()->user()->mail_plain;
        
        if(!empty($request->get('filterMailPlan'))) {
            $mailPlan = base64_decode($request->get('filterMailPlan'));
            $showFilter = true;
        }
        
        if(!empty($mailP)) {
            $mailPlan = $mailPlan;
            $showFilter = true;
        }  
        
        if(!empty($request->get('filterUserDocument'))) {
            $showFilter = true;   
        }
        
        $userDocument = (!empty($request->get('filterUserDocument'))) ? "'" . $request->get('filterUserDocument') . "'" : 'null';  
        
        $route = "http://10.244.9.70:9199/odata/GetUserData(key='" . auth()->user()->document . "',marca='" . $brand . "',idAsesora=" . $userDocument . ",mailPlan='" . $mailPlan . "')";
        
        //Armamos el response  
        $this->response = [
            'error' => false,
            'document' => auth()->user()->document,
            'brand' => auth()->user()->brand,  
            'route' => $route,
            //'users' => (!empty($this->request->result)) ? $this->request->result->value : [],
            'mailPlan' => $mailPlan,
            'mailPlanList' => (new ZoneController())->findMailPlan(),
            'showFilter' => $showFilter
        ];

        //Retornamos el resultado en una vista
        return view('configuration.users')->with(['response' => $this->response]);
    }
    
    public function findByDocument(Request $request) {
        //Verificamos el documento de la asesora este en el request
        if(empty($request->get('adviser'))) {
            //Retornamos el mensaje en formato JSON
            return ['error' => true, 'message' => 'Ingresa el número de documento de la asesora.'];
        }
        
        //Capturamos el nombre de la marca
        $brand = (Helpers::isEquals(strtolower(auth()->user()->brand), strtolower(Constants::BRAND_PCFK))) ? Constants::BRAND_PCFK_COMPLETE : auth()->user()->brand;
        //Llamamos el servicio REST  de usuarios
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_USERS . "(key='" . auth()->user()->document . "',marca='" . $brand . "',idAsesora='" . $request->get('adviser') . "',mailPlan=null)",
            []
        );
        
        //Verificamos que no haya ocurrido algun error
        if (!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Retornamos el mensaje en formato JSON
            return ['error' => true, 'message' => 'Error al obtener los datos de la asesora, intenta de nuevo.'];
        }
        
        if(!empty($this->request->result) && count($this->request->result->value) <= 0) {
            return ['error' => true, 'message' => 'La asesora no existe, por favor verifica los datos.'];
        }
        
        //Armamos el response
        $this->response = [
            'error' => false,
            'user' => (!empty($this->request->result)) ? $this->request->result->value[0] : []
        ];
        
        return $this->response;
    }

    public function detail(Request $request, $document, $ajax = false) {
        //Verificamos el documento de la asesora este en el request
        if(empty($document)) {
            if($ajax) {
                return [
                    'error' => true,
                    'messages' => 'Ingresa el número de documento de la asesora.'
                ];
            }
            
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, ['Ingresa el número de documento de la asesora.']);
            return redirect()->route('Usuarios');
        }
        
        //Capturamos el nombre de la marca
        $brand = (Helpers::isEquals(strtolower(auth()->user()->brand), strtolower(Constants::BRAND_PCFK))) ? Constants::BRAND_PCFK_COMPLETE : auth()->user()->brand;
        //Llamamos el servicio REST  de usuarios
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_USERS . "(key='" . auth()->user()->document . "',marca='" . $brand . "',idAsesora='" . $document . "',mailPlan=null)",
            []
        );
        
        //Verificamos que no haya ocurrido algun error
        if (!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, ['La asesora no existe, por favor verifica los datos.']);
            return redirect()->route('Usuarios');
        }
        
        if(!empty($this->request->result) && count($this->request->result->value) <= 0) {
            if($ajax) {
                return [
                    'error' => true,
                    'messages' => 'Error al obtener los datos de la asesora, intenta de nuevo.'
                ];
            }
            
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, ['Error al obtener los datos de la asesora, intenta de nuevo.']);
            return redirect()->route('Usuarios');
        }
        
        //Armamos el response
        $this->response = [
            'error' => false,
            'user' => (!empty($this->request->result)) ? $this->request->result->value[0] : []
        ];
        
        if($ajax) {
            return $this->response;
        }
        
        return view('configuration.userdetail')->with(['response' => $this->response]);
    }
    
}
