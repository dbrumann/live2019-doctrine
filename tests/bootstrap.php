<?php

require __DIR__ . '/../config/bootstrap.php';

if ($_SERVER['BOOTSTRAP_CLEAR_CACHE']) {
    passthru(sprintf(
        'APP_ENV="%s" php "%s/../bin/console" cache:clear --no-interaction',
        $_SERVER['BOOTSTRAP_CLEAR_CACHE'],
        __DIR__
    ));
}

if ($_SERVER['BOOTSTRAP_CLEAR_DATABASE']) {
    passthru(sprintf(
        'APP_ENV="%s" php "%s/../bin/console" doctrine:database:drop --force --no-interaction',
        $_SERVER['BOOTSTRAP_CLEAR_DATABASE'],
        __DIR__
    ));

    passthru(sprintf(
        'APP_ENV="%s" php "%s/../bin/console" doctrine:database:create --if-not-exists --no-interaction',
        $_SERVER['BOOTSTRAP_CLEAR_DATABASE'],
        __DIR__
    ));

    passthru(sprintf(
        'APP_ENV="%s" php "%s/../bin/console" doctrine:schema:update --force --no-interaction',
        $_SERVER['BOOTSTRAP_CLEAR_DATABASE'],
        __DIR__
    ));
}
