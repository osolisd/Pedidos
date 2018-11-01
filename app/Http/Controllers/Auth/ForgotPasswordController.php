<?php

/**
 * Controlador para recuperar clave del usuario
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Util\CallWebService;
use App\Util\WebServiceParameters;
use App\Util\Helpers;
use App\Util\Constants;

class ForgotPasswordController extends Controller {
    
    private $request;
    private $response;

    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct() {
        //Revisamos que solo puedan acceder usuarios no autenticados
        $this->middleware('guest');
    }
    
    public function checkAdviserDocument(Request $request) {
        if(empty($request->get('document'))) {
            return [
                'error' => true,
                'messages' => 'Ingresa tú número de documento.'
            ];
        }
        
        $this->request = (new CallWebService())->callGet(
            WebServiceParameters::WS_PERSON, 
            [
                'id' => $request->get('document'),
                'marca' => Helpers::checkBrand($request->getHost())
            ]
        );
        
        if(empty($this->request->result) || !Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            return [
                'error' => true,
                'messages' => 'Error al verificar tus datos, intenta de nuevo.'
            ];
        }
        
        if(!$this->request->result->Existe) {
            return [
                'error' => true,
                'messages' => 'Actualmente no vendes por catálogo en nuestra marca.'
            ];
        }
        
        if(empty($this->request->result->Email)) {
            return [
                'error' => false,
                'email' => false
            ]; 
        }
        
        //Realizamos split del correo por el caracter @
        $split = explode('@', $this->request->result->Email);
        //Formamos el correo para mostrar a la asesora
        $email = substr($split[0], 0, -5) . '********@' . $split[1];  
        
        return [
            'error' => false,
            'email' => true,
            'content' => $email,
            'key' => base64_encode($this->request->result->Email)
        ];
    }
    
    public function findQuestion(Request $request) {
        $this->request = (new CallWebService())->callGet(
            'http://10.244.9.70:9696/api/validations',
            [
                'id' => $request->get('document'),
                'marca' => Helpers::checkBrand($request->getHost())  
            ]
        );
        
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            return [
                'error' => true,
                'messages' => 'Error al obtener las preguntas.'
            ];
        }  
        
        $fullNameList = [];
        $documentDateList = [];
        $documentList = [];
        
        if(!empty($this->request->result)) {
            foreach($this->request->result as $value) {
                $fullNameList[] = utf8_encode(ucwords(strtolower(utf8_decode($value->name))));
                $documentList[] = $value->id;
                $documentDateList[] = utf8_encode(ucwords(strtolower(utf8_decode($value->fechaExpedicion))));
            }
        }
        
        //Desordenamos las listas
        shuffle($documentList);
        shuffle($fullNameList);
        shuffle($documentDateList);
        
        return [
            'error' => false,
            'questions' => $this->request->result,
            'name' => $fullNameList,
            'date'=> $documentDateList,
            'document' => $documentList
        ];
    }
    
    public function checkQuestion(Request $request) {
        $this->request = (new CallWebService())->callPost(
            'http://10.244.9.70:9696/api/validations',
            [
                'Id' => $request->get('questionDocument'),
                'Name' => strtoupper($request->get('questionName')),
                'FechaExpedicion' => strtoupper($request->get('questionDocumentDate'))
            ],
            [
                'marca' => Helpers::checkBrand($request->getHost())
            ]
        );
        
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            return [
                'error' => true,
                'messages' => 'Los datos son incorrectos, intenta de nuevo.'
            ];
        }
        
        return [
            'error' => false,
            'valid' => $this->request->result,
            'messages' => (!$this->request->result) ? 'Los datos son incorrectos, intenta de nuevo.' : ''
        ];
    }
    
    public function resetByEmail(Request $request) {
        $this->request = (new CallWebService())->callPut(
            'http://10.244.9.70:9696/Api/Usuarios/RenewPassword',
            [
                /*'Usuario' => $request->get('document'),
                'Clave' => '',
                'ClaveNueva' => '',
                'ClaveConfirmacion' => ''*/
            ], 
            [
                'email' => base64_decode($request->get('key')),
                'documentoId' => $request->get('document'),
                'marca' => Helpers::checkBrand($request->getHost())  
            ]
        );
        
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            return [
                'error' => true,
                'messages' => 'Error al enviar la clave a tú correo, intenta de nuevo.'
            ];
        }
        
        return [
            'error' => false,
            'messages' => 'Se ha enviado la clave a tú correo electronico, revisa la bandeja de entrada.'
        ];
    }
    
    public function resetByChange(Request $request) {
        $this->request = (new CallWebService())->callPost(
            'http://10.244.9.70:9696/Api/Configurations/RecoverPassword',
            [
                'Usuario' => $request->get('document'),
                'Clave' => '',
                'ClaveNueva' => base64_encode($request->get('password')),
                'ClaveConfirmacion' => base64_encode($request->get('repeatpassword'))
            ]
        );
        
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            return [
                'error' => false,
                'messages' => 'Error al recuperar tú clave, intenta de nuevo.'
            ];
        }
        
        return [
            'error' => false,
            'messages' => 'Tú clave ha sido cambiada correctamente.'
        ];
    }
}
