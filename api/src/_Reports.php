<?php

require_once 'Db.php';
require_once __DIR__  . '/../lib/jwt/autoload.php';
require_once __DIR__ . '/../Config/jwtSecret.php';

use \Firebase\JWT\JWT;


class Reports {
    
    private $userCalling = null;
    
    function __construct($userCalling) {
       $this->userCalling = $userCalling;
    }
    
    /**
     * quante partite sono state giocate
     */
    public function activestPlayers(){
        
        $count = Db::getDb()->select("report_username_played", ["username","played"], ["ORDER" => "played DESC", "LIMIT"=>5]);
        return $count;
    }
    
    /**
     * il giocatore che ha vinto più partite
     */
    public function strongestPlayer(){
        $count = Db::getDb()->select("report_username_wins", ["username","wins"], ["ORDER" => "wins DESC", "LIMIT"=>5]);
        return $count; 
    }
    
    /**
     * la coppia che ha vinto più partite
     */
    public function strongestGroup(){
        
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
        
        
        return "User created!";
        
    }
    
    

    

}
