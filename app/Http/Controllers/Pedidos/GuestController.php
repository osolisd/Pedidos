<?php

/**
 * Contorlador para gestionar los servicios que no necesitan autenticación
 */

namespace App\Http\Controllers\Pedidos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Util\CallWebService;
use App\Util\Helpers;
use App\Util\Constants;
use App\Util\WebServiceParameters;
use Illuminate\Support\Facades\Log;

class GuestController extends Controller {
    
    private $request;
    private $response;
    private $images;
    private $client;
    
    /**
     * @description Metodo para visualizar el log
     * @return      plain/text
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Noviembre 06 de 2017
     */
    public function showLogger() {
        $file = file('../storage/logs/laravel.log');
        // Recorrer nuestro array, mostrar el código fuente HTML como tal y mostrar tambíen los números de línea.
        foreach ($file as $num_línea => $línea) {
            echo htmlspecialchars($línea) . "<br />\n";
        }
    }
    
    /**
     * @description Metodo para obtener images de incentivos
     * @return      JSON
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Noviembre 08 de 2017        
     */
    public function findImagesUrl($brand) {
        //Creamos eun nuevo objeto de tipo CallWebService
        $this->client = new CallWebService();
        //Consumimos el servicio REST de Imagenes
        $this->request = $this->client->callGet(
            WebServiceParameters::WS_IMAGES_HOME, 
            [
                'marca' => $brand
            ]
        );
        
        //Verificamos que exista error en la respuesta HTTP
        if(!Helpers::isEquals($this->request->code, Constants::HTTP_OK)) {
            //Retornamos el resultado
            return ['error'  => true, 'images' => []];
        }
        
        $images = [];   
        
        //Recorremos la lista de imagenes que devuelve el servicio
        foreach ($this->request->result as $image) {
            $images[] = $image->Valor;
        }
        
        //Constrauimos la respuesta
        $this->response = [
            'error' => false,
            'images' => $images
        ];
        
        //Retornamos la respuesta
        return $this->response;
    }
}
