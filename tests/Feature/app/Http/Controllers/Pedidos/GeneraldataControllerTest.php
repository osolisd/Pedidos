<?php
/**
 * @description Clase prueba unitaria metodos Controlador Datos Generales Asesora
 * @author      Andres.Castellanos <andres.castellanos@softwareestartegico.com>
 * @date        Septiembre 19 de 2017
 * ----------------------------------------------------------------------------------------------
 * AUDITORIA DE CAMBIOS
 * ----------------------------------------------------------------------------------------------
 * Fecha Cambio     | Nombre Autor                      | Detalles Cambio
 * ----------------------------------------------------------------------------------------------
 */
namespace Tests\Feature\app\Http\Controllers\Pedidos;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GeneraldataControllerTest extends TestCase {
    
    /**
     * @description Prueba unitaria para comprobar que el metodo Datos Generales este correcto
     * @author      Andres.Castellano <andres.castellanos@softwareestrategico.com>
     * @date        Septiembre 14 de 2017 
     */
    public function testFind() { 
        //Llamamos la ruta del metodo del controlador
        $response = $this->call('GET', '/findAllOrders/{document}');
        //Agregamos el criterio de aceptación
        $this->assertEquals(200, $response->status());
    }
}
