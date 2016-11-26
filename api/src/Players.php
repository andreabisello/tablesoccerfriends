<?php

require_once 'Db.php';
require_once __DIR__  . '/../lib/jwt/autoload.php';
require_once __DIR__ . '/../Config/jwtSecret.php';

use \Firebase\JWT\JWT;

/**
 * Description of Players
 *
 * @author Computer
 */
class Players {
    
    private $userCalling = null;
    
    function __construct($userCalling) {
       $this->userCalling = $userCalling;
    }
    
    public function create() {
        
        //check for role
        //echo $this->userCalling->hasRole("user-create");
        
        $data = json_decode(file_get_contents('php://input'), true);

        $username = $data["username"];
        $password = $data["password"];
        $name = $data["name"];
        $surname = $data["surname"];

        $result = Db::getDb()->has("players", [
            "username" => $username
        ]);
        
        if($result == 1){
            return "Username Already Exists";
        }

        $result = Db::getDb()->insert("players", [
            "username" => $username,
            "password" => $password,
            "name" => $name,
            "surname" => $surname
        ]);
        
        // fix temporaneo metto tutti nella stessa squadra
        $result = Db::getDb()->insert("groups_players", [
            "groupId" => 1,
            "players" => $username
        ]);
        
        //creo il contatore vittorie per il giocatore
        $result = Db::getDb()->insert("report_username_wins", [
            "username" => $username
        ]);
        
        $result = Db::getDb()->insert("report_username_played", [
            "username" => $username
        ]);
        
        
        return "User created!";
        
    }
    
    

    

}
