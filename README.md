# Battle

This is a game where two people can engage in a chess-like fight on a grid-based battlefield.

## Installation

### Composer

Download composer first:

``` bash
$ curl -sS https://getcomposer.org/installer | php
```

Or if you don't have curl:

``` bash
$ php -r "readfile('https://getcomposer.org/installer');" | php
```

### Dependencies

Install the dependencies using composer:

``` bash
$ php composer.phar install
```

## Run

Add the root folder of this project to your webserver configuration. If you are using Apache, configure a new VHost
and let the project run under a local URL, for example `battle.local`. You have to add this URL to your hosts file.
