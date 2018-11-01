<?php

/**
 * Controlador para realizar el cambio de la contraseña
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Util\CallWebService;
use Illuminate\Http\Request;
use App\Util\WebServiceParameters;
use App\Util\Helpers;
use App\Util\Constants;
use App\Dao\UserDao;

class ResetPasswordController extends Controller {

    private $request;
    private $response;
    
    private $client;
    
    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct() {
        //Verificamos que el usuario este autenticado
        $this->middleware('auth');
        //Creamos una nueva instancia de la clase para llamado de servicios REST
        $this->client = new CallWebService();
    }
    
    /**
     * @description Metodo para cargar la vista de cambio de clave
     * @param       Request $request
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Enero 10 de 2018
     */
    public function reset(Request $request) {
        //Construimos el response
        $this->response = [
            'checkPassword' => base64_decode(auth()->user()->external_password)
        ];
        //Llamamos la vista para editar la contraseña
        return view('configuration.passwordupdate')->with(['response' => $this->response]);
    }
    
    /**
     * @description Metodo para cambiar la contraseña la asesora
     * @param       Request $request
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Enero 10 de 2018
     */
    public function resetByAdviser(Request $request) {
        //Validamos los datos del formulario
        $validation = $request->validate([
            'currentpassword' => 'required|max:30',
            'password' => 'required|max:30',
            'repeatpassword' => 'required|max:30|same:password',
        ]);
        
        //Consumimos el servicio REST cambiar clave
        $this->request = $this->client->callPut(
            'http://10.244.9.70:9696/' . WebServiceParameters::WS_UPDATE_PASSWORD_ADMINISTRATOR,
            [
                'Usuario' => auth()->user()->external_user,
                'Clave' => base64_encode($request->get('currentpassword')),  
                'ClaveNueva' => base64_encode($request->get('password')),
                'ClaveConfirmacion' => base64_encode($request->get('repeatpassword'))
            ],
            [
                'userId' => ''
            ]
        );
        
        //Verificamos si se presenta algún error
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Enviamos el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, ['Error al cambiar tú contraseña, intenta de nuevo.']);
        } else {
            //Enviamos le mensaje de success
            Helpers::setSessionFlash(Constants::SUCCESS_MESSAGES, ['Se cambio tú contraseña.']);
            //Modificamos la contraseña externa para poder validar el campo
            (new UserDao())->update(auth()->user()->document, ['external_password' => base64_encode($request->get('password'))]);
        }
        
        //Redireccionamos a la vista de cambiar clave
        return redirect()->route('CambiarClave');
    }
    
    /**
     * @description Metodo para realizar el cambio de contraseña por parte del adminitrador
     * @param       Request $request
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Enero 05 de 2018
     */
    public function resetByAdministrator(Request $request) {
        //Validamos los datos del formulario
        $validation = $request->validate([
            'user' => 'required|max:30',
            'password' => 'required|max:30',
            'repeatpassword' => 'required|max:30|same:password',
            'profile' => 'required'
        ]);
        //Consumimos el servicio REST cambiar clave
        $this->request = $this->client->callPut(
            'http://10.244.9.70:9696/' . WebServiceParameters::WS_UPDATE_PASSWORD_ADMINISTRATOR, 
            [   
                'Usuario' => $request->get('user'),
                'Clave' => null,
                'ClaveNueva' => base64_encode($request->get('password')),
                'ClaveConfirmacion' => base64_encode($request->get('repeatpassword'))
            ],
            [
                'userId' => auth()->user()->document
            ]
        );
        
        //Verificamos si se presenta algún error
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Enviamos el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, ['Error al cambiar la contraseña de la asesora, intenta de nuevo.']);
        } else {
            //Enviamos le mensaje de success
            Helpers::setSessionFlash(Constants::SUCCESS_MESSAGES, ['Se cambio la contraseña de la asesora.']);
        }
        
        //Redireccionamos al detalle del usuario
        return redirect()->route('DetalleUsuario', ['document' => $request->get('user')]);
    }
}
