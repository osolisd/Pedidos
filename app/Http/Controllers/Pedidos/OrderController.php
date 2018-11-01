<?php

/**
 * Controlador para gestionar todo lo relacionado con los pedidos
 */

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Util\WebServiceParameters;
use App\Util\CallWebService;
use App\Util\Constants;
use App\Util\Helpers;
use Psy\Util\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller {

    private $request;
    private $response;
    private $client;
    
    private $orders;
    private $order;
    
    private $totalBill = 0; //Variable para guardar el valor factura a pagar
    private $totalCatalog = 0; //Variable para guardar el valor catalogo
    private $totalPoints = 0; //Variable para guardar los puntos
    private $isUpdateOrder = false; //Bandera para identificar si se crea o modifica el pedido
    private $products = []; //Variable para guardar los plus de un pedido

    
    public function __construct() {
        //Verificamos que el usuario este autenticado
        $this->middleware('auth');
        //Creamos una nueva instancia de la clase para llamado de servicios REST
        $this->client = new CallWebService();
    }
    
    /**
     * @description Metodo para obtener el historial de pedidos
     * @param       string $document
     * @param       int $quantity
     * @return      JSON / View
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Septiembre 18 de 2017
     */
    public function findAll($quantity = 0) {
        $this->findAmmount();   
       
        $url = WebServiceParameters::WS_FIND_ALL_ORDERS;
        
        if(Helpers::isDirector()) {
            $url = "odata/GetOrderbyDirZona(id='" . auth()->user()->document . "',marca='" . auth()->user()->brand . "',opcion=1,idOrder=null)";
        }  
        
        if(Helpers::isAdministrator()) {
            $url = "odata/GetOrderToAdmin(marca='" . auth()->user()->brand . "',opcion=1,campanaI=null,campanaF=null,codZona='" . auth()->user()->code_zone . "')";
        }  
        
        //Llamamos el servicio REST para consultar historico de pedidos
        $this->request = $this->client->callGet(
            $url,
            [
                'id' => auth()->user()->document,
                'marca' => auth()->user()->brand
            ]
        );
        
        //Verificamos si existe error
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK) && $quantity > 0) {
            //Retornamos la respuesta con el mensaje de error
            return [
                'error' => true,
                'messages' => [
                    Helpers::getMessage('order.find.all.error')  
                ]
            ];
        }
        
        //Verificamos que el haya error y el llamado no sea via AJAX
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK) && $quantity <= 0) {   
            //Setamos el mensaje de error a mostrar en historico pedidos 
            Helpers::setSessionFlash(
                Constants::ERROR_MESSAGES, [
                Helpers::getMessage('order.find.all.error')
            ]);
        }
        
        //Verificamos que el valor del parametro sea mayor a cero
        if ($quantity > 0) {
            //Si es mayor a cero el llamado se realiza por AJAX y retorna un JSON
            //retornamos la respuesta con los pedidos solicitados por el parametro
            if(Helpers::isAdviser() || Helpers::isEmployee()) {
                return [
                    'error' => false,
                    'orders' => (!empty($this->request->result)) ? array_slice($this->request->result, 0, $quantity) : []
                ];
            }
            
            return [
                'error' => false,
                'orders' => (!empty($this->request->result->value)) ? array_slice($this->request->result->value, 0, $quantity) : []
            ];
        } 
        
        //Retornamos la lista de todos los pedidos retornados por el servicio
        $this->response = Array(
            'error' => false,
            'orders' => (!empty($this->request->result->value)) ? $this->request->result->value : $this->request->result,
            'title' => (!Helpers::isAdviser() && !Helpers::isEmployee()) ? 'Pedidos en curso' : ''
        );
        
        //Retornamos la vista historicos de pedidos
        return view('pedidos.historyorders')->with(['response' => $this->response]);
    }
    
    public function findAllByAdministrator(Request $request, $history = 0) {
        $filter = "";
        if(Helpers::isDirector() && $history == 0) {
            $this->response = [
                'title' => 'Pedidos en curso',
                'current' => true,
                'currentOrderRoute' => WebServiceParameters::END_POINT_SERVER . "/odata/GetOrderbyDirZona(id='" . auth()->user()->document . "',marca='" . auth()->user()->brand . "',opcion=3,idOrder=null)",
                'orderDetailRoute' => WebServiceParameters::END_POINT_SERVER . "/odata/GetOrderbyDirZona(id='" . auth()->user()->document . "',marca='" . auth()->user()->brand . "',opcion=2,idOrder"
            ];
            
            if(!empty($request->get('filterBalance'))) {
                $filter = $request->get('filterBalance') == 1 ? "SaldoPagar gt 0" : "SaldoPagar lt 1";
                $this->response['filterBalance'] = $request->get('filterBalance');
            }
            
            if(!empty($request->get('filterAmmount'))) {
                if(!empty($filter) && $filter != '') {
                    $filter = $filter . " and ";
                }
                
                if($request->get('filterAmmount') == 1) {
                    $filter = $filter . "MontoMin eq 0";
                } else if($request->get('filterAmmount') == 2 ){ 
                    $filter = $filter . "MontoMin eq 1";
                } else if($request->get('filterAmmount') == 3) {
                    $filter = $filter . "CupoMax eq 0";
                } else {
                    $filter = $filter . "CupoMax eq 1";
                }
                
                $this->response['filterAmmount'] = $request->get('filterAmmount');
            }
            
            if(!empty($request->get('filterStatus'))) {
                if(!empty($filter) && $filter != '') {
                    $filter = $filter . " and ";
                }
                
                $filter = $filter . "EstadoStencil eq '" . $request->get('filterStatus') . "'";
                $this->response['filterStatus'] = $request->get('filterStatus');
            }
            
            if(!empty($request->get('filterClasification'))) {
                if(!empty($filter) && $filter != '') {
                    $filter = $filter . " and ";
                }
                
                $filter = $filter . "ClasificacionValor eq '" . $request->get('filterClasification') . "'";
                $this->response['filterClasification'] = $request->get('filterClasification');
            }
            
            if(!empty($request->get('filterDocumentAdviser'))) {  
                if(!empty($filter) && $filter != '') {
                    $filter = $filter . " and ";
                }
                
                $filter = $filter . "NumeroDcto eq '" . $request->get('filterDocumentAdviser') . "'";  
            }
        } 
        
        if(Helpers::isDirector() && $history == 1) {
            if(!empty($request->get('filterStartCampaing')) && empty($request->get('filterEndCampaing'))) {
                $filter = $filter . "CampanaId eq '" . $request->get('filterStartCampaing') . "'";
                $this->response['filterStartCampaing'] = $request->get('filterStartCampaing');
            }
            
            if(!empty($request->get('filterStartCampaing')) && !empty($request->get('filterEndCampaing'))) {
                $filter = $filter . "CampanaId ge '" . $request->get('filterStartCampaing') . "' and CampanaId le '" . $request->get('filterEndCampaing') . "'";
                $this->response['filterStartCampaing'] = $request->get('filterStartCampaing');
                $this->response['filterEndCampaing'] = $request->get('filterEndCampaing');  
            }
            
            if(!empty($request->get('filterDocumentAdviser'))) {  
                if(!empty($filter) && $filter != '') {
                    $filter = $filter . " and ";
                }
                
                $filter = $filter . "NumeroDcto eq '" . $request->get('filterDocumentAdviser') . "'";  
            }
            
            $this->response = [
                'title' => 'Histórico pedidos',
                'current' => false,
                'historyOrderRoute' => WebServiceParameters::END_POINT_SERVER . "/odata/GetOrderbyDirZona(id='" . auth()->user()->document . "',marca='" . auth()->user()->brand . "',opcion=4,idOrder=null)"  
            ];
        }
        
        if(Helpers::isAdministrator() && $history == 0) {
            $zone = 'null'; //!empty($request->get('filterZone')) ? "'" . $request->get('filterZone') . "'" : 'null';
            $mail = empty($request->get('filterMailPlan')) ? auth()->user()->	mail_plain : $request->get('filterMailPlan'); //!empty($request->get('filterMailPlan')) ? "'" . $request->get('filterMailPlan') . "'" : 'null';
            $initialCampaing = 'null'; //!empty($request->get('filterStartCampaing')) ? "'" . $request->get('filterStartCampaing') . "'" : 'null';
            $endCampaing = 'null'; //!empty($request->get('filterEndCampaing')) ? "'" . $request->get('filterEndCampaing') . "'" : 'null';  
            
            $this->response = [
                'title' => 'Pedidos en curso',
                'current' => true,
                'currentOrderRoute' => WebServiceParameters::END_POINT_SERVER . "/odata/GetOrderToAdmin(marca='" . auth()->user()->brand . "',opcion=3,campanaI=" . $initialCampaing .",campanaF=" . $endCampaing . ",codZona=" . $zone . ",mailPlan='" . $mail . "',idOrder=null)",
                'orderDetailRoute' => WebServiceParameters::END_POINT_SERVER . "/odata/GetOrderToAdmin(marca='" . auth()->user()->brand . "',opcion=2,campanaI=null,campanaF=null,codZona=null,mailPlan=null,idOrder"
            ];
            
            
            if(!empty($request->get('filterAmmount'))) {
                if($request->get('filterAmmount') == 1) {
                    $filter = $filter . "MontoMin eq 0";
                } else if($request->get('filterAmmount') == 2 ){
                    $filter = $filter . "MontoMin eq 1";
                } else if($request->get('filterAmmount') == 3) {
                    $filter = $filter . "CupoMax eq 0";
                } else {
                    $filter = $filter . "CupoMax eq 1";
                }
                
                $this->response['filterAmmount'] = $request->get('filterAmmount');
                $this->response['showFilter'] = true;
            }
            
            if(!empty($request->get('filterZone'))) {
                if(!empty($filter) && $filter != '') {
                    $filter = $filter . " and ";
                }
                
                $filter = $filter . "CodigoZona eq '" . $request->get('filterZone') . "'";
                $this->response['filterZone'] = $request->get('filterZone');
                $this->response['showFilter'] = true;
            }
            
            if(!empty($request->get('filterMailPlan'))) {
                $this->response['filterMailPlan'] = $request->get('filterMailPlan');
                $this->response['showFilter'] = true;
            }
            
            if(!empty($request->get('filterCampaing'))) {
                if(!empty($filter) && $filter != '') {
                    $filter = $filter . " and ";
                }
                
                $filter = $filter . "CampanaId eq '" . $request->get('filterCampaing') . "'";
                $this->response['filterCampaing'] = $request->get('filterCampaing');  
                $this->response['showFilter'] = true;
            }
            
            if(!empty($request->get('filterAdviserDocument'))) {
                if(!empty($filter) && $filter != '') {
                    $filter = $filter . " and ";
                }
                
                $filter = $filter . "NumeroDcto eq '" . $request->get('filterAdviserDocument') . "'";
                $this->response['filterAdviserDocument'] = $request->get('filterAdviserDocument');
                $this->response['showFilter'] = true;
            }
            
            if(!empty($request->get('filterDivision'))) {
                if(!empty($filter) && $filter != '') {
                    $filter = $filter . " and ";
                }
                
                $filter = $filter . "Division eq '" . $request->get('filterDivision') . "'";
                $this->response['filterDivision'] = $request->get('filterDivision');
                $this->response['showFilter'] = true;
            }
        }
        
        if(Helpers::isAdministrator() && $history == 1) {
            $startCampaing = 'null';
            $endCampaing = 'null';
            
            $this->response = [
                'title' => 'Histórico pedidos',
                'current' => false
            ];
            
            if(!empty($request->get('filterStartCampaing'))) {
                $startCampaing = "'" . $request->get('filterStartCampaing') . "'";
                $this->response['filterStartCampaing'] = $request->get('filterStartCampaing');
                $this->response['showFilter'] = true;  
            }
            
            if(!empty($request->get('filterEndCampaing'))) {
                $endCampaing = "'" . $request->get('filterEndCampaing') . "'";
                $this->response['filterEndCampaing'] = $request->get('filterEndCampaing');
                $this->response['showFilter'] = true;
            }
            
            if(!empty($request->get('filterDocumentAdviser'))) {
                $filter = "NumeroDcto eq '" . $request->get('filterDocumentAdviser') . "'";
                $this->response['showFilter'] = true;  
            }
            
            $this->response['historyOrderRoute'] = WebServiceParameters::END_POINT_SERVER . "/odata/GetOrderToAdmin(marca='" . auth()->user()->brand . "',opcion=5,campanaI=" . $startCampaing . ",campanaF=" . $endCampaing . ",codZona=null,mailPlan=null,idOrder=null)";
        }
        
        $this->response['filter'] = $filter;
        
        if(Helpers::isDirector()) {
            $this->response['stencilStatus'] = (new AlertController())->findAllStencilStatus(true);
            $this->response['clasifications'] = (new AlertController())->findAllClasifications(true);
        }
        
        if(Helpers::isAdministrator() && $history == 0) {
            $list = (new AlertController())->findAllZones($request, true);  
            $listCampaing = (new AlertController())->findAllCampaings(true);
            $zones = [];
            $campaings = []; 
            
            if(!empty($request->get('filterMailPlan'))) {
                foreach ($list as $object) {
                    if(Helpers::isEquals($object->mailPlan, $request->get('filterMailPlan'))) {
                        $zones[] = $object;
                        if(!in_array($object->campana, $campaings)) {
                            $campaings[] = $object->campana;
                        }
                    }
                }
            } else {
                $zones = $list;
                $campaings = $listCampaing;  
            }
            
            $this->response['zones'] = $zones;
            $this->response['allZones'] = $list;
            $this->response['mailPains'] = (new AlertController())->findAllMainPlain(true);
            $this->response['campaings'] = $campaings;
            $this->response['divisions'] = (new AlertController())->findAllDivisions(true);
        }
        
        if(Helpers::isDirector() && $history == 1 || Helpers::isAdministrator() && $history == 1) {
            $campaings = (new AlertController())->findAllCampaings(true);
            
            $startCampaing = [];
            $endCampaing = [];  
            
            if(!empty($request->get('filterStartCampaing'))) {
                foreach($campaings as $campaing) { 
                    if(!Helpers::isEquals($campaing, $request->get('filterStartCampaing'))) {
                        $startCampaing[] = $campaing;
                    }
                }
            } 
            
            if(empty($request->get('filterStartCampaing'))){
                $startCampaing = $campaings;  
            }
            
            if(!empty($request->get('filterEndCampaing'))) {
                foreach ($campaings as $campaing) {
                    if(!Helpers::isEquals($campaing, $request->get('filterEndCampaing'))) {
                        $endCampaing[] = $campaing;  
                    }
                }
            }
            
            if(empty($request->get('filterEndCampaing'))) {
                $endCampaing = $campaings; 
            }
            
            $this->response['startCampaing'] = $startCampaing;
            $this->response['endCampaing'] = $endCampaing;    
        }
        
        //Retornamos la vista historicos de pedidos
        return view('pedidos.historyordersadministrator')->with(['response' => $this->response]);
    }
    
    /**
     * @description Metodo para crear un nuevo pedido a la asesora con el perfil Directora de Zona / Administrador / Emtelco
     * @param       Request $request
     * @return      Json
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 11 de 2017 
     */
    public function createByAdministrator(Request $request) {
        $validate = $this->validateAdviser($request->get('document'), true);
        
        $order = null;
        
        if($validate['error']) {
            return $validate;
        }
        
        //Llamamos el servicio REST para verificar si la asesora tiene pedido en la campaña actual
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_FIND_ORDER,
            [
                'id' =>  $request->get('document'),
                'marca' => auth()->user()->brand,
                'campana' => $request->get('campaing')  
            ]
        );
        
        //Verificamos que no exista errores
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Seteamos el resultado
            return [
                'error' => Constants::TRUE, 
                'messages' => Helpers::getMessage('order.check.order.create.error')
            ];
        }
        
        //Verificamos que el id de pedido no sea vacio
        if (!empty($this->request->result) && !empty($this->request->result->PedidoId)) {
            //Llamamos el servicio REST detalle pedido
            $this->request = $this->client->callGet(
                WebServiceParameters::WS_ORDER_DETAIL,
                [
                    'id' => $this->request->result->PedidoId,
                    'marca' => auth()->user()->brand,
                    'document' => $request->get('document')
                ]
            );
            
            //Verificamos el codigo de respuesta HTTP
            if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
                return [
                    'error' => Constants::TRUE,
                    'messages' => Helpers::getMessage('call.web.service.orders.detail.error')
                ];
            }
            
            //Capturamos el encabezado
            $this->order = [
                'campana' => trim($this->request->result->CampanaId),
                'fecha' => Helpers::dateFormat($this->request->result->Fecha),
                'pedidoId' => $this->request->result->PedidoId,
                'zona' => $this->request->result->CodigoZona,
                'procesado' => $this->request->result->Procesado,
                'puntos' => $this->request->result->PuntosPedido,
                'Editable' => $this->request->result->Editable
            ];
            
            // Recorremos la lista de PLU
            foreach ($this->request->result->OrderDetail as $product) {
                // Agregamos el plu a la lista de productos
                $this->products[] = [
                    'plu' => $product->CodigoPlu,
                    'descripcion' => $product->Descripcion,
                    'cantidad' => $product->Cantidad,
                    'precioFactura' => Helpers::removeNumberFormat($product->PrecioFactura),
                    'precioCatalogo' => Helpers::removeNumberFormat($product->PrecioCatalogo),
                    'puntosPrenda' => Helpers::calculatePoints(Helpers::removeNumberFormat($product->PrecioFactura), 1)
                ];
                
                // Calculamos el total factura
                $this->totalBill = Helpers::removeNumberFormat($this->totalBill) + Helpers::removeNumberFormat($product->PrecioFactura) * $product->Cantidad;
                // Calculamos el precio catalogo
                $this->totalCatalog = Helpers::removeNumberFormat($this->totalCatalog) + Helpers::removeNumberFormat($product->PrecioCatalogo) * $product->Cantidad;
                //Calculamos los puntos
                $this->totalPoints = $this->totalPoints + Helpers::calculatePoints(Helpers::removeNumberFormat($product->PrecioFactura), $product->Cantidad);
            }
            
            // Agregamos el totalizado
            $this->order['totalFacturaPedido'] = $this->totalBill; //Asignamos el total del pedido precio factura
            $this->order['totalCatalogoPedido'] = $this->totalCatalog; //Asignamos el total del pedido precio catalogo
            $this->order['calculadoPedido'] = $this->totalCatalog - $this->totalBill; //Calculamos la ganancia
            $this->order['totalPuntos'] = $this->totalPoints; //Calculamos el total de puntos
            //Agregamos la respuesta
            $order = [
                'pedidoEncabezado' => $this->order,
                'pedidoDetalle' => $this->products
            ];
        }
        
        //Llamamos el servicio REST de montos
        $this->request = $this->client->callGet(
            'api/amounts',
            [
                'id' => $request->get('document'),
                'marca' => auth()->user()->brand
            ]
        );
        
        //Verificamos si existe error
        if(empty($this->request->result) && !Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Retornamos el resultado            
            return [
                'error' => true,
                'messages' => Helpers::getMessage('call.web.service.amount.error')
            ];
        }
        
        //Capturamos los montos y los agregamos al response
        $ammount = Helpers::arrayToObject([
            'MontoMin' => Helpers::removeNumberFormat($this->request->result->MontoMin),
            'MontoMax' => Helpers::removeNumberFormat($this->request->result->MontoMax),
            'ToleranciaMontoMin' => Helpers::removeNumberFormat($this->request->result->ToleranciaMontoMin),
            'ToleranciaCupoMax' => Helpers::removeNumberFormat($this->request->result->ToleranciaCupoMax)
        ]);
        
        //Eliminamos los datos de montos de la sesion
        Session::forget('monto');
        //Agregamos a la sesión los datos de montos
        Session::put('monto', $ammount);
        
        //Construimos el resultado
        $this->response = [
            'error' => false,
            'order' => $order,
            'monto' => $ammount
        ];
        
        //Retornamos el response        
        return $this->response;
    }
    
    /**
     * @description Metodo para crear un pedido
     * @return      view
     * @author      Felipe.Echeverri <felipe.echeverri@ingeneo.com.co>
     * @date        Septiembre 22 de 2017
     */
    public function create() {
        //Verificamos que el usuario sea una asesora
        if(Helpers::isAdviser() || Helpers::isEmployee()) {
            // Verificamos si la asesora esta bloqueada por Estencil
            // Verificamos si se produce un error al obtener los montos
            if($this->validateAdviser()['error'] || !$this->findAmmount()) {  
                //Llamamos la vista de historico pedidos
                return redirect()->route('Inicio')->with(['response' => $this->response]);
            }
            
            //Llamamos el servicio REST para verificar si la asesora tiene pedido en la campaña actual
            $this->request = $this->client->callGet(
                WebServiceParameters::WS_FIND_ORDER, 
                [
                    'id' => auth()->user()->document,
                    'marca' => auth()->user()->brand,
                    'campana' => Helpers::getCurrentCampaign()
                ]
            );  
            
            //Verificamos que no exista errores
            if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
                //Agregamos el mensaje de error
                Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('order.check.order.create.error')]);
                //Llamamos la vista de historico pedidos
                return redirect()->route('Inicio')->with(['response' => $this->response]);
            }
            
            //Verificamos que el id de pedido no sea vacio
            if (!empty($this->request->result->PedidoId)) {
                //Llamamos a la vista detalle pedido
                return redirect()->route('OrderDetail', ['id' => $this->request->result->PedidoId]);
            }
        }   
        
        //Llamamos la vista de nuevo pedido
        return view('pedidos.createorder')->with(['response' => null]);  
    }
    
    /**
     * @description Metodo para validar el PLU
     * @param       string $plu
     * @param       int $quantity
     * @return      Json
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Septiembre 20 de 2017
     */
    public function checkProduct(Request $request, $plu, $quantity) {
        //Validamos el plu sea valido
        if (empty($plu) && !is_numeric($plu)) {
            //Agregamos el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, Array(Helpers::getMessage('product.validate.plu.error')));
        }
        
        //Llamamos el servicio REST para valdiar PLU
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_PLU_VALIDATE, 
            [
                'plu' => $plu,
                'codigoZona' => (!empty($request->get('zone'))) ? $request->get('zone') : Helpers::getZone(),
                'marca' => auth()->user()->brand
            ]
        );
        
        //Verificamos que hayan errores
        if(!$this->request->code == Constants::HTTP_OK || empty($this->request->result)) {
            //Retormanos un JSON con la respuesta
            return [
                'error' => true,
                'producto' => null,
                'messages' => [
                    Helpers::getMessage('product.validate.plu.campaing.error')
                ]
            ];
        }
        
        //Retornamos un JSON con los datos del producto
        return [
            'error' => false,
            'producto' => [
                'PLU' => $this->request->result->CodigoPlu,
                'Description' => $this->request->result->Description,
                'Price' => Helpers::removeNumberFormat($this->request->result->Price),
                'InvoicePrice' => Helpers::removeNumberFormat($this->request->result->InvoicePrice),
                'esPaquete' => $this->request->result->EsPaqueteAhorro,
                'PluExcluido' => $this->request->result->PluExcluido    
             ],
            'puntos' => ($this->request->result->PluExcluido) ? 0 : Helpers::calculatePoints(Helpers::removeNumberFormat($this->request->result->InvoicePrice), $quantity),
            'puntosPrenda' => ($this->request->result->PluExcluido) ? 0 : Helpers::calculatePoints(Helpers::removeNumberFormat($this->request->result->InvoicePrice), 1)
        ];  
    }
    
    /**
     * @description Metodo para obtener el pedido por id
     * @param       int $id
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Septiembre 18 de 2017
     */
    public function findById(Request $request, $id, $status = null, $document = null) {
        //Verificamos que el id sea un número y sea mayor a cero
        if (!is_numeric($id) && $id <= 0) {
            //Agregamos el mensaje de error a la sesion 
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, Array(Helpers::getMessage('order.find.id.invalid')));
            //Redireccionamos a la vista de historico pedidos
            return redirect()->route('Inicio');
        }
        
        //Consultamos el monto minimo y verificamos si hay error
        if(!self::findAmmount((!empty($document)) ? $document : auth()->user()->document)) {
            //Redireccionamos al inicio
            return redirect()->route('Inicio');
        }
        
        //Verificamos que el estado del pedido sea procesado
        if(isset($status) && $status == 3 || isset($status) && $status == 1) {
            //Llamamos el servicio para consultar el detalle de la factura
            $this->request = $this->client->callGet(
                WebServiceParameters::WS_DETAIL_BILL, 
                [
                    'id' => $id,
                    'marca' => auth()->user()->brand,
                    'document' => (!empty($document)) ? $document : auth()->user()->document
                ]
            );
            
            //Llamamos a la vista detalle pedido
            return view('pedidos.orderdetails')->with(['response' => $this->request->result]);  
        }
        
        //Llamamos el servicio REST detalle pedido
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_ORDER_DETAIL, 
            [
                'id' => $id,
                'marca' => auth()->user()->brand,
                'document' => (!empty($document)) ? $document : auth()->user()->document
            ]
        );
        
        //Verificamos el codigo de respuesta HTTP
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Agregamos el mensaje de error a la sesion
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, Array(Helpers::getMessage('call.web.service.orders.detail.error')));
            //Redireccionamos a la vista de historico pedidos
            return redirect()->route('Pedidos')->with(['response' => $this->response]);
        }
        
        //Capturamos el encabezado
        $this->order = [
            'campana' => trim($this->request->result->CampanaId),
            'fecha' => Helpers::dateFormat($this->request->result->Fecha),
            'pedidoId' => $this->request->result->PedidoId,
            'zona' => $this->request->result->CodigoZona,
            'procesado' => $this->request->result->Procesado,
            'puntos' => $this->request->result->PuntosPedido,
            'Editable' => $this->request->result->Editable
        ];

        $totalFacturaPaquete = 0;
        $totalCatalogoPaquete = 0;
        $totalGananciaPaquete = 0;
        
        // Recorremos la lista de PLU
        foreach ($this->request->result->OrderDetail as $product) {
            $object = [
                'plu' => $product->CodigoPlu,
                'descripcion' => $product->Descripcion,
                'cantidad' => $product->Cantidad,
                'precioFactura' => Helpers::removeNumberFormat($product->PrecioFactura),
                'precioCatalogo' => Helpers::removeNumberFormat($product->PrecioCatalogo),
                'puntosPrenda' => ($product->PluExcluido) ? 0 : Helpers::calculatePoints(Helpers::removeNumberFormat($product->PrecioFactura), 1),
                'esPaquete' => $product->EsPaquete,
                'PluExcluido' => $product->PluExcluido  
            ];
            
            //Verificamos si el PLU es un paquete 
            if($product->EsPaquete) {
                $totalFacturaPaquete = $totalFacturaPaquete + (Helpers::removeNumberFormat($product->PrecioFactura) * $product->Cantidad);
                $totalCatalogoPaquete = $totalCatalogoPaquete + (Helpers::removeNumberFormat($product->PrecioCatalogo) * $product->Cantidad);
                $totalGananciaPaquete = $totalGananciaPaquete + (Helpers::removeNumberFormat($product->PrecioCatalogo) * $product->Cantidad) - (Helpers::removeNumberFormat($product->PrecioFactura) * $product->Cantidad);
                
                //Agregamos el objeto al inicio del array  
                array_unshift($this->products, $object);
            } else {
                //Agregamos el plu al array
                $this->products[] = $object;
                
                  
                // Calculamos el total factura
                $this->totalBill = Helpers::removeNumberFormat($this->totalBill) + Helpers::removeNumberFormat($product->PrecioFactura) * $product->Cantidad;
                // Calculamos el precio catalogo
                $this->totalCatalog = Helpers::removeNumberFormat($this->totalCatalog) + Helpers::removeNumberFormat($product->PrecioCatalogo) * $product->Cantidad;
                if(!$product->PluExcluido) {
                    //Calculamos los puntos
                    $this->totalPoints = $this->totalPoints + Helpers::calculatePoints(Helpers::removeNumberFormat($product->PrecioFactura), $product->Cantidad);
                }
            }
        }
 
        // Agregamos el totalizado
        $this->order['totalFacturaPedido'] = $this->totalBill; //Asignamos el total del pedido precio factura
        $this->order['totalCatalogoPedido'] = $this->totalCatalog; //Asignamos el total del pedido precio catalogo
        $this->order['calculadoPedido'] = ($this->totalCatalog - $this->totalBill) + $totalGananciaPaquete; //Calculamos la ganancia
        $this->order['totalPuntos'] = $this->totalPoints; //Calculamos el total de puntos
        
        //Agregamos la respuesta
        $this->response = [
            'error' => false,
            'pedidoEncabezado' => $this->order,
            'pedidoDetalle' => $this->products
        ];
        
        Log::info($this->response);
        
        if(!Helpers::isAdviser() && !Helpers::isEmployee()) {
            $user = (new UserController())->detail($request, $document, true);
            
            $this->response['user'] = $user['user'];
            $this->response['show'] = true;
        }
        
        //Llamamos a la vista detalle pedido
        return view('pedidos.orderdetails')->with(['response' => $this->response]);
    }

    /**
     * @description Metodo para guardar y/o modificar el pedido el pedido
     * @param       Request $request
     * @return      View
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Septiembre 18 de 2017
     */
    public function save(Request $request) {
        //Capturamos los datos por el request
        $this->request = $request->all();
        //Variable que contendra los productos 
        $products = []; 
        //Capturamos los productos
        if(isset($this->request['products'])) {
            $products = $this->request['products'];
        } else {
            $this->products = [];
        }
        //Recorremos la lista de los productos
        while ($product = current($products)) {
            //Agregamos el producto a la lista
            $this->products[] = [
                'CodigoPlu' => key($products),
                'quantity' => $product['cantidad']
            ];

            //Continuamos con el siguiente elemento de la lista
            next($products);
        }
        
        // Construimos el objeto que sera enviado al servicio
        $this->order = [
            'ip' => $request->getClientIp(),
            'Mark' => auth()->user()->brand,
            'DocumentNumber' => (!empty($request->get('document'))) ? $request->get('document') : auth()->user()->document,
            'MontoMin' => Helpers::checkMinAmmount($this->request['totalCatalogoPedido']), // 0 = si cumple / 1 = si no cumple 
            'MontoMax' => Helpers::checkMaxAmmount($this->request['totalCatalogoPedido']), // 0 = si cumple / 1 = si no cumple
            'BloqueoStencil' => (Helpers::getSessionObject('asesora')['bloqueoStencil'] == 'True') ? Constants::TRUE : Constants::FALSE, //Bloqueado no cumple / activo si cumple 
            'ListDetails' => $this->products
        ];
        
        //Verificamos que exista el id pedido
        if (!empty($request->get('pedidoId'))) {
            //Agregamos el id al objeto que se envia al servicio
            $this->order['OrderId'] = $request->get('pedidoId');
            //Asignamos verdadero la bandera de actualizar pedido
            $this->isUpdateOrder = Constants::TRUE;
        }
        
        //Verificamos si es un nuevo pedido o una actualización
        if($this->isUpdateOrder) {
            //Llamamos el servicio REST para editar pedido
            $this->request = $this->client->callPut(
                WebServiceParameters::WS_ORDER,
                $this->order
            );
        } else {
            //Llamamos el servicio REST para guardar pedido
            $this->request = $this->client->callPost(
                WebServiceParameters::WS_ORDER,
                $this->order,
                [
                    'ip' => $request->getClientIp()
                ]
            );
        }
        
        //Inicializamos el response con las banderas en false
        $this->response = [
            'error' => Constants::FALSE,
            'warning' => Constants::FALSE
        ];  
        
        // Verificamos que el codigo HTTP sea diferente a 200
        if (!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Retornamos el error al guardar el pedido
            return Array(
                'error' => Constants::TRUE,
                'messages' => [
                    Helpers::getMessage('call.web.service.orders.save.error')
                ]
            );
        }
        
        //Verificamos que la propiedad ejecutado sea false
        if(!$this->request->result->Procesado) {
            return Array(
                'error' => Constants::TRUE,
                'messages' => [
                    $this->request->result->Respuesta
                ]
            );
        }

        //Agregamos el id del pedido
        $this->response['orderId'] = (empty($this->request->result->PedidoId)) ? $this->order['OrderId'] : $this->request->result->PedidoId;
        //Agegamos los mensajes
        $this->response['messages'] = Array(
            Helpers::getMessage('order.save.success'),
            'order' => $this->order
        );
        
        //Retornamos la repuesta
        return $this->response;
    }

    /**
     * @description Metodo para confirmar pedido
     * @param       int $id
     * @param       double $orderTotal
     * @return      JSON
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Octubre 11 de 2017
     */
    public function confirm(Request $request) {
        //Capturamos los datos por el request
        $this->request = $request->all();
        
        //Llamamos el servicio REST para confirmar el pedido
        $this->request = $this->client->callPost(
            WebServiceParameters::WS_ORDER_CONFIRM,   
            [
                'ip' => $request->getClientIp(),
                'Mark' => auth()->user()->brand,
                'DocumentNumber' =>(!empty($request->get('document'))) ? $request->get('document') : auth()->user()->document,
                'OrderId' => $this->request['pedidoId']
            ]
        );
        
        //Seteamos la bandera en el response
        $this->response = [
            'error' => false
        ];
        
        //Verificamos que el codigo HTTP sea diferente a 200
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Agregamos el mensaje de error a la sesión
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('order.confirm.error')]);
        }
        
        //Verificamos si se presento algun error
        if (!$this->request->result->Procesado) {
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, Array($this->request->result->Respuesta));
        } else {
            //Inicializamos la variable messages en null
            $messages = null;
            //Contador para identificar cuando poner negrita a un mensaje
            $i = 1; 
            //Variable para almacenar el mensaje de catalogo
            $catalogMessage = null;
            //Capturamos los mensajes y les realizamos un split por el caracter "."
            //Recorremos el resultado y agrergamos el mensaje a la lista
            foreach(explode('.', $this->request->result->Respuesta) as $message) {
                //Si el contador es igual a 2
                if($i == 1) {
                    //Agregamos negrita al mensaje
                    $messages[] = '<p style="text-align: center; margin: 10px 0; margin-top: -10px;"><strong>' . $message . '</strong></p>';
                } else {
                    //Verificamos que si el plu es diferente a 3 no se mostrara dicho mensaje
                    if($i != 4) {
                        //Agregamos el mensaje sin formato html
                        $messages[] = '<p style="text-align: center; margin: 10px 0;">' . $message . '</p>';
                    } else {
                        //Agregamos el mensaje de pluy catalogo.
                        $catalogMessage = '<p style="text-align: center; margin: 10px 0;">' . $message . '</p>';
                    }
                }
                
                //Incrementamos el contador
                $i ++;
            }
            
            //Verificamos que no tenga saldo pendiente
            if (Helpers::removeNumberFormat(Helpers::getSessionObject('saldos')['SaldoPagar']) > 0) {
                //Verificamos si la asesora tiene fecha limite de pago del pedido
                $end = (empty(Helpers::getSessionObject('FechaPagoPedido'))) ? '' : ' hasta ' . Helpers::dateFormat(Helpers::getSessionObject('FechaPagoPedido'), Constants::DATE_FORMAT_D_M_Y);
                //Seteamos el mensaje del saldo pendiente a pagar
                $messages[] = '<p  style="text-align: center; margin: 10px 0;">' . Helpers::getMessage('pay.pending') . '$' . Helpers::getSessionObject('saldos')['SaldoPagar'] . $end . '</p>';
                $messages[] = $catalogMessage;
                //Agregamos el mensaje al sesión flash
                Helpers::setSessionFlash(Constants::INFO_MESSAGES, $messages);
            } else {
                $messages[] = $catalogMessage;
                //Agregamos el mensaje al sesión flash
                Helpers::setSessionFlash(Constants::INFO_MESSAGES, $messages);  
            }
        }

        //Redireccionamos al historico de pedidos
        return redirect()->route('Inicio')->with(['response' => $this->response]);
    }

    /**
     * @description Metodo para eliminar el pedido
     * @param       int $id
     * @param       boolean $isDashboard
     * @return      view
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Septiembre 19 de 2017
     */
    public function delete(Request $request, $id, $isDashboard = false) {
        //Llamamos el servicio REST para eliminar el pedido
        $this->request = $this->client->callDelete(
            WebServiceParameters::WS_ORDER_DELETE, 
            [
                [
                    'ip' => $request->getClientIp(),  
                    'Mark' => auth()->user()->brand,
                    'DocumentNumber' => (!empty($request->get('document'))) ? $request->get('document') : auth()->user()->document,   
                    'OrderId' => $id,  
                    'MontoMin' => false,
                    'MontoMax' => false,
                    'BloqueoStencil' => false,
                    'ListDetails' => []
                ]
            ], //Parametros que van en el cuerpo formato JSON
            [
               'mark' => auth()->user()->brand 
            ] //Parametros que van en el query string
        );
        
        //Verificamos que el codigo de la peticion HTTP sea diferente a 200
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Agregamos el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, Array(Helpers::getMessage('order.remove.error')));
            //Redireccionamos al historico de pedidos
            return redirect()->route('Inicio')->with(['response' => $this->response]);
        }

        //Verificamos que se haya ejecutado la operación correctamente
        if (!$this->request->result->Procesado) {
            //Agregamos el mensaje de success
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [$this->request->result->Respuesta]);
        } else {
            //Agreamos el mensaje de error
            Helpers::setSessionFlash(Constants::SUCCESS_MESSAGES, [$this->request->result->Respuesta]);
        }

        //Redireccionamos a la vista Inicio / Datos Generales
        return redirect()->route('Inicio')->with(['response' => $this->response]);
    }
    
    /**
     * @description Metodo para validar asesora antes de realizar pedido
     * @return      boolean true | false
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 05 de 2017
     */
    private function validateAdviser($document = null, $json = false) {
        //Consultamos las marcas inscritas
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_USER_BRANDS,
            [
                'id' => (empty($document)) ? auth()->user()->document : $document
            ]
        );
        
        //Verificamos si existe algun error
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Ertornamos el mensaje de error
            return ['error' => true, 'messages' => Helpers::getMessage('error.validate.adviser.new.order')];
        }
        
        //Variable para capturar la marca e inicializamos en null;
        $object = null;
        
        //Recorremos la lista de marcas inscritas de la asesora
        for($i = 0; $i <= count($this->request->result)- 1; $i++) {
            //Verificamos si la marca es igual a la que se autentico
            if(Helpers::isEquals(strtolower($this->request->result[$i]->Marca), strtolower(auth()->user()->brand))) {
                //Seteamos el objeto de la marca a la variable $object
                $object = $this->request->result[$i];
                //Detenemos el bucle
                break;
            }
        }
        
        //Verificamos que la asesora no este bloqueada por Estencil
        //Verificamos si la asesora esta bloqueada por Estencil y Bloqueo Manual
        //Si la asesora tiene bloqueo manual no debe dejar hacer pedido
        //Si la asesora tiene bloqueo manual y bloqueo por Estencil no puede hacer pedido
        //Si la asesorano esta bloqueada manual y tiene bloqueo por Estencil debe permitir hacer pedido
        //Si la no tiene bloqueo manual y no esta bloqueada por Estencil debe permitir hacer pedido
        if($object->Bloqueado == 'True' || $object->BloqueoStencil == 'True' && $object->Bloqueado == 'True') {
            //Si la variable $json es igual a verdadero
            if($json) {
                //Retonamos el mensaje de error en formato JSON
                return [
                    'error' => true,
                    'messages' => Helpers::getMessage('adviser.estencil.error')
                ];
            } else {
                //Seteamos el mensaje en el session flash
                Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('adviser.estencil.error')]);
                //Retornamos error
                return ['error' => true];
            }
        }
        
        //Verificamos si la zona de la asesora es inactiva
        if(Helpers::isEquals($object->ZonaActiva, Constants::FALSE)) {  
            //Si la variable $json es igual a verdadero
            if($json) {
                //Retonamos el mensaje de error en formato JSON
                return [
                    'error' => true,
                    'messages' => Helpers::getMessage('zone.inactive')
                ];
            } else { 
                //Seteamos el mensaje en el session flash
                Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('zone.inactive')]);
                //Retornamos error
                return ['error' => true];
            }
        }
        
        //Retonamos falso para identificar que no hay errores
        return ['error' => false];
    }
    
    /**
     * @description Metodo para obtener los montos
     * @return      boolean
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Octubre 30 de 2017
     */
    private function findAmmount($document = null) {
        //Llamamos el servicio REST de montos
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_AMMOUNT,
            [
                'id' => (empty($document)) ? auth()->user()->document : $document,
                'marca' => auth()->user()->brand
            ]
        );
        
        //Verificamos si existe error
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Agregamso el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('call.web.service.amount.error')]);
            //Retornamos falso para identificar que hay errores
            return false;
        }
        
        //Capturamos los montos y los agregamos al response
        $this->response = Helpers::arrayToObject([
            'MontoMin' => Helpers::removeNumberFormat($this->request->result->MontoMin),
            'MontoMax' => Helpers::removeNumberFormat($this->request->result->MontoMax),
            'ToleranciaMontoMin' => Helpers::removeNumberFormat($this->request->result->ToleranciaMontoMin),
            'ToleranciaCupoMax' => Helpers::removeNumberFormat($this->request->result->ToleranciaCupoMax)
        ]);
        
        //Eliminamos los datos de montos de la sesion
        Session::forget('monto');
        //Agregamos a la sesión los datos de montos
        Session::put('monto', $this->response);
        //Retornamos verdadero para identificar que no hay errores
        return true;
    }

    /**
     * @description Metodo para obtener la lista de pedidos en curso directora zona
     * @param       Request $request
     * @return      array[]
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Enero 12 de 2017
     */
    public function findHistoryOrdersAdministrator($quantity = 0) {
        $this->findAmmount();
        
        $url = WebServiceParameters::WS_FIND_ALL_ORDERS;
        
        if(Helpers::isDirector()) {
            $url = "odata/GetOrderbyDirZona(id='" . auth()->user()->document . "',marca='" . auth()->user()->brand . "',opcion=2)";
        }
        
        if(Helpers::isAdministrator()) {
            $url = "odata/GetOrderToAdmin(marca='" . auth()->user()->brand . "',opcion=2,campanaI='201717',campanaF='201802',codZona='" . auth()->user()->code_zone . "')";
        }
        
        //Llamamos el servicio REST para consultar historico de pedidos
        $this->request = $this->client->callGet(
            $url,
            [
                'id' => auth()->user()->document,
                'marca' => auth()->user()->brand
            ]
        );
        
        //Verificamos si existe error
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK) && $quantity > 0) {
            //Retornamos la respuesta con el mensaje de error
            return [
                'error' => true,
                'messages' => [
                    Helpers::getMessage('order.find.all.error')
                ]
            ];
        }
        
        //Verificamos que el haya error y el llamado no sea via AJAX
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK) && $quantity <= 0) {
            //Setamos el mensaje de error a mostrar en historico pedidos
            Helpers::setSessionFlash(
                Constants::ERROR_MESSAGES, [Helpers::getMessage('order.find.all.error')]);
        }
        
        //Verificamos que el valor del parametro sea mayor a cero
        if ($quantity > 0) {
            //Si es mayor a cero el llamado se realiza por AJAX y retorna un JSON
            //retornamos la respuesta con los pedidos solicitados por el parametro
            
            if(Helpers::isAdviser() || Helpers::isEmployee()) {
                return [
                    'error' => false,
                    'orders' => (!empty($this->request->result)) ? array_slice($this->request->result, 0, $quantity) : []
                ];
            }
            
            return [
                'error' => false,
                'orders' => (!empty($this->request->result->value)) ? array_slice($this->request->result->value, 0, $quantity) : []
            ];
        }
        
        //Retornamos la lista de todos los pedidos retornados por el servicio
        $this->response = Array(
            'error' => false,
            'orders' => (!empty($this->request->result->value)) ? $this->request->result->value : $this->request->result,
            'title' => 'Histórico pedidos'
        );
        
        //Retornamos la vista historicos de pedidos
        return view('pedidos.historyorders')->with(['response' => $this->response]);
    }
    
    
    
    
    
}