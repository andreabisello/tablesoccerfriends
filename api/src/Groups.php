<?php

require_once 'Db.php';
require_once __DIR__ . '/../lib/jwt/autoload.php';
require_once __DIR__ . '/../Config/jwtSecret.php';
require_once 'Caller.php';

/**
 * Description of Players
 *
 * @author Computer
 */
class Groups {

    private $userCalling = null;

    function __construct($userCalling) {
        $this->userCalling = $userCalling;
    }

    /**
     * Returns players of a company
     * @param type $company
     */
    public function getPlayers($groupId) {
        //mi chiedi i giocatori di una compagnia, ma ci appartieni?

        $groupsACuiAppartiene = Db::getDb()->select("groups_players", ["groupId"], ["players" => $this->userCalling->getUsername()]);
        
        $appartiene = false;
        
        foreach ($groupsACuiAppartiene as $thisGroup) {

            if ($thisGroup["groupId"] == $groupId) {
                $appartiene = true;
            }
            
        }

        if ($appartiene) {
            $players = Db::getDb()->select(
                    "groups_players", ["[><]players" => ["players" => "username"]], ["username", "name", "surname", "avatar"], ["groupId" => $groupId]
            );
            return $players;
        } else {
            return "Non appartieni a questa squadra!";
        }
    }

    public function getGroups() {
        $groupsJoined = Db::getDb()->select("groups", 
        ["[><]groups_players" => ["id" => "groupId"]], 
        ["id", "name"], 
        ["players" => $this->userCalling->getUsername()]);
        
        //return Db::getDb()->last_query();
        
        return $groupsJoined;
    }

    public function create() {
        $data = json_decode(file_get_contents('php://input'), true);
        $name = $data["name"];

        $owner = $this->username;

        $result = Db::getDb()->insert("groups", [
            "name" => $name,
            "owner" => $owner
        ]);

        $message = "group Created!";
    }

    public function addPlayerToCompanies() {

        $data = json_decode(file_get_contents('php://input'), true);

        $username = $data["username"];
        $group = $data["group"];

        if (Db::getDb()->has("groups_players", ["AND" =>
                    [
                        "group" => $group,
                        "username" => $username
                    ]
                ])
        ) {
            
        } else {

            $result = parent::getDb()->insert("groups_players", [
                "group" => $group,
                "username" => $username
            ]);

            $message = $username + " joined the group!";
        }
    }

    public function removePlayerFromGroup() {

        $data = json_decode(file_get_contents('php://input'), true);

        $username = $data["username"];
        $group = $data["group"];

        if (Db::getDb()->has("groups_players", ["AND" =>
                    [
                        "group" => $group,
                        "username" => $username
                    ]
                ])
        ) {

            $result = parent::getDb()->delete("groups_players", [
                "group" => $group,
                "username" => $username
            ]);

            $message = $username + " removed from group!";
        } else {
            
        }
    }

}
