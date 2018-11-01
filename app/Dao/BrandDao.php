<?php
namespace Dao;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Util\Constants;
use App\Util\Logger;

class BrandDao {
    
    public function __construct() {
        
    }
    
    
    public function save($entity) {
        try {
            DB::table('brand')->insert($entity);
            return Constants::TRUE;
        } catch (QueryException $ex) {
            Logger::errorMySQL($ex);
            return Constants::FALSE;
        }
    }
    
}

