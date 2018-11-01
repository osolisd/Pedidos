<?php
/**
 * Clase para el manejo de tolas zonas / Activación y Desactivación y Cambio de Campañas de Zonas y repetir campaña
 */

namespace App\Http\Controllers\Pedidos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Util\CallWebService;
use App\Util\Helpers;
use App\Util\Constants;
use App\Util\WebServiceParameters;
use Illuminate\Support\Facades\Log;

class ZoneController extends Controller {

    private $request; //Variable para capturar la información del request al servicio web
    private $response; //Variable para construir el objeto de respuesta 
    private $client; //Variable para instanciar la clase CallWebService
    
    private $zones; //Variable para capturar todas las zonas al momento de actualizarla
    private $zone; //Variable para crear el objeto de zona al momento de guardar
    
    public function __construct() {
        //Verificamos que el usuario este autenticado
        $this->middleware('auth');
        //Creamos una nueva instancia de la clase para llamado de servicios REST
        $this->client = new CallWebService();
    }

    /**
     * @description Metodo para obtener la lsita de zonas
     * @param       Request $request
     * @return      View
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Noviembre 02 de 2017
     */
    public function findAll() {
        //Llamamos el servicio REST de Zonas
        $this->request = Helpers::arrayToObject($this->client->callGet(
            WebServiceParameters::WS_ZONES, [
                'marca' => auth()->user()->brand
            ]
        ));
        
        //Verificamos el estado de la respuesta
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {  
            //Seteamos el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('zones.list.error')]);
        } else {
            //Seteamos el response
            $this->response = [
                'zones' => $this->request->result,
                'mailPlan' => $this->findMailPlan()
            ];
        }
        
        //Retornamos la vista de configuración
        return view('configuration.find')->with(['response' => $this->response]);
    }
    
    /**
     * @description Metodo para actualizar las zonas
     * @param       Request $request
     * @return      \Illuminate\Http\RedirectResponse
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Noviembre 02 de 2017
     */
    public function update(Request $request) {
        //Capturamos las zonas del Request
        $this->request = $request->get('Zonas');  
        
        //Recorremos la lista de zonas
        for($i = 1; $i <= count($this->request); $i ++) {
            //Verificamos el estado
            $status = (isset($this->request[$i]['estado'])) ? true : false;
            //Verificamos si la campaña se va a repetir
            $repeatCampaing = (isset($this->request[$i]['repetirCampana'])) ? true : false;
            //Seteamos la zona
            $this->zone = [
                'codZona' => $this->request[$i]['codZona'],
                'campana' => (empty($this->request[$i]['campana'])) ? '' : $this->request[$i]['campana'],
                'estado' => $status,
                'repetirCampana' => $repeatCampaing,
                'ip' => $request->getClientIp()
            ];
            //Agregamos la zona a la lista
            $this->zones[] = $this->zone;
        }
        
        //Llamamos el servicio REST de Zonas
        $this->request = Helpers::arrayToObject($this->client->callPost(
            WebServiceParameters::WS_ZONES, [
                'marca' => auth()->user()->brand,
                'Zonas' => $this->zones
            ],
            [
                'id' => auth()->user()->document
            ]
        ));
        
        //Verificamos si hubo errores en el llamado del servicio web
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Seteamos mensajes de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('zones.update.error')]);
        } else {
            //Seteamos el mensaje de operación exitosa
            Helpers::setSessionFlash(Constants::SUCCESS_MESSAGES, [$this->request->result->respuesta]);
        }
        
        //Retornamos la vista Zonas y pasamos el response
        return redirect()->route('Zonas')->with(['response' => $this->response]);
    } 
    
    /**
     * @description Metodo para obtener los Mail Plain de la marca
     * @return      NULL|List<String>
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Noviembre 08 de 2017
     */
    public function findMailPlan() {
        //Consultamos el servicio REST de Mail Plan
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_MAIL_PLAN,
            [
                'marca' => auth()->user()->brand
            ]
        );
        
        //Verificamos que el codigo de respuesta HTTP sea diferente a 200
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Seteamos el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('zones.list.error')]);
            //Retornamos vacio
            return null;
        }
        
        //Retornamos el resultado del servicio
        return $this->request->result;
    }
}
