<?php

/**
 * Controlador para gestionar los datos generales de la asesora
 */

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Util\CallWebService;
use App\Util\Constants;
use App\Util\Helpers;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use App\Util\Logger;
use App\Util\WebServiceParameters;

class MainController extends Controller {

    private $request;
    private $response;
    private $client;

    public function __construct() {
        //Verificamos que el usuario este autenticado
        $this->middleware('auth');
        //Creamos una nueva instancia de la clase para llamado de servicios REST
        $this->client = new CallWebService();
    }

    /**
     * @description Metodo para obtener los datos de la asesora
     * @param       Request $request
     * @return      View
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Octubre 30 de 2017
     */
    public function index() {
        //Verificamos que exista los datos en la sesión
        if (!Session::get(Constants::SESSION_NAME)) {
            //Buscamos los datos de la asesora en el servicio web
            if (!$this->findPerson()) {
                //Si falla la comunicación con el servicio web retornamos al inicio
                return redirect()->route('Logout', ['credentials' => Helpers::getMessage('call.web.service.error')]);
            }

            //Creamos la sesión
            Helpers::sessionCreate($this->response);
            
            Session::push('showPackageMessage', false);
        } else {
            //Capturamos los datos de la sesion
            $this->response = Helpers::getSessionObject();
        }

        //Verificamos que la asesora no este bloqueada por Estencil
        //Verificamos si la asesora esta bloqueada por Estencil y Bloqueo Manual
        //Si la asesora tiene bloqueo manual no debe dejar hacer pedido
        //Si la asesora tiene bloqueo manual y bloqueo por Estencil no puede hacer pedido
        //Si la asesorano esta bloqueada manual y tiene bloqueo por Estencil debe permitir hacer pedido
        //Si la no tiene bloqueo manual y no esta bloqueada por Estencil debe permitir hacer pedido
        if ($this->response['asesora']['bloqueado'] == 'True' || $this->response['asesora']['bloqueoStencil'] == 'True' && $this->response['asesora']['bloqueado'] == 'True') {
            //Redireccionamos al inicio
            return redirect()->route('Logout', ['credentials' => Helpers::getMessage('auth.user.loock')]);
        }
        
        //Llamamos el metodo para obtener el mensaje de paquetes del ahorro no disponibles
        (new PackageController())->findAllRemove();
        
        
        if(Helpers::isDirector() || Helpers::isAdministrator()) {
            $this->response['currentOrderRoute'] = WebServiceParameters::END_POINT_SERVER . "/odata/GetOrderbyDirZona(id='" . auth()->user()->document . "',marca='" . auth()->user()->brand . "',opcion=3,idOrder=null)";
            $this->response['historyOrderRoute'] = WebServiceParameters::END_POINT_SERVER . "/odata/GetOrderbyDirZona(id='" . auth()->user()->document . "',marca='" . auth()->user()->brand . "',opcion=4,idOrder=null)";
            $this->response['orderDetailRoute'] = WebServiceParameters::END_POINT_SERVER . "/odata/GetOrderbyDirZona(id='" . auth()->user()->document . "',marca='" . auth()->user()->brand . "',opcion=2,idOrder";
        }    
        
        if(Helpers::isAdministrator()) {
            $this->response['currentOrderRoute'] = WebServiceParameters::END_POINT_SERVER . "/odata/GetOrderToAdmin(marca='" . auth()->user()->brand . "',opcion=1,campanaI=null,campanaF=null,codZona=null,mailPlan=null,idOrder=null)";
            $this->response['historyOrderRoute'] = WebServiceParameters::END_POINT_SERVER . "/odata/GetOrderToAdmin(marca='" . auth()->user()->brand . "',opcion=5,campanaI=null,campanaF=null,codZona=null,mailPlan=null,idOrder=null)";
            $this->response['orderDetailRoute'] = WebServiceParameters::END_POINT_SERVER . "/odata/GetOrderToAdmin(marca='" . auth()->user()->brand . "',opcion=2,campanaI=null,campanaF=null,codZona=null,mailPlan=null,idOrder";
        }
        
        //Retornamos la vista de
        return view('pedidos.index')->with(['response' => $this->response]);
    }

    /**
     * @description Metodo para obtener el historico de puntos
     * @return      JSON
     * @uthor       Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Octubre 30 de 2017
     */
    public function findHistoryPoints() {
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_POINTS, [
                'id' => auth()->user()->document,
                'marca' => auth()->user()->brand
            ]
        );

        $this->response = [
            'error' => false,
            'puntos' => $this->request->result
        ];

        return $this->response;
    }

    /**
     * @description Metodo para obtener datos de la directora de zona
     * @return      JSON
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Octubre 30 de 2017
     */
    public function findZoneDitector() {
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_ZONE_DIRECTOR, [
                'id' => auth()->user()->document,
                'marca' => auth()->user()->brand
            ]
        );

        $this->response = [
            'error' => false,
            'directora' => [
                'Nombre' => utf8_encode(ucwords(strtolower(utf8_decode($this->request->result->Nombre)))),
                'Celular' => $this->request->result->Celular,
                'Email' => strtolower($this->request->result->Email),
                'Zona' => Helpers::getSessionObject('datosMarca')['zona']
            ]
        ];

        return $this->response;
    }

    /**
     * @description Metodo para obtener las fechas claves
     * @return      JSON
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Octubre 30 de 2017
     */
    public function findKeyDates() {
        //Verificamos que no exista en la sesion las fechas claves
        if (empty(Session::get('fechas'))) {
            //Obtenemos las fechas claves del servicio REST
            $this->request = $this->client->callGet(
                    WebServiceParameters::WS_KEY_DATES, [
                'id' => auth()->user()->document,
                'marca' => auth()->user()->brand
                    ]
            );

            $changeDates = [];
            $conferenceDates = [];

            //Recorremos la lista de fechas de cambios
            foreach ($this->request->result->FechasCambios as $date) {
                $changeDates[] = [
                    'FechaCambiosDevoluciones' => Helpers::dateFormat($date->Key),
                    'LugarCambios' => $date->Value
                ];
            }

            //Recorremos la lista de fecahs de conferencias
            foreach ($this->request->result->FechasConferencias as $date) {
                $conferenceDates[] = [
                    'FechaConferencia' => Helpers::dateFormat($date->Key),
                    'LugarConferencia' => $date->Value
                ];
            }

            //Capturamos las fechas
            $this->request = [
                'FerchaLimitePedido' => Helpers::dateFormat($this->request->result->FechaSolicitudPedido),
                'FechasCambios' => $changeDates,
                'FechasConferencias' => $conferenceDates,
                'FechaEntregaPedido' => Helpers::dateFormat($this->findSendOrderDate()),
                'FechaPagoPedido' => Helpers::getSessionObject('FechaPagoPedido')
            ];

            //Agregamos las fechas a la sesión
            Session::put('fechas', $this->request);
        } else {
            //Capturamos las fechas de la sesión
            $this->request = Session::get('fechas');
        }

        //Dividimos las fechas para mostrarlas en la sección de fechas claves
        $this->response = Helpers::importantDates($this->request);
        //Retornamos la respuesta
        return $this->response;
    }
    
    /**
     * @description Metodo para visualizar las notificaciones al momento de loguearse
     * @return      View
     * @author      Felipe.Echeverri <felipe.echeverri@ingeneo.com.co>
     * @date        Diciembre 28 de 2017
     */
    public function showNotifications() {
        return view("layouts.metronic.includes.notificaciones")->render();
    }

    /**
     * @description Metodo para visualizar el video
     * @return      View
     * @author      Felipe.Echeverri <felipe.echeverri@ingeneo.com.co>
     * @date        Octubre 04 de 2017
     */
    public function showVideo() {
        //Carga la vista mostrar video
        return view('pedidos.showvideo');
    }

    /**
     * @description Metodo para obtener los datos de la asesora
     * @return      Boolean
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Septiembre 2 de 2017
     */
    private function findPerson() {
        //Llamamos el servicio REST de Personas
        $this->request = $this->client->callGet(
                WebServiceParameters::WS_PERSON, [
            'id' => auth()->user()->document,
            'marca' => auth()->user()->brand
                ]
        );

        //Verificamos el estado del request sea diferente de 200 OK
        if (!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            return false;
        }

        $this->response = Array(
            'asesora' => [
                'documento' => $this->request->result->CodigoDocumento,
                'nombre' => $this->request->result->Nombre,
                'apellido' => $this->request->result->Apellidos,
                'correo' => $this->request->result->Email,
                'telefono' => $this->request->result->Telefono,
                'departamento' => $this->request->result->Departamento,
                'municipio' => $this->request->result->Municipio,
                'direccion' => $this->request->result->Direccion,
                'barrio' => $this->request->result->Barrio,
                'bloqueado' => $this->request->result->Bloqueado,
                'bloqueoStencil' => $this->request->result->BloqueoStencil,
                'foto' => Helpers::findProfilePhoto()
            ],
            'saldos' => [
                'SaldoFavor' => (Helpers::removeNumberFormat($this->request->result->Saldo) <= 0) ? Helpers::negativeToPositive($this->request->result->Saldo) : 0,
                'SaldoPagar' => (Helpers::removeNumberFormat($this->request->result->Saldo) > 0) ? $this->request->result->Saldo : 0
            ],
            'FechaPagoPedido' => Helpers::dateFormat($this->request->result->FechaVencimiento),
            'datosMarca' => [
                'marcaActual' => $this->request->result->Marca,
                'codigoPerfil' => auth()->user()->profile,
                'nombrePerfil' => auth()->user()->profileName,
                'zona' => $this->request->result->CodigoZona,
                'campana' => $this->request->result->CampanaActual,
                'catalogo' => $this->findCatalog()
            ],
            'cupos' => $this->findQuotaCredit(),
            'monto' => $this->findAmmount(),
            'marcas' => $this->brands()
        );

        return true;
    }

    /**
     * @description Metodo para obtener las marcas de la asesora
     * @return      array | string
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 04 de 2017
     */
    private function brands() {
        //Consultamos las marcas inscritas
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_USER_BRANDS, 
            [
                'id' => auth()->user()->document
            ]
        );

        if (!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            return [];
        }

        $brands = null;

        //Recorremos la lista de 
        for ($i = 0; $i <= count($this->request->result) - 1; $i++) {
            if (!Helpers::isEquals($this->request->result[$i]->Bloqueado, Constants::TRUE)) {
                $brands[] = strtolower($this->request->result[$i]->Marca);
            }
        }

        return $brands;
    }

    /**
     * @description Metodo para obtener la url del catalogo
     * @return      string
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Octubre 30 de 2016
     */
    private function findCatalog() {
        //Llamamos el servicio REST Catalogo
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_CATALOG, 
            [
                'id' => auth()->user()->document,
                'marca' => auth()->user()->brand
            ]
        );

        //Retornamos la URL del catalogo
        return (!empty($this->request->result)) ? $this->request->result->UrlCatalogo : '';
    }

    /**
     * @description Metodo para obtener el cupo credito
     * @return      Json
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Octubre 30 de 2017
     */
    private function findQuotaCredit() {
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_QUOTA_CREDIT, 
            [
                'id' => auth()->user()->document,
                'marca' => auth()->user()->brand
            ]
        );
        //Retornamos la respuesta
        return $this->request->result;
    }

    /**
     * @description Metodo para obtener la fecha envio pedido
     * @return      Date
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Octubre 30 de 2017
     */
    private function findSendOrderDate() {
        //Consultamos el servicio REST de fecha entrega pedido
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_KEY_DATES, 
            [
                'idADVISER' => auth()->user()->document,
                'marca' => auth()->user()->brand,
                'condicion' => 'true'
            ]
        );
        //Retornamos la respuesta
        return (!empty($this->request->result)) ? $this->request->result->FechaEntrega : null;
    }

    /**
     * @description Metodo para obtener los montos
     * @return      boolean true | false
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Octubre 30 de 2017
     */
    private function findAmmount() {
        //Llamamos el servicio REST de montos
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_AMMOUNT, 
            [
                'id' => auth()->user()->document,
                'marca' => auth()->user()->brand
            ]
        );

        //Verificamos si existe error
        if (!Helpers::isEquals($this->request->code, Constants::HTTP_OK) || empty($this->request->result)) {
            //Agregamso el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('call.web.service.amount.error')]);
            //Retornamos falso para identificar que hay errores
        }

        //Verificamos que sea diferente de vacio el resultado
        if(!empty($this->request->result)) {
            //Capturamos los montos y los agregamos al response
            $this->response = Helpers::arrayToObject([
                'MontoMin' => Helpers::removeNumberFormat($this->request->result->MontoMin),
                'MontoMax' => Helpers::removeNumberFormat($this->request->result->MontoMax),
                'ToleranciaMontoMin' => Helpers::removeNumberFormat($this->request->result->ToleranciaMontoMin),
                'ToleranciaCupoMax' => Helpers::removeNumberFormat($this->request->result->ToleranciaCupoMax)
            ]);
        }
        //Eliminamos los datos de montos de la sesion
        Session::forget('monto');
        //Agregamos a la sesión los datos de montos
        Session::put('monto', $this->response);
        //Retornamos la respuesta
        return $this->response;
    }

    /**
     * @description Metodo para cargar la foto de perfil de la asesora
     * @param       Request $request
     * @return      Json
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 04 de 2017
     */
    public function uploadProfile(Request $request) {
        //Capturamos el fichero y lo validamos
        $fields = $request->validate([
            'profilePhoto' => 'required|image|mimes:jpg,jpeg|max:2048'
        ]);

        //Verificamos que exista un fieche cargado
        if ($request->hasFile('profilePhoto')) {
            //Capturamos el fichero
            $image = $request->file('profilePhoto');
            $img = $request->file('profilePhoto');
            //Generamos el nombre del fichero
            $name = md5(auth()->user()->document) . '.' . strtolower($image->getClientOriginalExtension());
            //Movemos el fichero
            $image->move(public_path(Constants::PROFILE_PATH), $name);

            //Incializamos la configuración del S3 Amazon 
            $s3 = S3Client::factory(Constants::S3_CONFIG);

            try {
                //Eliminamos el fichero del bucket   
                $s3->deleteObject([
                    'Bucket' => Constants::S3_PRIVATE_BUCKET,
                    'Key' => Constants::S3_PROFILE_PATH . $name
                ]);

                //Cargamos el fichero al S3 de Amazon
                $s3->putObject([
                    'Bucket' => Constants::S3_PRIVATE_BUCKET,
                    'Key' => Constants::S3_PROFILE_PATH . $name,
                    'SourceFile' => realpath(public_path(Constants::PROFILE_PATH) . $name),
                    'ACL' => Constants::S3_PUBLIC_READ
                ]);
                //Eliminamos el fichero de la carpeta temporal
                unlink(public_path(Constants::PROFILE_PATH . $name));
                //Redireccionamos al dash board
                return redirect()->route('Inicio');
            } catch (S3Exception $ex) {
                Logger::errorMySQL($ex->getMessage());
                Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('upload.profile.error')]);
                return redirect()->route('Inicio');
            }
        }
    }

}
