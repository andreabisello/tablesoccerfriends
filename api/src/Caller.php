<?php

require_once __DIR__ . '/../Config/jwtSecret.php';
require_once 'Roles.php';
require_once __DIR__  . '/../lib/jwt/autoload.php';
use \Firebase\JWT\JWT;

class Caller {
    
    private $username;
    private $roles;
    private $jwt;
    
    function __construct($jwt){
        $this->jwt = $jwt;
        
        $token = false;
        try{
            $token = (array) JWT::decode($this->jwt, jwtSecret::$Key, array('HS256'));
        } catch (\Firebase\JWT\SignatureInvalidException $ex) {
            exit("Token Decode Fail!");
        } catch (\Firebase\JWT\ExpiredException $ex){
            echo "Token espirato!";
            return false;
        }

        $this->username = $token["username"];
        $this->roles = $token["roles"];

    }
    
    public function getUsername(){
        return $this->username;
    }
    
    public function hasRole($role){
        
        //i ruoli vengono ritornati nel jwt. Per una prova li faccio hard-codati
        
        array_push($this->roles, Roles::$CAN_DOWNLOAD_ATTACHMENTS);
        array_push($this->roles, Roles::$CAN_SEE_ATTACHMENTS);
        
        if (in_array($role, $this->roles)){
            return true;
        } else {
            return false;
        }
    }
 
}