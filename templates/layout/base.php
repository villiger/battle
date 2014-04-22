<!DOCTYPE html>
<html lang="en" ng-app="battleApp">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Battle Chess</title>
    <link rel="stylesheet" href="/css/style.css">
    <script src="/js/jquery-1.11.0.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/angular.min.js"></script>
    <script src="/js/angular-route.min.js"></script>
    <script src="/app/js/app.js"></script>
</head>
<body>
    <div class="main container">
        <div class="row">
            <div class="main-content col-lg-12">
                <nav class="navbar navbar-default" role="navigation">
                    <div class="container-fluid">
                        <!-- Brand and toggle get grouped for better mobile display -->
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                            <a class="navbar-brand" href="#">BattleChess!</a>
                        </div>
                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                          <ul class="nav navbar-nav">
                            <li><a href="#/about">About</a></li>
                            <li><a href="#/mygames">My Games</a></li>
                            <li><a href="#/friends">Friends</a></li>
                          </ul>
                          <ul class="nav navbar-nav navbar-right">
                            <li><a href="#/logout">Register</a></li>
                            <li><a href="#/login">Login</a></li>
                          </ul>
                        </div><!-- /.navbar-collapse -->
                    </div><!-- /.container-fluid -->
                </nav>
                <?= $yield ?>
            </div>
        </div>
    </div>
</body>
</html>
