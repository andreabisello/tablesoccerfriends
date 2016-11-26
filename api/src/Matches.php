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
class Matches {

    private $userCalling = null;

    function __construct($userCalling) {
        file_put_contents("log.txt", "costruttore " . $userCalling->getUsername() );
       $this->userCalling = $userCalling;
    }
    
    public function lastPlayed(){
        $query = "SELECT report_username_played.username, played FROM report_username_played INNER JOIN players on report_username_played.username = players.username where report_attivi = 1 order by played desc limit 5";
        return Db::runQuery($query);
    }

    public function store() {
        file_put_contents("log.txt", "1");
        $data = json_decode(file_get_contents('php://input'), true);

        $balls = $data["balls"];
        $red_left = $data["red_left"];
        $red_right = $data["red_right"];
        $blue_left = $data["blue_left"];
        $blue_right = $data["blue_right"];
        $winner1 = $data["winner1"];
        $winner2 = $data["winner2"];
        file_put_contents("log.txt", $this->userCalling->getUsername());
        $result = Db::getDb()->insert("matches", [
            "reporter" => $this->userCalling->getUsername(),
            "red_left" => $red_left,
            "red_right" => $red_right,
            "blue_left" => $blue_left,
            "blue_right" => $blue_right,
            "winner1" => $winner1,
            "winner2" => $winner2
        ]);

        return Db::getDb()->last_query();
        
        //salvo le vittorie nel contatore vittorie
        $w1wins = Db::getDb()->update("report_username_wins", ["wins[+]" => 1],["username"=>$winner1]);
        $w2wins = Db::getDb()->update("report_username_wins", ["wins[+]" => 1],["username"=>$winner2]);

        //salvo le partite giocate
        $a = Db::getDb()->update("report_username_played", ["played[+]" => 1],["username"=>$red_left]);
        $b = Db::getDb()->update("report_username_played", ["played[+]" => 1],["username"=>$red_right]);
        $c = Db::getDb()->update("report_username_played", ["played[+]" => 1],["username"=>$blue_left]);
        $d = Db::getDb()->update("report_username_played", ["played[+]" => 1],["username"=>$blue_right]);

        if($result != 0){
            return true;
        } else {
            return false;
        }



    }

}