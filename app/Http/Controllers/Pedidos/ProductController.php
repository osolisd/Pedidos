<?php
/**
 * @description Controlador para el manejo de plu de la fucnionalidad auto completado
 * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
 * @date        Noviembre 08 de 2017
 */
namespace App\Http\Controllers\Pedidos;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Util\CallWebService;
use App\Util\Helpers;
use App\Util\WebServiceParameters;

class ProductController extends Controller {

    private $client;
    private $request;
    private $response;
    
    private $products;
    
    public function __construct() {
        //Verificamos que el usuario este autenticado
        $this->middleware('auth');
        //Instaciamos un nuevo objeto para consumir servicios
        $this->client = new CallWebService();
    }
    
    /**
     * @description Metodo para obtener todos los PLU de la campaña
     * @return      JSON
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Noviembre 08 de 2017
     */
    public function findAll(Request $request) {
        //Eliminamos los PLU
        $this->deleteAll((!empty($request->get('document'))) ? $request->get('document') : auth()->user()->document);  
              
        //Consumimos el servicio REST de Productos
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_PRODUCTS,   
            [
                'id' => (!empty($request->get('document'))) ? $request->get('document') : auth()->user()->document,
                'marca' => auth()->user()->brand
            ]
        );        
        
        if(count($this->request->result) > 1) {
            //Recorremos la lista de plus obtenidos en el servicio
            foreach($this->request->result as $product) {
                //Agregamos el producto a la lista 
                $this->products[] = [
                    'id_adviser' => (!empty($request->get('document'))) ? $request->get('document') : auth()->user()->document,
                    'code_plu' => $product->CodigoPlu,
                    'name_plu' => $product->Description,
                    'campaing' => $product->CampanaId,
                    'brand' => auth()->user()->brand,
                    'is_package' => $product->EsPaqueteAhorro
                ];
            }
            
            //Realizamos la inserción de forma masiva de productos
            DB::table('products')->insert($this->products);
        }
        
        //Buscamos la lista de productos por cedula asesora y marca
        $this->products = DB::table('products')
                ->where([
                    'id_adviser' => (!empty($request->get('document'))) ? $request->get('document') : auth()->user()->document, 
                    'brand' => auth()->user()->brand
                ])
                ->get(['code_plu', 'name_plu', 'is_package']);  
                
        //Construimos la respuesta
        $this->response = [
            'error' => false,
            'products' => $this->products
        ];
        
        //Retornamos la respuesta
        return $this->response;
    }   
    
    /**
     * @description Metodo para obtener los PLUS por filtro
     * @param       string $filter
     * @return      JSON
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Noviembre 08 de 2017
     */
    public function findByFilter($filter) {
        //Buscamos los productos por cedula asesora, campaña, marca y por el valor del filtro
        $this->products = DB::table('products')
                ->where([
                    'id_adviser' => auth()->user()->document,
                    'brand' => auth()->user()->brand,
                    'campaing' => Helpers::getCurrentCampaign()
                ])
                ->where('code_plu', 'like', '%' . $filter . '%')
                ->get(['code_plu', 'name_plu']); 
                
        //Construimos la respuesta                 
        $this->response = [
            'error' => false,
            'products' => $this->products
        ];
        //Retornamos la respuesta
        return $this->response;
    }
    
    /**
     * @description Metodo para eliminar todos los productos por asesora, campaña y marca
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Noviembre 09 de 2017
     */
    public function deleteAll($document = null) {
        //Eliminamos los productos por cedula asesora, campaña y marca
        DB::table('products')
            ->where([
                'id_adviser' => (!empty($document)) ? $document : auth()->user()->document,
                'brand' => auth()->user()->brand
            ])
            ->delete();
    }
}
