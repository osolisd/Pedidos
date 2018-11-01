<?php
namespace App\Util;

/**
 * @description Clase para realizar llamados a los servicios REST
 * @author      Andres.Castellanos <andres.castellanos@softwareestartegico.com>
 * @date        Septiembre 13 de 2017
 */

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use function GuzzleHttp\Psr7\str;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Log;
use App\Util\Logger;

class CallWebService {
    
    private $client;
    private $response;
    private $request;
    
    public function __construct() {
        //Inicializamos el cliente
        $this->client = new Client([
            'base_uri' => WebServiceParameters::END_POINT_SERVER
        ]);
    }

    /**
     * @description Metodo para consumir servicios REST por el verbo Http GET
     * @param       string $endpoint
     * @param       array $params
     * @return      array response
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     */
    public function callGet($endpoint, $params, $header = null) {
        try {
            Log::info('-----------------------------------------------------------------------------');
            Log::info('CALL WEB SERVICE ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::');
            Log::info('-----------------------------------------------------------------------------');
            Log::info($endpoint);
            Log::info('-----------------------------------------------------------------------------');
            Log::info(json_encode($params));
            Log::info('-----------------------------------------------------------------------------');
            Log::info($header);
            Log::info('-----------------------------------------------------------------------------');
            //Realizamos el llamado al servicio REST
            $this->request = $this->client->request(
                Constants::METHOD_GET, 
                $endpoint, [
                    'query' => $params
                ]
            );
            //Construimos el response
            $this->response = Array (
                'code' => $this->request->getStatusCode(), //Obtenemos el codigo de la peticion 200
                'result' => json_decode($this->request->getBody()->getContents()) //Deserializamos el contenido JSON del response
            );
        } catch (ClientException $ex) {   
            $this->response = Array (
                'code' => $ex->getResponse()->getStatusCode(),  //Obtenemos el codigo de la peticion 400 / 500
                'message' => json_decode($ex->getResponse()->getBody()->getContents()) //Deserializamos el contenido JSON del response
            );
            //Agregamos el error al log
            Logger::errorClient($ex);  
        } catch (ServerException $ex) {
            $this->response = Array (
                'code' => $ex->getResponse()->getStatusCode(),  //Obtenemos el codigo de la peticion 400 / 500
                'message' => json_decode($ex->getResponse()->getBody()->getContents()) //Deserializamos el contenido JSON del response
            );
            //Agregamos el error al log
            Logger::errorServer($ex);
        } 
        
        //Agregamos el resultado al log
        Log::info('-----------------------------------------------------------------------------');
        Log::info('RESPONSE WEB SERVICE ::::::::::::::::::::::::::::::::::::::::::::::::::::::::');
        Log::info('-----------------------------------------------------------------------------');
        Log::info($this->response);
        Log::info('-----------------------------------------------------------------------------');
        //Retornamos el response
        return Helpers::arrayToObject($this->response);  
    }
    
    /**
     * @description Metodo para consultar servicios REST por le verbo Http POST
     * @param       string $endpoint
     * @param       array $params
     * @param       array $query
     * @return      JSON response
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     */
    public function callPost($endpoint, $params, $query = null) {
        try {
            Log::info('-----------------------------------------------------------------------------');
            Log::info('CALL WEB SERVICE ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::');
            Log::info('-----------------------------------------------------------------------------');
            Log::info($endpoint);
            Log::info('-----------------------------------------------------------------------------');
            Log::info(json_encode($params));
            Log::info('-----------------------------------------------------------------------------');
            Log::info($query);
            Log::info('-----------------------------------------------------------------------------');
            
            //Realizamos el llamado al servicio REST
            $this->request = $this->client->request(Constants::METHOD_POST, $endpoint, ['query' => $query, 'json' => $params]);
            //Construimos el response
            $this->response = Array (
                'code' => $this->request->getStatusCode(), //Obtenemos el codigo de la peticion 200
                'result' => json_decode($this->request->getBody()->getContents()) //Deserializamos el contenido JSON del response
            );
        } catch (ClientException $ex) {
            $this->response = Array (
                'code' => $ex->getResponse()->getStatusCode(),  //Obtenemos el codigo de la peticion 400 / 500
                'message' => json_decode($ex->getResponse()->getBody()->getContents()) //Deserializamos el contenido JSON del response
            );
            //Agregamos el error al log
            Log::info('-----------------------------------------------------------------------------');
            Log::info('EXCEPTION WEB SERVICE :::::::::::::::::::::::::::::::::::::::::::::::::::::::');
            Log::info('-----------------------------------------------------------------------------');
            Log::error($ex);
            Log::info('-----------------------------------------------------------------------------');
        } catch (ServerException $ex) {
            $this->response = Array (
                'code' => $ex->getResponse()->getStatusCode(),  //Obtenemos el codigo de la peticion 400 / 500
                'message' => json_decode($ex->getResponse()->getBody()->getContents()) //Deserializamos el contenido JSON del response
            );
            //Agregamos el resultado al log
            Log::info('-----------------------------------------------------------------------------');
            Log::info('EXCEPTION WEB SERVICE :::::::::::::::::::::::::::::::::::::::::::::::::::::::');
            Log::info('-----------------------------------------------------------------------------');
            Log::error($ex);
            Log::info('-----------------------------------------------------------------------------');
        }
        
        //Agregamos el resultado al log
        Log::info('-----------------------------------------------------------------------------');
        Log::info('RESPONSE WEB SERVICE ::::::::::::::::::::::::::::::::::::::::::::::::::::::::');
        Log::info('-----------------------------------------------------------------------------');
        Log::info($this->response);
        Log::info('-----------------------------------------------------------------------------');
        
        //Retornamos el response
        return Helpers::arrayToObject($this->response);
    }
    
    /**
     * @description Metodo para consultar servicios REST por le verbo Http PUT
     * @param       string $endpoint
     * @param       array $params
     * @param       array $query
     * @return      JSON response
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     */
    public function callPut($endpoint, $params, $query = null) {
        try {
            Log::info('-----------------------------------------------------------------------------');
            Log::info('CALL WEB SERVICE ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::');
            Log::info('-----------------------------------------------------------------------------');
            Log::info($endpoint);
            Log::info('-----------------------------------------------------------------------------');
            Log::info(json_encode($params));
            Log::info('-----------------------------------------------------------------------------');
            Log::info($query);
            Log::info('-----------------------------------------------------------------------------');
            //Realizamos el llamado al servicio REST
            $this->request = $this->client->request(Constants::METHOD_PUT, $endpoint, ['query' => $query, 'json' => $params]);
            //Construimos el response
            $this->response = Array (
                'code' => $this->request->getStatusCode(), //Obtenemos el codigo de la peticion 200
                'result' => json_decode($this->request->getBody()->getContents()) //Deserializamos el contenido JSON del response
            );
        } catch (ClientException $ex) {
            $this->response = Array (
                'code' => $ex->getResponse()->getStatusCode(),  //Obtenemos el codigo de la peticion 400 / 500
                'message' => json_decode($ex->getResponse()->getBody()->getContents()) //Deserializamos el contenido JSON del response
            );
            //Agregamos el error al log
            Log::info('-----------------------------------------------------------------------------');
            Log::info('EXCEPTION WEB SERVICE :::::::::::::::::::::::::::::::::::::::::::::::::::::::');
            Log::info('-----------------------------------------------------------------------------');
            Log::error($ex);
            Log::info('-----------------------------------------------------------------------------');
        } catch (ServerException $ex) {
            $this->response = Array (
                'code' => $ex->getResponse()->getStatusCode(),  //Obtenemos el codigo de la peticion 400 / 500
                'message' => json_decode($ex->getResponse()->getBody()->getContents()) //Deserializamos el contenido JSON del response
            );
            //Agregamos el error al log
            Log::info('-----------------------------------------------------------------------------');
            Log::info('EXCEPTION WEB SERVICE :::::::::::::::::::::::::::::::::::::::::::::::::::::::');
            Log::info('-----------------------------------------------------------------------------');
            Log::error($ex);
            Log::info('-----------------------------------------------------------------------------');
        }
        
        //Agregamos el resultado al log
        Log::info('-----------------------------------------------------------------------------');
        Log::info('RESPONSE WEB SERVICE ::::::::::::::::::::::::::::::::::::::::::::::::::::::::');
        Log::info('-----------------------------------------------------------------------------');
        Log::info($this->response);
        Log::info('-----------------------------------------------------------------------------');
        //Retornamos el response
        return Helpers::arrayToObject($this->response);
    }
    
    /**
     * @description Metodo para consultar servicios REST por le verbo Http PUT
     * @param       string $endpoint
     * @param       array $params
     * @param       array $query
     * @return      JSON response
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     */
    public function callDelete($endpoint, $params, $query = null) {
        try {
            Log::info('-----------------------------------------------------------------------------');
            Log::info('CALL WEB SERVICE ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::');
            Log::info('-----------------------------------------------------------------------------');
            Log::info($endpoint);
            Log::info('-----------------------------------------------------------------------------');
            Log::info(json_encode($params));
            Log::info('-----------------------------------------------------------------------------');
            Log::info($query);
            Log::info('-----------------------------------------------------------------------------');
            //Realizamos el llamado al servicio REST
            $this->request = $this->client->request(Constants::METHOD_DELETE, $endpoint, ['query' => $query, 'json' => $params]);
            //Construimos el response
            $this->response = Array (
                'code' => $this->request->getStatusCode(), //Obtenemos el codigo de la peticion 200
                'result' => json_decode($this->request->getBody()->getContents()) //Deserializamos el contenido JSON del response
            );
        } catch (ClientException $ex) {
            $this->response = Array (
                'code' => $ex->getResponse()->getStatusCode(),  //Obtenemos el codigo de la peticion 400 / 500
                'message' => json_decode($ex->getResponse()->getBody()->getContents()) //Deserializamos el contenido JSON del response
            );
            //Agregamos el error al log
            Log::info('-----------------------------------------------------------------------------');
            Log::info('EXCEPTION WEB SERVICE :::::::::::::::::::::::::::::::::::::::::::::::::::::::');
            Log::info('-----------------------------------------------------------------------------');
            Log::error($ex);
            Log::info('-----------------------------------------------------------------------------');
        } catch (ServerException $ex) {
            $this->response = Array (
                'code' => $ex->getResponse()->getStatusCode(),  //Obtenemos el codigo de la peticion 400 / 500
                'message' => json_decode($ex->getResponse()->getBody()->getContents()) //Deserializamos el contenido JSON del response
            );
            //Agregamos el error al log
            Log::info('-----------------------------------------------------------------------------');
            Log::info('EXCEPTION WEB SERVICE :::::::::::::::::::::::::::::::::::::::::::::::::::::::');
            Log::info('-----------------------------------------------------------------------------');
            Log::error($ex);
            Log::info('-----------------------------------------------------------------------------');
        }
        
        //Agregamos el resultado al log
        Log::info('-----------------------------------------------------------------------------');
        Log::info('RESPONSE WEB SERVICE ::::::::::::::::::::::::::::::::::::::::::::::::::::::::');
        Log::info('-----------------------------------------------------------------------------');
        Log::info($this->response);
        Log::info('-----------------------------------------------------------------------------');
        //Retornamos el response
        return Helpers::arrayToObject($this->response);
    }
}

