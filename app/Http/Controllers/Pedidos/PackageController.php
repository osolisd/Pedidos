<?php

/**
 * Controlador para gestionar todo lo relacionado con las suscripciones a los paquetes del ahorro
 */

namespace App\Http\Controllers\Pedidos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Util\CallWebService;
use App\Util\Helpers;
use App\Util\Constants;
use PHPUnit\Util\Json;
use App\Util\WebServiceParameters;
use App\Util\Logger;

class PackageController extends Controller {

    private $request;
    private $response;
    private $client;
    private $packages = [];
    private $suscriptions = [];

    public function __construct() {   
        //Verificamos que el usuario este autenticado
        $this->middleware('auth');
        //Creamos una nueva instancia de la clase para llamado de servicios REST
        $this->client = new CallWebService();
    }

    /**
     * @description Metodo para consultar las estrategias disponibles
     * @param       Request $request
     * @return      Json
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 06 de 2017
     */
    public function findAll(Request $request) {
        //Consumimos el servicio REST de consulta de estrategias de paquete del ahorro
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_PACKAGES, 
            [
                'documentNumber' => auth()->user()->document,
                'campaign' => Helpers::getCurrentCampaign(),
                'marca' => auth()->user()->brand
            ]
        );

        //Verificamos que la respuesta sea 200
        if (!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            return [
                'error' => false,
                'message' => Helpers::getMessage('package.list.error')
            ];
        }

        if (!empty($this->request->result)) {
            $this->packages = $this->request->result;
        }

        $object = $this->findAllSuscriptions(new Request());

        if (!$object['error']) {
            $this->suscriptions = $object['packages'];
        }

        //Armamos el resultado
        $this->response = [
            'error' => false,
            'packages' => $this->packages,
            'suscriptions' => $this->suscriptions
        ];

        //Retornamos el resultado
        //return $this->response;   
        return view('pedidos.package')->with(['response' => $this->response]);
    }

    /**
     * @description Metodo para consultar las estrategias disponibles
     * @param       Request $request
     * @return      Json
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 06 de 2017
     */
    public function countFindAll() {
        //Consumimos el servicio REST de consulta de estrategias de paquete del ahorro
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_PACKAGES, 
            [
                'documentNumber' => auth()->user()->document,
                'campaign' => Helpers::getCurrentCampaign(),
                'marca' => auth()->user()->brand
            ]
        );

        //Verificamos que la respuesta sea 200
        if (!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            return false;
        }

        if (!empty($this->request->result)) {
            $this->packages = $this->request->result;
        }

        $object = $this->findAllSuscriptions(new Request());  

        if (!$object['error']) {
            $this->suscriptions = $object['packages'];
        }        
        
        /**
         * Si tiene subscripciones valido que sean menores que la campaña
         */
        if (count($this->suscriptions) > 0) {
            $varPckCamp = false;
            foreach ($this->suscriptions as $suscription) {
                if ($suscription->campanaEntrega >= Helpers::getCurrentCampaign()) {
                    $varPckCamp = true;
                    break;
                }
            }
            //si las suscripciones no son validas las anulo
            if(!$varPckCamp){
                $this->suscriptions = null;
            }
        }

        return (count($this->packages) > 0 || count($this->suscriptions) > 0);
    }

    /**
     * @description Metodo para consultar las campañas de entrega del paquete
     * @param       Request $request
     * @return      Json
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 07 de 2017
     */
    public function findAllCampaingById(Request $request) {
        //Consumimos el servicio REST para consultar las campañas de entrega
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_PACKAGE_CAMPAINGS, 
            [
                'paqueteAhorroEstrategiaId' => $request->get('id'),
                'documentNumber' => auth()->user()->document,
                'campaign' => Helpers::getCurrentCampaign(),
                'marca' => auth()->user()->brand
            ]
        );

        //Verificamos que no hayan errores
        if (!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            return [
                'error' => true,
                'message' => Helpers::getMessage('package.find.campaing.error')
            ];
        }

        //Armamos el response
        $this->response = [
            'error' => false,
            'campaings' => $this->request->result
        ];

        //Retornamos el resultado
        return $this->response;
    }

    /**
     * @description Metodo para obtener las suscripciones de la asesora
     * @param       Request $request
     * @return      Json
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 07 de 2017
     */
    public function findAllSuscriptions(Request $request) {
        $document = '';
        if($request == null || empty($request->get('adviserDocument'))) {
            $document = auth()->user()->document;  
        } else {
            $document = $request->get('adviserDocument');  
        }
        
        //Llamamos el servicio REST para obtener la lista de las suscripciones a paquetes
        $this->request = $this->client->callPost(
            WebServiceParameters::WS_PACKAGE_SUSCRIPTION_ADMINISTRATOR, 
            [
                'operacion' => 2,
                'campanaPedido' => Helpers::getCurrentCampaign(),
                'codigoDocumento' => $document  
            ],    
            [
                'marca' => auth()->user()->brand     
            ]
        );

        //Verificamos la respuesta HTTP sea diferente a OK
        if (!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            return [
                'error' => true,
                'message' => 'Error al obtener las suscripciones, intenta de nuevo.'
            ];
        }

        //Construimos la respuesta
        $this->response = [
            'error' => false,
            'packages' => $this->request->result
        ];
        //Retornamos la respuesta
        return $this->response;
    }

    /**
     * @description Metodo para guardar la suscripcion al paquete 
     * @param       Request $request
     * @return      Json
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 07 de 2017
     */
    public function save(Request $request) {
        //Consultamos el servicio REST para guardar la suscripción al paquete
        $this->request = $this->client->callPost(
            WebServiceParameters::WS_PACKAGE_SUSCRIPTION_ADMINISTRATOR,   
            [
                'operacion' => 1,
                'cantidad' => $request->get('quantity'),
                'pedidoSuscrito' => 1,
                'paqueteAhorroFechasEntregaId' => $request->get('campaingId'),
                'paqueteAhorroEstrategiaId' => $request->get('id'),
                'codigoDocumento' => auth()->user()->document,
                'paqueteAhorroSuscripcionesId' => null,  
                'personaId' => 0,
                'fechaSuscripcion' => null,
                'fechaPedidoSuscrito' => null,
                'fechaModificacion' => null,
                'campanaEntrega' => null,   
                'campanaPedido' => null,
                'plu' => null,
                'descripcionPlu' => null,
                'cantidadMaxima' => 0,
                'eliminar' => null,
                'pedidoEnviado' => false,
                'fechaPedidoEnviado' => null,
                'paqueteAhorroSuscripcionesEntregaId' => null
            ],
            [
                'marca' => auth()->user()->brand
            ]
        );

        //Verificamos al respuesta HTTP sea diferente a OK
        if (!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, ['Error al guardar la suscripción al paquete del ahorro, intenta de nuevo.']);
        } else {
            Helpers::setSessionFlash(Constants::SUCCESS_MESSAGES, ['Te has suscrito al paquete correctamente.']);
        }

        //Retornamos la respuesta
        return redirect()->route('Paquetes');
    }

    /**
     * @description Metodo para actualizar la suscripción al paquete
     * @param       Request $request
     * @return      Json
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 07 de 2017 
     */
    public function update(Request $request) {
        //Consultamos el servicio REST para editar la suscripción
        $this->request = $this->client->callPost(
            WebServiceParameters::WS_PACKAGE_SUSCRIPTION_ADMINISTRATOR, 
            [
                'operacion' => 1,
                'cantidad' => $request->get('quantity'),
                'pedidoSuscrito' => 1,
                'paqueteAhorroFechasEntregaId' => $request->get('campaingId'),
                'paqueteAhorroEstrategiaId' => $request->get('packageId'),
                'codigoDocumento' => auth()->user()->document,
                'paqueteAhorroSuscripcionesId' => $request->get('id'),
                'personaId' => null,
                'fechaSuscripcion' => null,
                'fechaPedidoSuscrito' => null,
                'fechaModificacion' => null,
                'campanaEntrega' => null,
                'campanaPedido' => null,
                'plu' => null,
                'descripcionPlu' => null,
                'cantidadMaxima' => 0,
                'eliminar' => null,
                'pedidoEnviado' => false,
                'fechaPedidoEnviado' => null,
                'paqueteAhorroSuscripcionesEntregaId' => null
            ], 
            [
                'marca' => auth()->user()->brand
            ]
        );

        //Verificamos que la respuesta HTTP dea diferente de OK
        if (!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            return [
                'error' => false,
                'message' => 'Error al actualizar la suscripción al paquete del ahorro, intenata de nuevo.'
            ];
        }

        //Construimos la respuesta
        $this->response = [
            'error' => false,
            'message' => 'Suscripción paquete actualziada con exito.'
        ];

        //Retornamos la respuesta
        return $this->response;
    }

    /**
     * @description Metodo para eliminar suscripción al paquete
     * @param       Request $request
     * @return      Json
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 07 de 2017
     */
    public function remove(Request $request) {
        $this->request = $this->client->callPost(
            WebServiceParameters::WS_PACKAGE_SUSCRIPTION_ADMINISTRATOR, 
            [
                'operacion' => 4,
                'cantidad' => 0,
                'pedidoSuscrito' => 1,
                'paqueteAhorroFechasEntregaId' => $request->get('campaingId'),
                'paqueteAhorroEstrategiaId' => $request->get('packageId'),
                'codigoDocumento' => auth()->user()->document,
                'paqueteAhorroSuscripcionesId' => $request->get('id'),
                'personaId' => null,
                'fechaSuscripcion' => null,
                'fechaPedidoSuscrito' => null,
                'fechaModificacion' => null,
                'campanaEntrega' => null,
                'campanaPedido' => null,
                'plu' => null,
                'descripcionPlu' => null,
                'cantidadMaxima' => 0,
                'eliminar' => null,
                'pedidoEnviado' => false,
                'fechaPedidoEnviado' => null,
                'paqueteAhorroSuscripcionesEntregaId' => null
            ], 
            [
                'marca' => auth()->user()->brand
            ]
        );

        if (!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, ['Error al eliminar la suscripción al paquete, intenta de nuevo.']);
        } else {
            Helpers::setSessionFlash(Constants::SUCCESS_MESSAGES, ['Se ha eliminado la suscripción al paquete.']);
        }

        //Retornamos la respuesta
        return redirect()->route('Paquetes');
    }

    public function findAllRemove() {
        if(!Session::get('showPackageMessage')[0]) {
        
            $this->suscriptions = self::findAllSuscriptions(new Request());
            
            if($this->suscriptions['error']) {
                return $this->suscriptions;
            }
            
            $remove = [];
            
            $packages = null;
            $message = null;
            
            $i = 0;
            foreach($this->suscriptions['packages'] as $package) {
                if($package->eliminar == 1) {
                    $packages = $packages . $package->plu . ' - ' . $package->descripcionPlu . ' ';
                    $i ++;
                }  
            }
            
            if(!empty($this->suscriptions['packages']) && count($this->suscriptions['packages']) > 0) {
                if($i > 1 && !empty($packages)) {
                    $message = 'Los paquetes <strong>' . $packages . '</strong> no se enviaran en tu pedido ya que no aplica para tu zona.';
                } else if(!empty($packages)) {
                    $message = 'El paquete <strong>' . $packages . '</strong> no se enviara en tu pedido ya que no aplica para tu zona.';
                }  
            }
            
            if(!empty($message)) {
                Helpers::setSessionFlash(Constants::INFO_MESSAGES, [$message]);  
            }
        
            Session::forget('showPackageMessage');
            Session::push('showPackageMessage', true);  
        }
    }
    
}
