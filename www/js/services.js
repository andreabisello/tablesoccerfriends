angular.module('starter.services', [])

.factory('Reports', function($http, User) {
  var Reports = {};
  Reports.activestPlayers = function(){
    var req =
    {
      method: 'GET',
      url: "http://api.tablesoccerfriends.com/reports/activestPlayers",
      headers: {'Content-Type': 'application/json','jwt':User.getJwt()}
    }
    //ritorna una promise
    return $http(req);
  };
  Reports.strongestPlayers = function(){
    var req =
    {
      method: 'GET',
      url: "http://api.tablesoccerfriends.com/reports/strongestPlayer",
      headers: {'Content-Type': 'application/json','jwt':User.getJwt()}
    }
    //ritorna una promise
    return $http(req);
  };

  return Reports;
})

.factory('Matches', function($http, User) {
  var Matches = {};
  Matches.storeMatch = function(dati){
    var req =
    {
      method: 'POST',
      url: "http://api.tablesoccerfriends.com/matches/save",
      data: JSON.stringify({"balls":dati.balls,
        "red_left":dati.red_left,
        "red_right":dati.red_right,
        "blue_left":dati.blue_left,
        "blue_right":dati.blue_right,
        "winner1":dati.winner1,
        "winner2":dati.winner2}),
      headers: {'Content-Type': 'application/json','jwt':User.getJwt()}
    }
    //ritorna una promise
    return $http(req);
  };

  return Matches;
})

.factory('User', function($http) {
  // Might use a resource here that returns a JSON array

  var User = {};
  console.log("da localstorage " + window.localStorage['jwt']);
  User.username = window.localStorage['username'] || "";
  User.password = window.localStorage['password'] || "";
  User.jwt = window.localStorage['jwt'] || "";

  User.login = function(user){
    var req =
    {
      method: 'POST',
      url: "http://api.tablesoccerfriends.com/auth/login",
      data: JSON.stringify({"username":user.username, "password":user.password}),
      headers: {'Content-Type': 'application/json'}
    }
    //ritorna una promise
    return $http(req);
  };

  User.register = function(user){
    console.log(user);
    var req =
    {
      method: 'POST',
      url: "http://api.tablesoccerfriends.com/users/create",
      data: JSON.stringify({"name":user.name,"surname":user.surname,"username":user.username, "password":user.password}),
      headers: {'Content-Type': 'text/html'}
    }
    //ritorna una promise
    return $http(req);
  };

  User.setUsername = function(username){
    console.log("Settato" + username);
    User.username = username;
    window.localStorage['username'] = username;
  };
  User.setPassword = function(password){
    User.password = password;
    window.localStorage['password'] = password;
  };
  User.setJwt = function(jwt){
    console.log("Settato" + jwt);
    User.jwt = jwt;
    window.localStorage['jwt'] = jwt;
  };
  User.getJwt = function(){
    return User.jwt;
  };
  User.getUsername = function(){
    return User.username;
  };
  User.getPassword = function(){
    return User.password;
  };

  return User;

})

.factory('Players', function($http, User) {
  console.log(User);
  console.log("userei " + User.getJwt());
  return {
    all: function(){
      var req =
      {
        method: 'GET',
        url: "http://api.tablesoccerfriends.com/groups/getPlayers/1",
        headers: {'Content-Type': 'application/json','jwt':User.getJwt()}
      }
      return $http(req);
    }
  };
})

.factory('Chats', function() {
  // Might use a resource here that returns a JSON array

  // Some fake testing data
  var chats = [{
    id: 0,
    name: 'Ben Sparrow',
    lastText: 'You on your way?',
    face: 'img/ben.png'
  }];

  return {
    all: function() {
      return chats;
    },
    remove: function(chat) {
      chats.splice(chats.indexOf(chat), 1);
    },
    get: function(chatId) {
      for (var i = 0; i < chats.length; i++) {
        if (chats[i].id === parseInt(chatId)) {
          return chats[i];
        }
      }
      return null;
    }
  };
});
