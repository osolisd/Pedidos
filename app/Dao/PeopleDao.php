<?php
namespace App\Dao;

use App\Util\Constants;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Util\Logger;

class PeopleDao {
    
    private $user;
    
    public function __construct() {
        
    }
    
    
    public function save($entity) {
        try {
            DB::table('people')->insert($entity);
            return Constants::TRUE;
        } catch (QueryException $ex) {
            Logger::errorMySQL($ex);  
            return Constants::FALSE;
        }
    }
    
    public function exist() {
        $this->user = DB::table('people')
                          ->where('document', auth()->user()->document)
                          ->where('current_brand', auth()->user()->brand)
                          ->get();
        
        if(count($this->user) > 0) {
            return Constants::TRUE;
        }
        
        return Constants::FALSE;
    }
    
    
}

