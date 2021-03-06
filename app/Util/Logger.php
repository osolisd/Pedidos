<?php
namespace App\Util;

use Illuminate\Support\Facades\Log;

class Logger {
    
    public static function errorMySQL($exception) {
        Log::error("====================================================================================================");
        Log::error("MySQL Exception ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::");
        Log::error("====================================================================================================");
        Log::error($exception);
        Log::error("====================================================================================================");
    }
    
    public static function errorServer($exception) {
        Log::error("====================================================================================================");
        Log::error("Server Exception :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::");
        Log::error("====================================================================================================");
        Log::error($exception);
        Log::error("====================================================================================================");
    }
    
    public static function errorClient($exception) {
        Log::error("====================================================================================================");
        Log::error("Client Exception :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::");
        Log::error("====================================================================================================");
        Log::error($exception);
        Log::error("====================================================================================================");
    }
    
    public static function saveDatabase($object) {
        Log::info("====================================================================================================");
        Log::info("Save Object Database :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::");
        Log::info("====================================================================================================");
        Log::info($object);
        Log::info("====================================================================================================");
    }
    
}

