<?php
namespace App\Dao;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Util\Constants;
use App\Util\Logger;

class BalanceDao {
    
    private $balance;
    
    public function __construct() {
        
    }
    
    public function save($entity) {
        try {
            DB::table('balance')->insert($entity);
            return Constants::TRUE;
        } catch (QueryException $ex) {
            Logger::errorMySQL($ex);
            return Constants::FALSE;
        }
    }
    
    public function exist() {
        $this->balance = DB::table('balance')
                             ->where('id_people', auth()->user()->document)
                             ->where('id_brand', auth()->user()->brand)
                             ->get();
        
        if(count($this->balance) > 0) {
            return Constants::TRUE;
        }
        
        return Constants::FALSE;
    }
    
}

