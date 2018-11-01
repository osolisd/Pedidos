<?php
/**
 * @description Clase prueba unitaria metodos call web services
 * @author      Andres.Castellanos <andres.castellanos@softwareestartegico.com>
 * @date        Septiembre 19 de 2017
 * ----------------------------------------------------------------------------------------------
 * AUDITORIA DE CAMBIOS
 * ----------------------------------------------------------------------------------------------
 * Fecha Cambio     | Nombre Autor                      | Detalles Cambio
 * ----------------------------------------------------------------------------------------------
 */
namespace Tests\Feature\app\Util;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CallWebServiceTest extends TestCase {
    
    /**
     * @description Prueba unitaria para comprobar el llamado a web services metodo GET
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Septiembre 19 de 2017
     */
    public function testCallGet() {
        //Iniciamos el curl
        $curl = curl_init();
        //Seteamos el endpoint
        $endpoint = 'http://10.244.9.70:9198/api/Points';
        //Seteamos el metodo 
        $method = 'GET';
        //Seteamos los parametros
        $params = Array (
            'id' => '1028015456',
            'marca' => 'carmel'
        );
        //Ageregamos los parametros al end point    
        $endpoint = sprintf("%s?%s", $endpoint, http_build_query($params));
        curl_setopt($curl, CURLOPT_URL, $endpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //Ejecutamos el llamado y capturamos la respuesta
        $response = curl_exec($curl);
        //Agregamos el criterio de aceptación
        $this->assertEquals(200, curl_getinfo($curl, CURLINFO_HTTP_CODE));
        //Cerramos el curl
        curl_close($curl);
        //Agregamos criterio de aceptación
        $this->assertTrue(array_key_exists('CampanaActual', json_decode($response)));
    }
    
    /**
     * @description Prueba unitaria para comprobar el llamado a web services metodo POST
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Septiembre 19 de 2017
     */
    public function testCallPost() {
        //Iniciamos el curl
        $curl = curl_init();
        //Seteamos el endpoint
        $endpoint = 'http://10.244.9.70:9198//api/Orders';
        //Seteamos el metodo
        $method = 'POST';
        //Seteamos los parametros
        $params = Array (
            'Mark' => 'carmel',
            'DocumentNumber' => '1028015456',
            'ListDetails' => Array (
                Array(
                    'ProductoId' => '114120',
                    'Quantity' => 8
                ),
                Array(
                    'ProductoId' => '114134',
                    'Quantity' => 10
                )
            )
        );
        //Iniciamos el curl
        $curl = curl_init();
        
        curl_setopt($curl, CURLOPT_POST, 1);
        //Verificamos que hayan parametros
        if($params) {
            //Agregamos los parametros al endpoint
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        }
        
        curl_setopt($curl, CURLOPT_URL, $endpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //Ejecutamos el llamado y capturamos la respuesta
        $response = curl_exec($curl);
        //Agregamos el criterio de aceptación
        $this->assertEquals(200, curl_getinfo($curl, CURLINFO_HTTP_CODE));
        //Cerramos el curl
        curl_close($curl);
        //Agregamos criterio de aceptación
        $this->assertTrue(array_key_exists('Respuesta', json_decode($response))); 
    }
    
}
