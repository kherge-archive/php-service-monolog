Monolog Service Provider
========================

[![Build Status](https://travis-ci.org/herrera-io/php-service-monolog.png?branch=master)](https://travis-ci.org/herrera-io/php-service-monolog)

A service provider for Monolog.

Summary
-------

Integrates the Monolog library into the [Herrera.io service container](https://github.com/herrera-io/php-service-container).

Installation
------------

Add it to your list of Composer dependencies:

```sh
$ composer require herrera-io/service-monolog=1.*
```

Usage
-----

```php
<?php

use Herrera\Service\Container;
use Herrera\Service\Monolog\MonologServiceProvider;

$container = new Container();
$container->register(new MonologServiceProvider(), array(
    'monolog.options' => array(
        'default' => array(
            'handler.stream' => STDOUT
        )
    )
));

$container['monolog']->debug('A debug message!');
```

Running it:

```sh
$ php script.php
[2013-03-01 22:41:44] default.DEBUG: A debug message! [] []
```