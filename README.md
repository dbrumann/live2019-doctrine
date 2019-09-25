TO DO LIST
==========

This is a demo application.

Provides the following features:

* Quickly register users without email confirmation
* Log in with a previously registered user (email and password)
* Dashboard shows all tasks lists
  * New lists can be created
  * Existing lists can be archived [todo]
  * Displayed lists can be filtered based on status (archived/active) [todo]
* Detail view for lists showing their tasks
  * New tasks can be added to the list
  * Tasks can be closed and reopened

Installation (development)
--------------------------

1. Clone repository

    ```bash
    git clone https://github.com/dbrumann/todo-app.git
    ```

1. Install dependencies

    ```bash
   composer install 
   ``` 

1. Start database

    ```bash
    docker-compose up -d
    ```

1. Run tests (also sets up fixtures!)

    ```bash
    php bin/phpunit
    ```

1. Start local web server

    ```bash
    symfony local:proxy:start
    symfony local:proxy:domain:attach todo-app
    symfony local:server:start --daemon
    ```

1. Open app in browser: [todo-app.wip](http://todo-app.wip)

Known issues
------------

**Database port is already in use**

You can modify the assigned port by changing the `docker-compose.yaml` or
providing a `docker-compose.override.yaml` that will not be committed with
your remaining changes.

**Using a local database instead of a service**

When using a local database make sure to provide a proper `DATABASE_URL` env var or update the default value in
`.env`. You might also want to adjust the `config/packages/doctrine.yaml` in case you are using a different server
version, e.g. MySQL 5.7 or MariaDB.
