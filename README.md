# Legacy PHP web application boilerplate

PHP web application boilerplate designed to work with PHP version >= [5.5.9](http://php.net/supported-versions.php).
There is also older version of this [project](https://github.com/rikniitt/legacy-php-boilerplate/tree/php-5.3),
which requires PHP version >= 5.3.3.
Boilerplate utilizes following [composer](https://getcomposer.org/doc/) [packages](https://packagist.org/):

#### Dependencies
 * [Silex](http://silex.sensiolabs.org/documentation) PHP micro-framework.
 * [Doctrine](http://www.doctrine-project.org/) as [database abstraction layer](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/) and [ORM](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/).
 * [Valitron](https://github.com/vlucas/valitron) as validation library.
 * [Twig](http://twig.sensiolabs.org/documentation) as template engine.
 * [Monolog](https://github.com/Seldaek/monolog) for logging.
 * [m1/env](https://github.com/m1/Env) for loading config files.
 * [Carbon](https://github.com/briannesbitt/Carbon/tree/1.33.0) for more fluent datetime [API](https://carbon.nesbot.com/docs/)
 * [Twitter Bootstrap](http://getbootstrap.com/css/) frontend framework.

#### Development dependencies
 * [Robo](http://robo.li/getting-started/) as task runner.
 * [PHPUnit](https://phpunit.de/manual/current/en/phpunit-book.html) as test suite.
 * [CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer/wiki) for checking coding style.
 * [PsySH](https://github.com/bobthecow/psysh) as PHP REPL.
 * [Whoops](https://github.com/filp/whoops/blob/master/docs/API%20Documentation.md) for displaying pretty errors.


## Install
  * You will need PHP, Apache and MySql.
  * Download composer.phar with `curl -sS https://getcomposer.org/installer | php`.
  * Install dependencies `php composer.phar install`.
  * Install project `./robo install`.

  Or if you are using [Vagrant](https://www.vagrantup.com/downloads.html), just

  * `vagrant up`


## Configuration

Local settings are stored in *config/config.file*. This file is created by
`./robo install`. Setup your local database credentials and other project
settings. They can be accessed via Legacy\Application instance method getSetting($key).


## Migrations

Database migrations are located in *config/migrations*-directory as sql files. You can create
new migration file with `./robo migration:create`. Migrations can be inserted
to your database with `./robo migration:run`.


## Tests

Tests are located in *tests*-folder. Tests can be executed with `./robo test`
or `./vendor/bin/phpunit`.


## Helper scripts

  * `./robo app:console` run PHP REPL with your application bootstrapped. Access your application via $app variable.
  * `./robo db:console` open mysql console.
  * `./robo serve [port_number=8000]` start development server.
  * `./robo` list robo tasks.


## Frontend dependencies

Frontend dependencies can be 'published' with `./robo assets:publish`. This will copy bootstrap from *vendor*-dir to
*public/assets/vendor*-directory.
