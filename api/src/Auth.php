<?php
require_once 'Db.php';
require_once __DIR__  . '/../lib/jwt/autoload.php';
require_once __DIR__ . '/../Config/jwtSecret.php';

use \Firebase\JWT\JWT;

/**
 * Description of UserCalling
 *
 * @author Computer
 */
class Auth {
    
    public function login() {
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $username = $data["username"];
        $password = $data["password"];
        
        //dati da ottenere
        $jtw = "";
        $groupsOwned = [];
        $companiesJoined = [];

        if (Db::getDb()->has("players", ["AND" =>
                    [
                        "username" => $username,
                        "password" => $password
                    ]
                ])
        ) {
            //password correct, devo generare jwt e restituirlo
            
            $token = array(
                "username" => $username,
                "roles" => array(
                    "user-create", "user-delete"
                )
            );
            
            $userInformation = Db::getDb()->select("players", ["username","email","avatar","name","surname"], [
                    "username" => $username
            ]);

            $jwt = JWT::encode($token,jwtSecret::$Key);   
        } else {
            //password errata
            return false;
        }
        
        
        $response = ["Status" => "OK", "jwt" => $jwt , "data" => $userInformation];
        //$response = ["Status" => "OK", "jwt" => $jwt];
        return $response;
        
    }
 
    
    
    
}
