<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Battle Chess</title>
    <link rel="stylesheet" href="/css/style.css">
    <script src="/js/lib/jquery.min.js"></script>
    <script src="/js/lib/bootstrap.min.js"></script>
    <script src="/js/util.js"></script>
    <script src="/js/game.js"></script>
    <script>
        // eliminate the #_=_ by Facebook
        if (window.location.hash && window.location.hash === "#_=_") {
            window.history.replaceState("", document.title, window.location.pathname);
        }
    </script>
</head>
<body>

    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/games">Battle Chess</a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="/games">Games</a></li>
                    <li><a href="/friends">Friends</a></li>
                    <li><a href="/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main container">
        <div class="row">
            <div class="main-content col-xs-12 col-lg-12">
                <?= $yield ?>
            </div>
        </div>
    </div>

</body>
</html>
