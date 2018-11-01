<?php
/**
 * @description Clase prueba unitaria metodos Controlador Pedidos
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

class OrderControllerTest extends TestCase {
    
    /**
     * @descrioption    Prueba unitaria para verificar que el metodo historial pedidos este correcto
     * @author          Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date            Septiembre 19 de 2017
     */
    public function testFindAll() {
        //Llamamos la ruta del metodo del controlador
        $response = $this->call('GET', '/findGeneraldata/{document}');
        //Agregamos el criterio de aceptación
        $this->assertEquals(200, $response->status());
    }
    
    /**
     * @description Prueba unitaria para verificar que el metodo detalle pedido este correcto
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Septiembre 19 de 2017
     */
    public function testFindById() {
        //Llamamos la ruta del metodo del controlador
        $response = $this->call('GET', '/findOrderById/1');
        //Agregamos el criterio de aceptación
        $this->assertEquals(200, $response->status());
    }
}
