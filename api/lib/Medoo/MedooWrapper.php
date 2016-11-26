<?php
require __DIR__.'/medoo.php';
require __DIR__.'/../../Config/mysqlConfig.php';

/**
 * Description of MedooWrapper
 *
 * @author Computer
 */
class MedooWrapper {
    
    public static $db = null;
    
    public static function getDb(){

        if(MedooWrapper::$db != null){
            return MedooWrapper::$db;
        }
            
        
        MedooWrapper::$db = new medoo([
            'database_type' => 'mysql',
            'database_name' => mysqlConnection::$dbname,
            'server' => mysqlConnection::$servername,
            'username' => mysqlConnection::$username,
            'password' => mysqlConnection::$password,
            'charset' => 'utf8'
        ]);
        
        return MedooWrapper::$db;
                
    } 

}
