<?php

/**
 * Controlador para la gestion de autenticación del usuario, como tambien el cambio de marca
 */

namespace App\Http\Controllers\Auth;

use App\Dao\UserDao;
use App\Http\Controllers\Controller;
use App\Util\CallWebService;
use App\Util\Constants;
use App\Util\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Util\WebServiceParameters;
use App\Util\Logger;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller {

    use AuthenticatesUsers;
    
    private $client;
    private $request;
    private $response;
    private $user;  
    
    private $role = null;
    private $brand = null;
    private $brands = null;
    private $functions = null;
    
    protected $guard = 'auths';   
    
    public function __construct() {
        //Si estamos en forma de invitado mostramos el formulario de inicio de sesión
        $this->middleware('guest', ['only' => 'showLoginForm']);  
        //Inicializamos el cliente cliente para llamar los servicios Rest
        $this->client = new CallWebService();
    }
    
    /**
     * @description Metodo para autenticar el usuario
     * @param       Request $request
     * @return      view view
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Octubre 26 de 2017
     */
    public function authenticate(Request $request) {
        //Validamos los campos del formulario y capturamos los valores
        $credentials = $request->validate([
            'user' => 'required|string|max:20',
            'password' => 'required|string|max:20'  
        ]);
        
        //Consultamos si existe el usuario
        $this->request = Helpers::arrayToObject($this->client->callPost(
            'http://10.244.9.70:9696/' . WebServiceParameters::WS_USER_AUTHENTICATION, [
                'usuario' => $credentials['user'],
                'clave' => base64_encode($credentials['password'])
            ]
        ));   
        
        //Verificamos si el codigo HTTP es 401
        if(Helpers::isEquals($this->request->code, Constants::HTTP_UNAUTHORIZED)) {
            return back()
                    ->withErrors(['credentials' => Helpers::getMessage('auth.creadentials.error')])
                    ->withInput(request(['user']));
        }
        
        //Verificamos el estado del request sea diferente de 200 OK
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK) || empty($this->request->result)) {
            return back()
                    ->withErrors(['credentials' => Helpers::getMessage('call.web.service.error')])
                    ->withInput(request(['user']));
        }     
        
        //Verificamos el estado del usuario
        //true = Usuario activo
        //false = Usuario bloqueado
        if(!Helpers::isEquals($this->request->result->estado, Constants::TRUE)) {
            //Retornamos el inicio
            return back()
                    ->withErrors(['credentials' => Helpers::getMessage('auth.user.loock')])
                    ->withInput(request(['user']));
        }
        
        //Consultamos la lista de marcas inscritas
        $this->brand = self::findAllBrands($this->request->result->documentoIdentidad, $request->getHost());
        
        //Si la marca es vacio / asesora no existe para la marca
        if(empty($this->brand)) {
            return back()
                    ->withErrors(['credentials' => Helpers::getMessage('adviser.not.exist.brand')])
                    ->withInput(request(['user']));
        }
        
        //Verificamos si existe algun error
        if($this->brand['error']) {
            return back()
                    ->withErrors(['credentials' => $this->brand['message']])
                    ->withInput(request(['user']));
        }
        
        //Verificamos que la asesora no este bloqueada por stencil
        if(Helpers::isUnlockAdviser($this->brand['brand'])) {
            return back()
                    ->withErrors(['credentials' => Helpers::getMessage('auth.user.loock')])
                    ->withInput(request(['user']));
        }
        
        //Consultamos el perfil de la asesora
        $this->role = self::findProfile();
        
        //Verificamos si se presenta algun error
        if(empty($this->role) || empty($this->role['profile']) || $this->role['error']) {
            return back()
                    ->withErrors(['credentials' => $this->role['message']])
                    ->withInput(request(['user']));
        }
        
        //Verificamos que el rol sea diferente de vacio
        if(!empty($this->role['profile'])) {
            //Consultamos el servicio de Asegurables
            $this->functions = $this->client->callGet(
                'http://10.244.9.70:9696/' . WebServiceParameters::WS_USER_FUNCTIONS .'(rolId=' . $this->role["profile"]->rolId . ')',
                []
            );
        }
        
        //Verificamos que se haya eliminado correctamente el usuario de la BD
        if(!(new UserDao())->delete($this->request->result->documentoIdentidad)) {  
            //Retornamos el inicio
            return back()
                    ->withErrors(['credentials' => Helpers::getMessage('database.error')])
                    ->withInput(request(['user']));
        }
        
        //Construimos el objeto del usuario a guardar
        $this->user = [
            'user_id' => $this->request->result->usuarioId,
            'document' => $this->request->result->documentoIdentidad,
            'name' => $this->request->result->persona->nombreCompleto,
            'password' => bcrypt($this->request->result->documentoIdentidad),
            'status' => $this->request->result->estado,
            'profile' => $this->role['profile']->rolId,  
            'profileName' => $this->role['profile']->nameRol,     
            'brand' => $this->brand['brand']->Marca,
            'brand_id' => $this->brand['brand']->IdMarca,
            'is_new' => $this->brand['brand']->Nueva,
            'clasification' => $this->brand['brand']->ClasificacionXValorId,
            'stencil_status' => $this->brand['brand']->EstadoStencilId,
            'stencil_locked' => $this->brand['brand']->BloqueoStencil,
            'is_locked' => $this->brand['brand']->Bloqueado,
            'code_zone' => $this->brand['brand']->CodZona,
            'active_zone' => $this->brand['brand']->ZonaActiva,
            'mail_plain' => $this->brand['brand']->MailPlan,
            'external_password' => base64_encode($credentials['password']),
            'external_user' => $credentials['user']
        ];
        
        //Verificamos que el usuario se guarde
        if(!(new UserDao())->save($this->user)) {  
            //Retornamos el inicio
            return back()
                    ->withErrors(['credentials' => Helpers::getMessage('database.error')])
                    ->withInput(request(['user']));
        }
        
        //Autenticamos el usuario en el sistema
        if(Auth::attempt(['document' => $this->request->result->documentoIdentidad, 'password' => $this->request->result->documentoIdentidad])) {
            return redirect('/Inicio');
        }
        
        //Retornamos el inicio en caso de que haya error al autenticar
        return back()
                ->withErrors(['credentials' => Helpers::getMessage('auth.user.error')])
                ->withInput(request(['user']));
    }
    
    /**
     * @description Metodo para obtener el perfil de la asesora
     * @return      boolean[]|string[]
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 22 de 2017
     */
    private function findProfile() {
        //Inicializamos la variable en null
        $profile = null;        
        
        //Verificamos si la marca es diferente de vacio
        if(!empty($this->brand) && !empty($this->brand['brand'])) {
            //Consultamos el rol de la asesora para la marca
            $profile = $this->client->callPost(
                'http://10.244.9.70:9696/' . WebServiceParameters::WS_USER_ROL,
                [
                    'UserId' => (empty($this->request->result->usuarioId)) ? auth()->user()->user_id : $this->request->result->usuarioId,
                    'Marck' => [
                        $this->brand['brand']->IdMarca
                    ]
                ]
            );
        }
        
        //Verificamos si se presento algun error
        if(empty($profile->result) || !Helpers::isEquals($profile->code, Constants::HTTP_OK)) {
            return [
                'error' => true,
                'message' => Helpers::getMessage('adviser.role.error')
            ];
        }
        
        //Retornamos el perfil
        return [
            'error' => false,
            'profile' => $profile->result[0]
        ];
    }
    
    /**
     * @description Metodo para obtener las marcas inscritas del usuario
     * @param       string $document
     * @param       string $domain
     * @return      boolean[]|string[]|NULL|boolean[]
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 21 de 2017
     */
    private function findAllBrands($document, $domain, $brandName = null) {
        Logger::errorMySQL("Enrtra!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
        //Consultamos servicio REST las marcas inscritas
        $this->brands = $this->client->callGet(
            WebServiceParameters::WS_USER_BRANDS,
            [
                'id' => $document
            ]
        );
        
        //Verificamos si hay algun error en la consulta de la marca de la asesora
        if(empty($this->brands->result) || !Helpers::isEquals($this->brands->code, Constants::HTTP_OK)) {
            return [
                'error' => true,
                'message' => Helpers::getMessage('error.find.brands')
            ];
        }
        
        $brand = null;  
        
        //Recorremos la lista de marcas
        foreach($this->brands->result as $key) {
            //Verificamso que la asesora exista en la marca actual
            if(Helpers::isEquals((!empty($brandName)) ? strtolower($brandName) : strtolower(Helpers::checkBrand($domain)), strtolower($key->Marca))) {
                //Asignamos los datos de la marca
                $brand = [
                    'error' => false,
                    'brand' => $key                  
                ];
            }
        }
        
        //Retornamos el resultado
        return $brand;
    }
    
    /**
     * @description Metodo para eliminar la sesión
     * @param       Request $request
     * @return      view  
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        octubre 27 de 2017
     */
    public function logout(Request $request) {
        //Verificamos si el usuario esta autenticado
        if(Auth::check()) {  
            //Eliminamos el Usuario de la BD
            (new UserDao())->delete(auth()->user()->document);
        }
        //Eliminamos datos de la sesión
        $this->removeUserData($request);
        //Verificamos que el request venga con credenciales
        if(!empty($request->get('credentials'))) {
            //Redireccionamos al inicio
            return redirect('/')->withErrors(['credentials' => $request->get('credentials')]);
        }
        
        //Redireccionamos al inicio
        return redirect('/');
    }
 
    /**
     * @description Metodo para cargar la vista de autenticación
     * @return      view
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Octubre 26 de 2017
     */
    public function showLoginForm(Request $request) {
        //Construimos la respuesta
        $this->response = [
            'marca' => Helpers::checkBrand($request->getHost())
        ];
        //Retornamos la vista
        return view('login')->with(['response' => $this->response]);    
    }
    
    /**
     * @description Metodo para cambiar la asesora de marca
     * @param       string $brand
     * @return      \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Noviembre 03 de 2017
     */
    public function changeBrand(Request $request, $brand) {
        //Verificamos si la sesión sigue activa
        if(!Auth::check()) {
            return redirect('/')->withErrors(['credentials' => 'Has iniciado sesión en otro navegador.']);  
        }
        
        //Verificamos que si no viene la marca 
        if(empty($brand)) {
            //Redirecciona al inicio
            return redirect('/Inicio');  
        }

        //Consultamos las marcas
        $this->brand = self::findAllBrands(auth()->user()->document, $request->getHost(), $brand);  
        
        Log::info('Brand =======================');
        Log::info($this->brand);
        
        //Si la marca es vacio / asesora no existe para la marca
        if(empty($this->brand)) {
            //Seteamos el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('adviser.not.exist.brand')]);
            //Redireccionamos al inicio
            return redirect('/Inicio');
        }
        
        //Verificamos si existe algun error
        if($this->brand['error']) {
            //Seteamos el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [$this->brand['message']]);
            //Redireccionamos al inicio
            return redirect('/Inicio');
        }
        
        //Verificamos que la asesora no este bloqueada por stencil
        if(Helpers::isUnlockAdviser($this->brand['brand'])) {
            //Seteamos el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [Helpers::getMessage('auth.user.loock')]);
            //Redireccionamos al inicio
            return redirect('/Inicio');
        }
        
        //Consultamos el perfil de la asesora
        $this->role = self::findProfile();
        
        //Verificamos si se presenta algun error
        if(empty($this->role) || empty($this->role['profile']) || $this->role['error']) {
            //Seteamos el mensaje de error
            Helpers::setSessionFlash(Constants::ERROR_MESSAGES, [$this->role['message']]);
            //Redireccionamos al inicio
            return redirect('/Inicio');
        }
        
        //Construimos el objeto del usuario a guardar
        $this->user = [
            'profile' => $this->role['profile']->rolId,
            'profileName' => $this->role['profile']->nameRol,
            'brand' => $this->brand['brand']->Marca,
            'brand_id' => $this->brand['brand']->IdMarca,
            'is_new' => $this->brand['brand']->Nueva,
            'clasification' => $this->brand['brand']->ClasificacionXValorId,
            'stencil_status' => $this->brand['brand']->EstadoStencilId,
            'stencil_locked' => $this->brand['brand']->BloqueoStencil,
            'is_locked' => $this->brand['brand']->Bloqueado,
            'code_zone' => $this->brand['brand']->CodZona,
            'active_zone' => $this->brand['brand']->ZonaActiva,
            'mail_plain' => $this->brand['brand']->MailPlan
        ];
        
        //Actualizamos la marca al usuario
        (new UserDao())->update(auth()->user()->document, $this->user);
        //Buscamos el usuario por documento
        $this->user = (new UserDao())->findByDocument(auth()->user()->document);
        //Eliminamos los datos de la sesión
        $this->removeUserData($request);
        
        //Autenticamos el usuario en el sistema 
        if(Auth::attempt(['document' =>  $this->user->document, 'password' =>  $this->user->document])) {
            //Redirecciona al inicio
            return redirect('/Inicio');
        }
    }
    
    /**
     * @description Metodo para eliminar los datos de sesión
     * @return      void
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Noviembre 03 de 2017
     */
    private function removeUserData(Request $request) {
        //Finalizamos la sesión
        Auth::logout();
        //Eliminamos los datos de la sesión
        $request->session()->flush();
        //Reconstruimos el is de la sesión
        $request->session()->regenerate();
    }
    
    /**
     * @description Metodo para obtener la marca
     * @return      string
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Octubre 30 de 2017
     */
    private function checkBrand($port) {
        if($port == 82) {
            return 'carmel';
        }
        
        if($port == 81) {
            return 'loguin';
        }
        
        return 'pcfk';
        //$request->getHost();
    }
}
