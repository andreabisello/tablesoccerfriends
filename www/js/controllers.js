angular.module('starter.controllers', ["chart.js"])

.controller('ReportCtrl', function($scope, $state, $ionicLoading, Reports) {

  //$scope.labels = [];
  //$scope.data = [];

  $ionicLoading.show({
    template: 'Loading activest players ...'
  });

  Reports.activestPlayers().then(function(data){
    var activest = data.data;
    var tmpData = [];
    var tmpLabels = [];
    for (var i=0; i < activest.length; i++){
      giocatore = activest[i];
      tmpData.push(giocatore.played);
      tmpLabels.push(giocatore.username);
    }
    $scope.a_p_labels = tmpLabels;
    $scope.a_p_data = [tmpData];
    $ionicLoading.hide();
  }, function (data){
      console.log("strongestPlayers error" + data)
      alert("Please Login");
      $state.go('tab.account');
      $ionicLoading.hide();
  });

  $ionicLoading.show({
    template: 'Loading strongest players ...'
  });

  Reports.strongestPlayers().then(function(data){
    var activest = data.data;
    var tmpData = [];
    var tmpLabels = [];
    for (var i=0; i < activest.length; i++){
      giocatore = activest[i];
      tmpData.push(giocatore.wins);
      tmpLabels.push(giocatore.username);
    }
    $scope.s_p_labels = tmpLabels;
    $scope.s_p_data = [tmpData];
    $ionicLoading.hide();
}, function (data){
    console.log("strongestPlayers error" + data)
    $state.go('tab.account');
    $ionicLoading.hide();
});


})

.controller('DashCtrl', function($scope) {})

.controller('TabsCtrl', function($scope, User) {
    $scope.reportEnabled = function(){
        return false;
    }
    $scope.matchesEnabled = function(){
        return false;
    }
})

.controller('MatchesCtrl', function($scope, $state, $ionicLoading, Players, Matches) {
  console.log("matches e players");

  $scope.players = [];
  $scope.attaccanteRosso = "";
  $scope.portiereRosso = "";
  $scope.attaccanteBlu = "";
  $scope.portiereBlu = "";

  $ionicLoading.show({
    template: 'Loading Players...'
  });

  Players.all().then(function(data){
    $scope.players = data.data;
    console.log("players caricati");
    console.log($scope.players);
    $ionicLoading.hide();
  }, function (data){
      console.log("strongestPlayers error" + data)
      $state.go('tab.account');
      $ionicLoading.hide();
  });

  $scope.selectUpdated = function(optionSelected,who) {

    switch(who) {
      case 'portiereRosso':
      $scope.portiereRosso = optionSelected;
      break;
      case 'portiereBlu':
      $scope.portiereBlu = optionSelected;
      break;
      case 'attaccanteRosso':
      $scope.attaccanteRosso = optionSelected;
      break;
      case 'attaccanteBlu':
      $scope.attaccanteBlu = optionSelected;
      break;
    }

    console.log('portiereRosso' + $scope.portiereRosso);
    console.log('attaccanteRosso' + $scope.attaccanteRosso);
    console.log('portiereBlu' + $scope.portiereBlu);
    console.log('attaccanteBlu' + $scope.attaccanteBlu);

  };

  $scope.storeWin = function (color){
    console.log("storewin");
    console.log('portiereRosso' + $scope.portiereRosso);
    console.log('attaccanteRosso' + $scope.attaccanteRosso);
    console.log('portiereBlu' + $scope.portiereBlu);
    console.log('attaccanteBlu' + $scope.attaccanteBlu);

    $ionicLoading.show({
      template: 'Saving Matches...'
    });

    var matches = {};
    matches.balls = 7;
    matches.red_left = $scope.portiereRosso;
    matches.red_right = $scope.attaccanteRosso;
    matches.blue_left = $scope.portiereBlu;
    matches.blue_right = $scope.attaccanteBlu;

    if(color == "blue"){
      matches.winner1 = matches.blue_left;
      matches.winner2 = matches.blue_right;
    } else {
      matches.winner1 = matches.red_left;
      matches.winner2 = matches.red_right;
    }

    Matches.storeMatch(matches).then(function(data){
      $ionicLoading.hide();
      console.log(data)
    });

  }

})

.controller('ChatsCtrl', function($scope, Chats) {
  // With the new view caching in Ionic, Controllers are only called
  // when they are recreated or on app start, instead of every page change.
  // To listen for when this page is active (for example, to refresh data),
  // listen for the $ionicView.enter event:
  //
  //$scope.$on('$ionicView.enter', function(e) {
  //});

  $scope.chats = Chats.all();
  $scope.remove = function(chat) {
    Chats.remove(chat);
  };
})

.controller('ChatDetailCtrl', function($scope, $stateParams, Chats) {
  $scope.chat = Chats.get($stateParams.chatId);
})

.controller('AccountCtrl', function($scope, $state, $http, $ionicLoading, User) {

  //casomai recupero da localstorage
  $scope.user = {username:window.localStorage['username'] || "",password:window.localStorage['password'] || "", logged:false};

  $scope.signIn = function(user) {
    console.log('Sign-In', user);

    $ionicLoading.show({
      template: 'Log in ...'
    });

    User.login(user)
    .success(function(data, status, headers, config)
    {
      User.setJwt(data.jwt);
      User.setUsername(user.username);
      User.setPassword(user.password);
      $scope.user.logged = true;
      $state.go('tab.matches');
      $ionicLoading.hide();
    }).
    error(function(data, status, headers, config)
    {
      alert("Username o password errati");
      $scope.user.logged = false;
      $ionicLoading.hide();
    });

  };

  $scope.register = function(user) {
    console.log('registering', user);

    $ionicLoading.show({
      template: 'Creating Account ...'
    });

    User.register(user)
    .success(function(data, status, headers, config)
    {
      alert("User Created! now log in");
      $state.go('tab.account');
      $ionicLoading.hide();
    }).
    error(function(data, status, headers, config)
    {
      alert("User Creation Fails");
      $ionicLoading.hide();
    });

  };


  if($scope.user.username != "" && $scope.user.password != "" && $scope.user.logged == false){
    console.log("login automatica");
    $scope.signIn($scope.user);
  }
});
