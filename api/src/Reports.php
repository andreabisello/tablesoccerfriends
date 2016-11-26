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
        $query = "SELECT report_username_played.username, played FROM report_username_played INNER JOIN players on report_username_played.username = players.username where report_attivi = 1 order by played desc limit 5";
        return Db::runQuery($query);
    }
    
    /**
     * il giocatore che ha vinto più partite
     */
    public function strongestPlayer(){
        $query = "SELECT report_username_wins.username,wins FROM report_username_wins INNER JOIN players on report_username_wins.username = players.username where report_attivi = 1 order by wins desc limit 5";
        return Db::runQuery($query);
        /*
        $connection = Db::db_connect();
        if($connection === false) {
            file_put_contents("log.txt", mysqli_connect_error());
        }
        $result = mysqli_query($connection, "SELECT report_username_wins.username,wins FROM report_username_wins INNER JOIN players on report_username_wins.username = players.username where report_attivi = 1 order by wins desc limit 5");
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
        */
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
