<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: Content-Type, jwt, Content-Range, Content-Disposition, Content-Description');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Expires: 0");
header("Pragma: no-cache");

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once 'lib/slim/autoload.php';

require_once 'src/Players.php';
require_once 'src/Groups.php';
require_once 'src/Matches.php';
require_once 'src/Caller.php';
require_once 'src/Auth.php';
require_once 'src/Reports.php';

$app = new Slim\App();
//eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6ImFuZHJlYS5iaXNlbGxvIiwicm9sZXMiOlsidXNlci1jcmVhdGUiLCJ1c2VyLWRlbGV0ZSJdfQ.PTqhNIzldf5Uz1p3FdAYt-PHOqiydm6fmCxU9DJp4h8
$app->post('/auth/login', function ($request, $response, $args) {
    $user = new Auth();
    $userData = $user->login();
    if($userData){
        $response->withJson($userData);
        $response->withHeader('Content-type', 'text/plain');
        return $response;
    } else {
        $response->write("Login Failed!");
        $response->withHeader('Content-type', 'text/plain');
        return $response->withStatus(403);
    }
});

$app->get('/groups/getGroups', function ($request, $response, $args) {
    $jwtHeader = $request->getHeaderLine('jwt');
    $userCalling = new Caller($jwtHeader);
    
    $q = new Groups($userCalling);
    $groups = $q->getGroups();
    return $response->withJson($groups);
});

$app->get('/groups/getPlayers/{groupId}', function ($request, $response, $args) {
    
    $groupId = $request->getAttribute('groupId');
    
    $jwtHeader = $request->getHeaderLine('jwt');
    $userCalling = new Caller($jwtHeader);
    
    $q = new Groups($userCalling);

    $players = $q->getPlayers($groupId);
    return $response->withJson($players);
});

$app->post('/groups/addPlayer', function ($request, $response, $args) {
    //dammi username e companies dove vuoi mettere username.
    //controllo se fai parte di quella companies
    $response->write("Da implementare!");
    return $response;
});

$app->post('/groups/removePlayer', function ($request, $response, $args) {
    //dammi username e companies per togliere username.
    //controllo se fai parte di quella companies
    $response->write("Da implementare!");
    return $response;
});

$app->post('/groups/create', function ($request, $response, $args) {
    //crea una companies.
    //controllo se fai parte di quella companies
    $response->write("Da implementare!");
    return $response;
});

//per adesso salvo tutto nella stessa companies
$app->post('/matches/save', function ($request, $response, $args) {
    
    $jwtHeader = $request->getHeaderLine('jwt');
    
    $userCalling = new Caller($jwtHeader);

    $match = new Matches($userCalling);
    if($match->store($userCalling)){
        return $response->write("match stored!");
    } else {
        return $response->write("error!")->withStatus(403);;
    };
});

/**
 * create an user. send a json with username and password keys.
 * questa api non richiede jwt
 */
$app->post('/users/create', function ($request, $response, $args) {
    

    $userAsking = new Players(null);
    $userCreated = $userAsking->create();

    if($userCreated == "User created!"){
        $response->write("User created!");
        $response->withHeader('Content-type', 'application/json');
        return $response;
    } else if ($userCreated == "Username Already Exists"){
        $response->write("Username Already Exists");
        $response->withHeader('Content-type', 'application/json');
        return $response->withStatus(403);
    } else {
        $response->write("Errore generico");
        $response->withHeader('Content-type', 'application/json');
        return $response->withStatus(403);
    }

});

$app->post('/users/delete', function ($request, $response, $args) {
    $response->write("Da implementare!");
    return $response;
});

$app->get('/', function ($request, $response, $args) {
    $response->write("Table Soccer Friends Api!");
    return $response;
});

$app->get('/reports/activestPlayers', function ($request, $response, $args) {
    $jwtHeader = $request->getHeaderLine('jwt');
    $userCalling = new Caller($jwtHeader);
    $q = new Reports($userCalling);
    
    $matches = $q->activestPlayers();
    return $response->withJson($matches);
});

$app->get('/reports/strongestPlayer', function ($request, $response, $args) {
    $jwtHeader = $request->getHeaderLine('jwt');
    $userCalling = new Caller($jwtHeader);
    $q = new Reports($userCalling);
    $matches = $q->strongestPlayer();
    return $response->withJson($matches);
});

$app->run();
