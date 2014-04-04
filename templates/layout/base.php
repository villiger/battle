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
                <?= $yield ?>
            </div>
        </div>
    </div>
</body>
</html>
