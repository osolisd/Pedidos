<?php
/**
 * Controlador para realizar la gestión de las alertas / notificaciones
 */
namespace App\Http\Controllers\Pedidos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Util\CallWebService;
use App\Util\Helpers;
use App\Util\Constants;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use App\Util\WebServiceParameters;
use Illuminate\Support\Facades\Log;

class AlertController extends Controller {
    
    private $request;
    private $response;
    private $alerts;
    private $alert;
    private $clasifications = [];
    private $stencilstatus = [];
    private $zones = [];
    private $mailplains = [];
    private $imageUrl;
    private $client;
    
    /**
     * Constructor de la clase  
     */
    public function __construct() {
        //Verificamos que el usuario este autenticado
        $this->middleware('auth');
        //Creamos una nueva instancia de la clase para llamado de servicios REST
        $this->client = new CallWebService();
    }  
    
    /**
     * @description Metodo para obtener la lista de alertas creadas
     * @param       Request $request
     * @return      \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 26 de 2017
     */
    public function findAll(Request $request) {
        //Consumimos el servicio REST para listar las alertas
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_ALERTS, 
            [
                'marca' => auth()->user()->brand
            ]
        );
        
        //Verificamos is existe algun error
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Seteamos el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('alert.list.error')]);
            //Redireccionamos al inicio
            return redirect('Inicio');
        }
        
        //Construimos la respuesta
        $this->response = [
            'error' => false,
            'alerts' => $this->request->result
        ];
        
        //Retornamos la vista de alertas
        return view('configuration.alerts')->with(['response' => $this->response]);
    }
    
    /**
     * @description Metodo para obtener la lista de alertas por asesora
     * @param       Request $request
     * @return      Json
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 26 de 2017
     */
    public function findAllByAdviser() {
        //Consumimos el servico REST para obtener las alertas de la asesora
        $this->request = $this->client->callGet( 
            WebServiceParameters::WS_ALERTS, 
            [
                'codZone' => auth()->user()->code_zone,
                'adviserNew' => auth()->user()->is_new,
                'stateStencil' => auth()->user()->stencil_status,
                'clasXVlr' => auth()->user()->clasification,
                'mailPlan' => auth()->user()->mail_plain,
                'marca' => auth()->user()->brand
            ]
        );
        
        //Verificamos si existe algun tipo de error
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Construimos la respuesta de error
            return [
                'error' => true,
                'message' => Helpers::getMessage('alert.list.adviser.error')
            ];
        }
        
        //Construimos el response
        $this->response = [
            'error' => false,
            'alerts' => $this->request->result
        ];
        
        //Retornamos el response
        return $this->response;
    }
    
    /**
     * @description Metodo para obtener la alerta principal del dash board y del nuevo pedido
     * @param       string | A = 'Dashboard' | 'NP' = Nuevo Pedido
     * @return      boolean[]|string[]|boolean[]|string[]|NULL[]
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Enero 03 de 2018
     */

    public function findMainAlert($ubication) {
        //Obtenemos la lista de alertas que aplicacn a la asesora
        $this->alerts = self::findAllByAdviser();
        
        //Verificamos si existe algun error
        if($this->alerts['error']) {
            //Retornamos el mensaje de error
            return $this->alerts;
        }
        
        //Verificamos que la variable alerta sea diferente de vacio
        if(!empty($this->alerts['alerts']) && auth()->user()->active_zone == 1) {
            //Recorremos la lista de alertas
            foreach ($this->alerts['alerts'] as $alert) {  
                //Verificamos si la alerta es principal = 1
                //Verificamos que la fecha fin de la alerta sea mayor o igual a la fecha actual
                //Verificamos que la parte donde se mostrara la alerta sea en el dashboard = 'A'
                if($alert->Detalle->AsesorasNuevas == auth()->user()->is_new || $alert->Detalle->AsesorasNuevas == 2) {
                    if($alert->AlertaPrincipal == 1 && Helpers::dateFormat($alert->FechaInicio, Constants::DATE_FORMAT_D_M_Y) >= date(Constants::DATE_FORMAT_D_M_Y) && $alert->Detalle->Ubicacion == $ubication) {
                        //Seteamos la alerta a la variable
                        $this->alert = $alert;
                        //Detenemos el ciclo
                        break;
                    }
                }
            }
        }
        
        //Retornamos el resultado
        return [
            'error' => false,
            'alert' =>$this->alert
        ];
    }
    
    /**
     * @description Metodo para obtener las alertas secundarias de las asesoras
     * @return      array|boolean[]|string[]|NULL[]|boolean[]|boolean[][]|string[][]|NULL[][]
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Enero 05 de 2018
     */
    public function findAllAlertSecondary() {
        //Obtenemos la lista de alertas que aplican a la asesora
        $this->request = self::findAllByAdviser();
        //Verificamos si existe algun error
        if($this->request['error']) {
            //Retornamos el mensaje de error
            return $this->request;
        }
        
        Log::info('Alerts!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
        Log::info($this->request);
        Log::info('End Alerts !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');  
        
        //Verificamos que la variable alerta sea diferente de vacio
        if(!empty($this->request['alerts'])) {
            //Recorremos la lista de alertas
            foreach ($this->request['alerts'] as $alert) {
                //Verificamos si la alerta es secundaria = 0
                //Verificamos que la fecha fin de la alerta sea mayor o igual a la fecha actual
                if($alert->Detalle->AsesorasNuevas == auth()->user()->is_new || $alert->Detalle->AsesorasNuevas == 2) {
                    if($alert->AlertaPrincipal == 0 && Helpers::dateFormat($alert->FechaInicio, Constants::DATE_FORMAT_D_M_Y) >= date(Constants::DATE_FORMAT_D_M_Y)) {
                        //Seteamos la alerta a la variable
                        $this->alerts[] = $alert;
                    }
                }
            }
        } 
        
        //Retornamos el resultado
        return [
            'error' => false,
            'alerts' =>$this->alerts
        ];
    }
    
    /**
     * @description Metodo para crear una alerta
     * @param       Request $request
     * @return      \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 26 de 2017
     */
    public function create(Request $request) {
        //Consultamos el servicio de clasificación por valor
        if(!self::findAllClasifications()) {
            //Redireccionamos al inicio
            return redirect('Inicio');
        }
        
        //Consultamos el servicio de estado estencil
        if(!self::findAllStencilStatus()) {
            //Redireccionamos al inicio
            return redirect('Inicio');
        }
        
        //Consultamos el servicio de zonas
        if(!self::findAllZones($request)) {
            //Redireccionamos al inicio
            return redirect('Inicio');
        }
        
        //Consultamos el servicio de mail plan
        if(!self::findAllMainPlain()) {
            //Redireccionamos al inicio
            return redirect('Inicio');
        }
        
        //Construimos la respuesta
        $this->response = [
            'error' => false,
            'stencilStatus' => $this->stencilstatus,
            'clasifications' => $this->clasifications,
            'zones' => $this->zones,
            'mailpains' => $this->mailplains  
        ];
        
        //Retornamos la vista y pasamoas los datos
        return view('configuration.createalert')->with(['response' => $this->response]);
    }
    
    /**
     * @description Metodo para guardar la alerta
     * @param       Request $request
     * @return      \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 26 de 2017
     */
    public function save(Request $request) {
        //Validamos el formulario
        $validate = $request->validate([
            'principal' => 'required|numeric|max:1',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,gif|max:2048',
            'titulo' => 'required|max:30',
            'feini' => 'required|date',
            'fefin' => 'required|date',
            'orientacion' => 'required|max:2',
            'descripcion' => 'nullable|max:8000',
            'ubicacion' => 'required|max:2',
            'newAdviser' => 'required',
            'my_multi_select2' => 'required'
        ]);
        
        //Recorremos el objeto del multi select
        foreach($request->get('my_multi_select2') as $key) {
            //Si el prefijo S / Estado Estencil 
            if(substr($key, 0, 1) == 'S') {
                //Dividimos la cadena por | y obtenemos los datos de la poción 1 eje: S|2 = 2
                $this->stencilstatus[] = explode('|', $key)[1];
            }
            
            //Si el prefijo es C / Clasificación por valor
            if(substr($key, 0, 1) == 'C') {
                //Dividimos la cadena por | y obtenemos los datos de la poción 1 eje: C|2 = 2
                $this->clasifications[] = explode('|', $key)[1];
            }
            
            //Si el prefijo es Z / Zona
            if(substr($key, 0, 1) == 'Z') {
                //Dividimos la cadena por | y obtenemos los datos de la poción 1 eje: Z|2 = 2
                $this->zones[] = explode('|', $key)[1];
            }
            
            //Si el prefijo es M / Mail Plan
            if(substr($key, 0, 1) == 'M') {
                //Dividimos la cadena por | y obtenemos los datos de la poción 1 eje: M|AB = AB
                $this->mailplains[] = explode('|', $key)[1];
            }
        }
        
        //Verificamos si la imagen fue cargada con exito
        if(!self::uploadAlertImage($request)) {
            //Agregamos el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('upload.image.alert.error')]);
            //Redireccionamos al inicio
            return redirect('Alertas');  
        }
        
        //Consumimos el servicio REST para guardar la alerta
        $this->request = $this->client->callPost(
            WebServiceParameters::WS_ALERTS, 
            [
                'Titulo' => $request->get('titulo'),
                'AlertaPrincipal' => (int)$request->get('principal'),
                'FechaInicio' => $request->get('feini'),
                'FechaFin' => $request->get('fefin'),
                'Detalle' => [
                    'ImagenId' => $this->imageUrl,
                    'Orientacion' => $request->get('orientacion'),
                    'ContenidoMensaje' => (empty($request->get('descripcion'))) ? '' : $request->get('descripcion') ,
                    'AsesorasNuevas' => (int)$request->get('newAdviser'),
                    'Ubicacion' => $request->get('ubicacion'),
                    'ListaEstadoEstencilIds' => $this->stencilstatus,
                    'ListaMailPlanIds' => $this->mailplains,
                    'ListaClasificacionValorIds' => $this->clasifications,
                    'ListaCodigoZonas' => $this->zones,
                    'HiperVinculo' => $request->get('url')
                ]
            ],
            [
                'marca' => auth()->user()->brand
            ]
        );
        
        //Verificamos que no hayan errores
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Agregamos el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('alert.save.error')]);
        }
        
        //Verificamos que el resultado no sea vacio y que la variable procesado sea false
        if(!empty($this->request->result) && !$this->request->result->Procesado) {
            //Agregamos el mensaje de error   
            Helpers::setSessionFlash(Constants::WARNING_MESSAGES, [$this->request->result->Respuesta]);
        } 
        
        if(!empty($this->request->result) && $this->request->result->Procesado) {
            //Agregamos el mensaje de success
            Helpers::setSessionFlash(Constants::SUCCESS_MESSAGES, [Helpers::getMessage('alert.save.success')]);
        }
        
        //Redireccionamos al inicio
        return redirect('Alertas');
    }
    
    /**
     * @description Metodo para obtener la alerta por id
     * @param       Request $request
     * @return      \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 26 de 2017
     */
    public function findById(Request $request, $id) {
        //Consumimos el servicio REST para obtener el detalle de la alerta por id
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_ALERTS,
            [
                'marca' => auth()->user()->brand,
                'id' => $id
            ]
        );
        
        //Verificamos si hay algun error
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {  
            //Agregamos el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('alert.find.error')]);
            //Redireccionamos al inicio
            return redirect('Alertas');
        }
        
        //Seteamos el resultado a la variable alerta
        $this->alert = $this->request->result;
        
        //Consultamos el servicio de clasificación por valor
        if(!self::findAllClasifications()) {
            //Redireccionamos al inicio
            return redirect('Inicio');
        }
        
        //Consultamos el servicio de estado estencil
        if(!self::findAllStencilStatus()) {
            //Redireccionamos al inicio
            return redirect('Inicio');
        }
        
        //Consultamos el servicio de zonas
        if(!self::findAllZones($request)) {
            //Redireccionamos al inicio
            return redirect('Inicio');
        }
        
        //Consultamos el servicio de mail plan
        if(!self::findAllMainPlain()) {
            //Redireccionamos al inicio
            return redirect('Inicio');
        }
        
        //Construimos la respuesta
        $this->response = [
            'error' => false,
            'stencilStatus' => $this->stencilstatus,
            'clasifications' => $this->clasifications,
            'zones' => $this->zones,
            'mailpains' => $this->mailplains,
            'alert' => $this->alert
        ];
        
        //Retornamos la vista con los datos
        return view('configuration.updatealert')->with(['response' => $this->response]);
    }
    
    /**
     * @description Metodo para editar la alerta
     * @param       Request $request
     * @return      \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 26 de 2017
     */
    public function update(Request $request) {
        //Validamos el formulario
        $validate = $request->validate([
            'principal' => 'required|numeric|max:1',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,gif|max:2048',
            'titulo' => 'required|max:30',
            'feini' => 'required|date',
            'fefin' => 'required|date',
            'orientacion' => 'required|max:2',
            'descripcion' => 'nullable|max:8000',
            'ubicacion' => 'required|max:2',
            'newAdviser' => 'required',
            'my_multi_select2' => 'required'
        ]);
        
        //Recorremos el objeto del multi select
        foreach($request->get('my_multi_select2') as $key) {
            //Si el prefijo S / Estado Estencil
            if(substr($key, 0, 1) == 'S') {
                //Dividimos la cadena por | y obtenemos los datos de la poción 1 eje: S|2 = 2
                $this->stencilstatus[] = explode('|', $key)[1];
            }
            
            //Si el prefijo es C / Clasificación por valor
            if(substr($key, 0, 1) == 'C') {
                //Dividimos la cadena por | y obtenemos los datos de la poción 1 eje: C|2 = 2
                $this->clasifications[] = explode('|', $key)[1];
            }
            
            //Si el prefijo es Z / Zona
            if(substr($key, 0, 1) == 'Z') {
                //Dividimos la cadena por | y obtenemos los datos de la poción 1 eje: Z|2 = 2
                $this->zones[] = explode('|', $key)[1];
            }
            
            //Si el prefijo es M / Mail Plan
            if(substr($key, 0, 1) == 'M') {
                //Dividimos la cadena por | y obtenemos los datos de la poción 1 eje: M|AB = AB
                $this->mailplains[] = explode('|', $key)[1];
            }
        }
        
        //Verificamos si la imagen fue cargada con exito
        if(!self::uploadAlertImage($request)) {
            //Agregamos el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('upload.image.alert.update.error')]);
            //Redireccionamos al inicio
            return redirect('Alertas');
        }
        
        //Consumimos el servicio REST para guardar la alerta
        $this->request = $this->client->callPut(
            WebServiceParameters::WS_ALERTS,
            [
                'AlertaId' => $request->get('id'),  
                'Titulo' => $request->get('titulo'),
                'AlertaPrincipal' => $request->get('principal'),
                'FechaInicio' => $request->get('feini'),
                'FechaFin' => $request->get('fefin'),
                'Detalle' => [
                    'ImagenId' => $this->imageUrl,
                    'Orientacion' => $request->get('orientacion'),
                    'ContenidoMensaje' => (empty($request->get('descripcion'))) ? '' : $request->get('descripcion') ,
                    'AsesorasNuevas' => $request->get('newAdviser'),
                    'Ubicacion' => $request->get('ubicacion'),
                    'ListaEstadoEstencilIds' => $this->stencilstatus,
                    'ListaMailPlanIds' => $this->mailplains,
                    'ListaClasificacionValorIds' => $this->clasifications,
                    'ListaCodigoZonas' => $this->zones,
                    'HiperVinculo' => $request->get('url')
                ] 
            ],
            [
                'marca' => auth()->user()->brand
            ]
        );
        
        //Verificamos que no hayan errores
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Agregamos el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('alert.save.error')]);
        }
        
        //Verificamos que el resultado no sea vacio y que la variable procesado sea false
        if(!empty($this->request->result) && !$this->request->result->Procesado) {
            //Agregamos el mensaje de error
            Helpers::setSessionFlash(Constants::WARNING_MESSAGES, [$this->request->result->Respuesta]);
        }
        
        if(!empty($this->request->result) && $this->request->result->Procesado) {
            //Agregamos el mensaje de success
            Helpers::setSessionFlash(Constants::SUCCESS_MESSAGES, [Helpers::getMessage('alert.save.success')]);
        }
        
        //Redireccionamos al inicio
        return redirect('Alertas');
    }
    
    /**
     * @description Metodo para eliminar las alertas
     * @param       Request $request
     * @return      \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 26 de 2017    
     */
    public function remove(Request $request, $id) {
        $listId = [
            'AlertaId' => $id
        ];
        
        //Consumimos el servicio REST para eliminar las alertas
        $this->request = $this->client->callDelete(
            WebServiceParameters::WS_ALERTS,
            [
                $listId
            ],
            [
                'Mark' => auth()->user()->brand
            ]
        );
        
        //Verificamos si se presento un error
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Agregamos el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('alert.remove.error')]);
            //Redireccionamos al inicio
            return redirect('Alertas');
        }
        
        //Agregamos el mensaje de de success
        Helpers::setSessionFlash(Constants::SUCCESS_MESSAGES, [Helpers::getMessage('alert.remove.success')]);
        //Redireccionamos al inicio
        return redirect('Alertas');
    }
    
    /**
     * @description Metodo para cargar la imagen de la alerta al S3 de Amazon
     * @param       Request $request
     * @return      boolean
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 26 de 2017
     */
    private function uploadAlertImage(Request $request) {
        try {
            if($request->hasFile('imagen')) {
                //Capturamos el fichero
                $image = $request->file('imagen');
                //Generamos el nombre del fichero
                $name = md5(uniqid()) . '.' . strtolower($image->getClientOriginalExtension());    
                //Movemos el fichero
                $image->move(public_path(Constants::ALERT_PATH), $name);
                
                //Incializamos la configuración del S3 Amazon
                $s3 = S3Client::factory(Constants::S3_CONFIG);
                //Construimos el nombre del bucket
                $bucket = Constants::S3_PRIVATE_BUCKET;
                
                //Cargamos el fichero al S3 de Amazon
                $s3->putObject([
                    'Bucket' => $bucket,
                    'Key' => Constants::S3_ALERT_PATH . $name,
                    'SourceFile' => realpath(public_path(Constants::ALERT_PATH) . $name),   
                    'ACL' => Constants::S3_PUBLIC_READ
                ]);
                
                //Eliminamos el fichero de la carpeta temporal
                unlink(public_path(Constants::ALERT_PATH . $name));
                //Seteamos la ruta de la imagen
                $this->imageUrl = 'http://' . $bucket . '/' . Constants::S3_ALERT_PATH . $name;
            } else {
                if(!empty($request->get('oldAlertImage'))){
                    $this->imageUrl = $request->get('oldAlertImage');
                }
            }
        } catch (S3Exception $ex) {  
            Log::error($ex);
            return false;
        }
        
        return true;
    }
    
    /**
     * @description Metodo para obtener la lista de mail plan
     * @return      boolean
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 29 de 2017
     */
    public function findAllMainPlain($external = false) {
        //Consultamos el servicio REST para obtener los mail plan
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_MAIL_PLAN,
            [
                'marca' => auth()->user()->brand
            ]
        );
        
        //Verificamos si algun error
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Agregamos el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('mail.plan.list.error')]);
            
            if($external) {
                return [];
            }
            
            //Retornamos falso para identificar que se presento un error
            return false;
        }
        
        //Asignamos el mail plan a la variable
        $this->mailplains = $this->request->result;
        //Redireccionamos verdadero para identificar que no se presento errores
        if($external) {
            return $this->request->result;
        }
        
        return true;
    }
    
    /**
     * @description Metodo para obtener la lista de zonas
     * @return      boolean
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 29 de 2017
     */
    public function findAllZones(Request $request, $external = false) {
        //Consultamos el servicio REST para obtener las zonas
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_ZONES,
            [
                'marca' => auth()->user()->brand
            ]
        );
        
        //Verificamos si algun error
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Agregamos el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('zone.list.error')]);
            
            if($external) {
                return [];
            }
            
            //Retornamos la falso para identificar que hay errores
            return false;
        }
        
        //Asignamos la zonas a la variable
        $this->zones = $this->request->result;
        
        if($external && empty($request->get('mailPlan'))) {
            return $this->request->result;
        }
        
        if($external && !empty($request->get('mailPlan'))) {
            $zones = [];  
            foreach ($this->request->result as $object) {
                Log::info('------' . $object->mailPlan . ' >>>>>> ' . $request->get('mailPlan'));
                if(Helpers::isEquals($object->mailPlan, $request->get('mailPlan'))) {
                    $zones[] = $object;
                }
            }
            
            return $zones;  
        }
        
        //Retornamos verdadero para identificar que no se presentaron errores
        return true;
    }
    
    /**
     * @description Metodo para obtener la lista de estado estencil
     * @return      boolean
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 29 de 2017
     */
    public function findAllStencilStatus($external = false) {
        //Consultamos le servicio REST para consultar la lista de estado estencil
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_STENCIL_STATUS,
            [
                'marca' => auth()->user()->brand
            ]
        );
        
        //Verificamos si algun error
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Agregamos el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('status.stencil.list.error')]);
            
            if($external) {
                return [];
            }
            
            //Retornamos falso para identificar que se presento algún error
            return false;
        }
        
        //Seteamos el estado estencil a la variable
        $this->stencilstatus = $this->request->result;
        //Retornamos verdadero para identifcar que no hay errores
        if(!$external) {
            return true;
        }
        
        return $this->stencilstatus;
    }
    
    /**
     * @description Metodo para obtener la lista de clasificaciones por valor
     * @return      boolean
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 29 de 2017
     */
    public function findAllClasifications($external = false) {
        //Consumimos el servicio REST para obtener las clasificaciones por valor
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_VALUE_CLASIFICATION,
            [
                'marca' => auth()->user()->brand
            ]
        );
        
        //Verificamos si hay algun error
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Agregamos el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('clasification.list.error')]);
            
            if($external) {
                return [];
            }
            
            //Retornamos falso para identificar que se presento un error
            return false;
        }
        
        //Asignamos variable para guardar las clasificaicones por valor
        $this->clasifications = $this->request->result;
        //Retornamos true para identificar que no hay errores
        if(!$external) {
            return true;
        }
        
        return $this->clasifications;
    }
    
    
    public function findAllCampaings($external = false) {   
        $zone = (!Helpers::isAdministrator()) ? auth()->user()->code_zone : 'all';         
        //Consumimos el servicio REST para obtener las clasificaciones por valor
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_CAMPAING_YEAR,  
            [  
                'marca' => auth()->user()->brand,   
                'codZona' => (!Helpers::isAdministrator()) ? auth()->user()->code_zone : ''
            ]  
        );
        
        //Verificamos si hay algun error
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Agregamos el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, ['Error al obtener la lista de campañas.']);
            //Retornamos falso para identificar que se presento un error
            return [];
        }
        
        //Asignamos variable para guardar las clasificaicones por valor
        return $this->request->result;
    }
    
    public function findAllDivisions($external = false) {
        //Consumimos el servicio REST para obtener las clasificaciones por valor
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_DIVISION,
            [
                'marca' => auth()->user()->brand
            ]
        );
        
        //Verificamos si hay algun error
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Agregamos el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('clasification.list.error')]);
            //Retornamos falso para identificar que se presento un error
            return [];
        }
        
        //Asignamos variable para guardar las clasificaicones por valor
        return $this->request->result;
    }
}
