<?php

require_once __DIR__ . '/../Config/mysqlConfig.php';
require_once __DIR__ . '/../lib/Medoo/autoload.php';

/**
 * Description of DatabaseConnection
 * Questa classe gestisce la connessione al database.
 * @author Computer
 */
class Db {
    
    public static $db = null;
    
    private static $connection = null;
    
    public static function getDb(){

        if(Db::$db != null){
            return Db::$db;
        }

        Db::$db = new medoo([
            'database_type' => 'mysql',
            'database_name' => mysqlConnection::$dbname,
            'server' => mysqlConnection::$servername,
            'username' => mysqlConnection::$username,
            'password' => mysqlConnection::$password,
            'charset' => 'utf8'
        ]);
        
        return Db::$db;
                
    }
    
    public static function db_connect() {

        if(!isset(self::$connection)) {
             // Load configuration as an array. Use the actual location of your configuration file
            self::$connection = mysqli_connect(mysqlConnection::$servername,mysqlConnection::$username,mysqlConnection::$password, mysqlConnection::$dbname);
        }  
        
        if(self::$connection === false) {
            // Handle error - notify administrator, log to a file, show an error screen, etc.
            return mysqli_connect_error();
            exit();
        }
        return self::$connection;
    }
    
    public static function runQuery($query){
        
        $connection = Db::db_connect();
        $result = mysqli_query($connection, $query);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    

}
