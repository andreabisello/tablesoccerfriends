<?php

require_once 'Db.php';
require_once __DIR__  . '/../lib/jwt/autoload.php';
require_once __DIR__ . '/../Config/jwtSecret.php';
require_once 'Caller.php';

/**
* Description of Players
*
* @author Computer
*/
class Companies {

  private $userCalling = null;

  function __construct($userCalling) {
    $this->userCalling = $userCalling;
  }
  /**
  * Returns players of a company
  * @param type $company
  */
  public function getPlayers($company){
    //mi chiedi i giocatori di una compagnia, ma ci appartieni?
    
    $companyACuiAppartiene = Db::getDb()->select("companies_players",["company"],["players" => $this->userCalling->getUsername()]);

    $appartiene = false;

    foreach ($companyACuiAppartiene as $thisCompany){
      if($thisCompany["company"] == $company){
        $appartiene = true;
      }
    }

    if($appartiene){
      $players = Db::getDb()->select(
      "companies_players",
      ["[><]players" => ["players" => "username"]],
      ["username","name","surname","avatar"],
      ["company" => $company]
    );
    return $players;
  } else {
    return "Non appartieni a questa squadra!";
  }

}
public function retrieveCompanies(){
  $companiesOwned = Db::getDb()->select("companies",["id","name"],["owner" => $this->userCalling->username]);

  $companiesJoined = Db::getDb()->select("companies",
  ["[><]companies_players" => ["owner" => "players"]],
  ["id","name"],
  ["owner" => $this->userCalling->username]);


  $response = ["Status" => "OK",
  "companiesOwned" => $companiesOwned,
  "companiesJoined" => $companiesJoined];

  return $response;
}

public function create() {
  $data = json_decode(file_get_contents('php://input'), true);
  $name = $data["name"];

  $owner = $this->username;

  $result = Db::getDb()->insert("companies", [
    "name" => $name,
    "owner" => $owner
  ]);

  $message = "Companies Created!";

}

public function addPlayerToCompanies() {

  $data = json_decode(file_get_contents('php://input'), true);

  $username = $data["username"];
  $company = $data["company"];

  if (Db::getDb()->has("companies_players", ["AND" =>
  [
    "company" => $company,
    "username" => $username
  ]
])
) {


} else {

  $result = parent::getDb()->insert("companies_players", [
    "company" => $company,
    "username" => $username
  ]);

  $message = $username + " joined the companies!";

}
}

public function removePlayerFromCompanies() {

  $data = json_decode(file_get_contents('php://input'), true);

  $username = $data["username"];
  $company = $data["company"];

  if (Db::getDb()->has("companies_players", ["AND" =>
  [
    "company" => $company,
    "username" => $username
  ]
])
) {

  $result = parent::getDb()->delete("companies_players", [
    "company" => $company,
    "username" => $username
  ]);

  $message = $username + " removed from companies!";


} else {


}
}

}
