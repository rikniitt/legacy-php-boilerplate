# Legacy PHP web application boilerplate

PHP web application boilerplate designed to work with PHP version >= [5.3.3](http://php.net/supported-versions.php).
It utilizes following [composer](https://getcomposer.org/doc/) [packages](https://packagist.org/):

#### Dependencies
 * [Silex](http://silex.sensiolabs.org/documentation) PHP micro-framework.
 * [Doctrine](http://www.doctrine-project.org/) as [database abstraction layer](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/) and [ORM](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/).
 * [Twig](http://twig.sensiolabs.org/documentation) as template engine.
 * [Monolog](https://github.com/Seldaek/monolog) for logging.
 * [PHP Dotenv](https://github.com/josegonzalez/php-dotenv) for loading config files.
 * [Twitter Bootstrap](http://getbootstrap.com/css/) frontend framework.

#### Development dependencies
 * [Phing](http://www.phing.info/trac/wiki/Users/Documentation) as task runner.
 * [PHPUnit](https://phpunit.de/manual/current/en/phpunit-book.html) as test suite.
 * [CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer/wiki) for checkng coding style.
 * [PsySH](https://github.com/bobthecow/psysh) as PHP REPL.


## Install

  * You will need PHP, Apache and MySql.
  * Download composer.phar with `curl -sS https://getcomposer.org/installer | php`.
  * Install dependencies `php composer.phar install`.
  * Install project `./phing install`.


## Configuration

Local settings are stored in *config/config.file*. This file is created by 
`./phing install`. Setup your local database credentials and other project 
settings. They can be accessed via Legacy\Application instance method getSetting($key).
These settings are also used by phing build.xml and helper scripts.


## Migrations

Migrations are located in *config/migrations*-directory as sql files. You can create
new migration file with `./phing migration-create`. Migrations can be inserted
to your database with `./phing migration-run-all`. Note that this script won't
keep track which migrations have been inserted, so everytime you run migrations,
they will be inserted in creation time order from the oldest to newest.


## Tests

Tests are located in *tests*-folder. Tests can be executed with `./phing test`
or `./vendor/bin/phpunit`. 


## Helper scripts

  * `./app_console` run PHP REPL with your application bootstrapped. Access your application via $app variable.
  * `./db_console` open mysql console.
  * `./serve [port_number=8000]` start development server (requires php>=5.4.0).

## Frontend dependencies

Frontend dependencies can be 'published' with `./phing assets-publish`. This will copy bootstrap from *vendor*-dir to
*public/assets/vendor*-directory. 
