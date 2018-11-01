<?php
/**
 * @description Clase prueba unitaria metodos helpers
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

class HelpersTest extends TestCase {
    
    /**
     * @description Prueba unitaria metodo obtener mensaje archivo propiedades
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Septiembre 19 de 2017
     */
    public function testGetMessage() {
        //Asignamos el valor de la propiedad a buscar
        $key = 'unit.test.message';
        //obtiene el archivo de propiedades
        $file = parse_ini_file('testmessages.properties', false);
        //capturamos el mensaje del archivo de propiedades
        $result = $file[$key];
        
        //Agregamos el criterio de aceptación
        $this->assertEquals('Test Properties', $result);
    }
    
    /**
     * @description Prueba unitaria metodo remover formato moneda
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Septiembre 19 de 2017
     */
    public function testRemoveMoneyFormat() {
        //eliminamos el caracter "." de la cadena y guardamos el resultado en una lista
        $list = explode(".", "100.000.2");
        $result = '';
        //Recorremos la lista
        for($i = 0; $i <= count($list) - 1; $i++) {
            //Concatenamos los valores de la lista por posición
            $result = $result . $list[$i];
        }
        
        //Agregamos el criterio de aceptación
        $this->assertEquals('1000002', $result);
    }
    
    /**
     * @description Prueba unitaria para probar metodo errores servcios web
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Septiembre 19 de 2017
     */
    public function testCheckErrors() {
        //Seteamos los datos
        $list = Array (
            Array(
                'error' => true,
                'mensaje' => 'Error al llamar servicio web directora'
            ),
            Array(
                'error' => true,
                'mensaje' => 'Error al llamar el servicio web personas'
            )
        );
        
        //Resultado si no hay errores
        $result = Array (
            'error' => false
        );
        //Recorremos la lista de objetos
        for($i = 0; $i <= count($list) - 1; $i++) {  
            //Verificamos si el objeto la propiedad error esta en true
            if($list[$i]['error']) {
                //Cambios la propiedad error de la respuesta a verdadero
                $result['error'] = true;
                //Agregamos el mensaje de error
                $result['mensaje'][] = $list[$i]['mensaje'];
            }
        }
        
        //Agregamos los criterios de aceptación en caso de que haya errores
        $this->assertTrue($result['error']);
        $this->assertEquals('Error al llamar servicio web directora', $result['mensaje'][0]);
        $this->assertEquals('Error al llamar el servicio web personas', $result['mensaje'][1]);
        //Setamos los datos en caso que no hayan errores
        $result = Array (
            'error' => false    
        );
        
        //Agregamos el criterio en caso de que no haya errores
        $this->assertFalse($result['error']);
    }
    
    /**
     * @description Prueba unitaria para comprobar que el metodo contar lista este correctamente
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Septiembre 19 de 2017
     */
    public function testCountList() {
        //Seteamos los datos de prueba
        $list = Array (
            'id' => 1,
            'name' => 'Test',
            'brand' => 'Carmel',
            'document' => '12345678'
        );
        
        //Agregamos el criterio de aceptación
        $this->assertCount(4, $list);
    }
    
    /**
     * @description Prueba unitaria para testeaer el metodo formato moneda
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Septiembre 19 de 2017
     */
    public function testNumberFormat() {
        //Setamos los datos
        $number = 100000;
        //Damos formato al número
        $format = number_format($number, 2, ',', '.');
        //Agregamos el criterio de aceptacion
        $this->assertEquals('100.000,00', $format);
    }
}
