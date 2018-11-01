<?php
namespace   App\Dao;

use Illuminate\Database\QueryException;
use App\Util\Logger;
use Illuminate\Support\Facades\DB;

class AmmountDao {
    
    private $ammount;  
    
    public function __construct() {
        
    }
    
    /**
     * @description Metodo para guardar los monto y tolerancia
     * @param       array $entity
     * @return      boolean
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 27 de 2017
     */
    public function save($entity) {
        try {
            DB::table('ammount')->insert($entity); 
        } catch (QueryException $ex) {
            Logger::errorMySQL($ex);
            return false;
        }
        
        return true;
    }
    
    public function findByBrandDocument($brand, $document) {
        try {
            $this->ammount = DB::table('ammount')->where('');
        } catch (QueryException $ex) {
            Logger::errorMySQL($ex);
        }
        
        return $this->ammount;
    }
    
    /**
     * @description Metodo para eliminar los montos de la aseora
     * @param       string $brand
     * @param       string $document
     * @return      boolean
     */
    public function delete($brand, $document) {
        try {
            DB::table('ammount')
                ->where('id_brand', $brand)
                ->where('people_id', $document)
                ->delete();
        } catch (QueryException $ex) {
            Logger::errorMySQL($ex);
            return false;
        }
        
        return true;
    }
    
}

