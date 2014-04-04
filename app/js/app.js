var battleApp = angular.module('battleApp', [
  'ngRoute',
  //'LoginCtrl'
]);
 
battleApp.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
      when('/login', {
        templateUrl: 'app/partials/login.html',
        //controller: 'LoginCtrl'
      }).
      when('/logout', {
        templateUrl: 'app/partials/logout.html',
        //controller: 'PhoneDetailCtrl'
      }).
      otherwise({
        redirectTo: '/'
      });
  }]);



battleApp.controller('LoginCtrl', function ($scope, $http) {
  $scope.names = ['You', 'and', 'me'];

  $scope.login = function() {
    alert($scope.password);
  };
});