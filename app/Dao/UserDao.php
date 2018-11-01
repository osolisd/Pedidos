<?php
/**
 * Clase DAO para re3alizar operaciones de base de datos sobre la tabla Users
 */
namespace App\Dao;

use App\Util\Constants;
use App\Util\Logger;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class UserDao {
    
    public function __construct() {
        
    }
    
    /**
     * @description Metodo para guardar el usuario en la BD
     * @param       array $entity
     * @return      boolean true | false
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Noviembre 30 de 2017
     */
    public function save($entity) {
        try {
            //Guardamos el usuario 
            DB::table('users')->insert($entity);  
            //Agregamos al log los datos del usuario
            Logger::saveDatabase($entity);
            return Constants::TRUE;
        } catch (QueryException $ex) {
            //Agregamos al log la excepcion
            Logger::errorMySQL($ex);
            return Constants::FALSE;
        }
    }
    
    /**
     * @description Metodo para para obtener el usuario por documento
     * @param       string $document
     * @return      array
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 04 de 2017
     */
    public function findByDocument($document) {
        try {
            //Buscamos el usuario por documento
            return DB::table('users')->where('document', $document)->first();
        } catch (QueryException $ex) {
            //Agregamos al log la excepcion
            Logger::errorMySQL($ex);
            return [];
        }
    }
    
    /**
     * @description Metodo para editar el usuario
     * @param       string $document
     * @param       array $entity
     * @return      boolean true | false
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Diciembre 04 de 2017
     */
    public function update($document, $entity) {
        try {
            //Editamos el usuario
            DB::table('users')
                ->where('document', $document)
                ->update($entity);
            return Constants::TRUE;
        } catch (QueryException $ex) {
            //Agregamos al log la excepcion
            Logger::errorMySQL($ex);
            return Constants::FALSE;
        }
    }
    
    /**
     * @description Metodo para eliminar el usuario de la BD
     * @param       integer $document
     * @return      boolean true | false
     * @author      Andres.Castellanos <andres.castellanos@softwareestrategico.com>
     * @date        Noviemre 27 de 2017
     */
    public function delete($document) {
        try {
            //Eliminamos el usuario de la base de datos
            DB::table('users')->where('document', $document)->delete();
            return Constants::TRUE;
        } catch (QueryException $ex) {
            //Agregamos al log la excepcion
            Logger::errorMySQL($ex);
            return Constants::FALSE;
        }
    }
}

