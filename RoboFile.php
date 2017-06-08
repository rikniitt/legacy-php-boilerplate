<?php

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
    private $app;

    public function __construct()
    {
        $this->app = require_once __DIR__ . '/config/bootstrap.php';
    }

    /**
     * Run PsySH PHP REPL.
     */
    public function appConsole()
    {
        $this->say('Starting PsySH.');
        $this->say('Use $app to access the Legacy\Application.');

        $cmd = 'php '
             . ROOT_DIR
             . '/vendor/bin/psysh '
             . ROOT_DIR
             . '/config/bootstrap.php';

        $this->io()->comment($cmd);

        // Symfony process is dependency of Robo
        $process = new Symfony\Component\Process\Process($cmd);
        $process->setTty(true);
        $process->run();
    }

     /**
     * Install project
     */
    public function appInstall()
    {
        $this->taskFilesystemStack()
             ->copy(
                 ROOT_DIR . '/config/config.file.example',
                 ROOT_DIR . '/config/config.file'
             )
             ->run();

        $this->assetsPublish();
    }

    /**
     * Rename application namespace
     */
    public function appRename()
    {
        $this->io()->warning('You should check the results manually after this...');

        $current = str_replace('\\Application', '', get_class($this->app));
        $this->say('Current name is ' . $current);

        $new = $this->ask('New namespace');

        if (!$this->confirm('Change it to ' . $new . '?')) {
            $this->say('Doing nothing.');
            return 0;
        }

        $files = array_merge(
            $this->glob(ROOT_DIR . '/src/*.php'),
            $this->glob(ROOT_DIR . '/tests/*.php'),
            $this->glob(ROOT_DIR . '/config/*.php')
        );

        foreach ($files as $file) {
            $this->taskReplaceInFile($file)
                 ->from($current)
                 ->to($new)
                 ->run();
        }

        $nspaceToPath = function ($str) {
            return str_replace('\\', '/', $str);
        };

        $this->taskFilesystemStack()
             ->rename(
                 ROOT_DIR . '/src/' . $nspaceToPath($current),
                 ROOT_DIR . '/src/' . $nspaceToPath($new)
             )
             ->run();

        $this->taskComposerDumpAutoload()->run();

        $this->taskReplaceInFile(__FILE__)
             ->from($current)
             ->to($new)
             ->run();
    }

     /**
     * Print application routes
     */
    public function appRoutes()
    {
        $app = $this->app;
        require ROOT_DIR . '/config/routes.php';
        $app->flush();

        $headers = [
            'METHOD',
            'ROUTE',
            'CONTROLLER'
        ];
        $rows = [];

        foreach ($app['routes'] as $route) {
            foreach ($route->getMethods() as $method) {
                $defaults = $route->getDefaults();
                if (array_key_exists('_controller', $defaults)) {
                    if (is_string($defaults['_controller'])) {
                        $controller = $defaults['_controller'];
                    } else {
                        $controller = 'PHP Closure';
                    }
                } else {
                    $controller = 'unknown';
                }

                $rows[] = [
                    $method,
                    $route->getPath(),
                    $controller
                ];
            }
        }

        $this->io()->table($headers, $rows);
    }

    /**
     * Archives all log files older than this month
     *
     * @param string $month Optional month in format Ym.
     */
    public function archivelogs($month = null)
    {
        $logsDir = ROOT_DIR . '/logs';

        if ($month !== null) {
            $logFiles = $this->glob($logsDir . '/*' . $month . '*.log');
            $this->zipFiles($logsDir, $month, $logFiles);
        } else {
            $exclude = date('Ym');
            $months = [];

            foreach ($this->glob($logsDir . '/*.log') as $f) {
                preg_match('/([0-9]{6})/', basename($f), $captured);

                if (count($captured) === 2
                    && $captured[1] !== $exclude
                    && !in_array($captured[1], $months)) {
                    $months[] = $captured[1];
                }
            }

            if (!$months) {
                $this->say('No log files found');
            }

            foreach ($months as $m) {
                $logFiles = $this->glob($logsDir . '/*' . $m . '*.log');
                $this->zipFiles($logsDir, $m, $logFiles);
            }
        }
    }

    /**
     * Publish assets installed via composer
     */
    public function assetsPublish()
    {
        $publicVendorDir = ROOT_DIR . '/public/assets/vendor';

        $this->say("Cleaning up $publicVendorDir directory.");
        $this->taskCleanDir([$publicVendorDir])->run();

        $this->say('Publishing jquery.');
        $this->taskCopyDir([
            ROOT_DIR . '/vendor/components/jquery' => $publicVendorDir. '/jquery/js'
        ])->run();

        $this->say('Publishing twitter bootstrap.');
        $this->taskCopyDir([
            ROOT_DIR . '/vendor/twbs/bootstrap/dist/css' => $publicVendorDir. '/bootstrap/css',
            ROOT_DIR . '/vendor/twbs/bootstrap/dist/fonts' => $publicVendorDir. '/bootstrap/fonts',
            ROOT_DIR . '/vendor/twbs/bootstrap/dist/js' => $publicVendorDir. '/bootstrap/js',
        ])->run();
    }

    /**
     * Build project
     */
    public function build()
    {
        $this->io()->error('Hey there. No one has not setup any build magic ... :(');
        $this->say('Run ./robo to see full list of tasks.');
    }

    /**
     * Find coding standard violations using PHP CodeSniffer
     */
    public function checkstyle()
    {
        $this->say('Running PHP CodeSniffer to check coding standard violations.');

        $this->taskExec(ROOT_DIR . '/vendor/bin/phpcs')
             ->arg('-s')
             ->arg('-p')
             ->option('standard', ROOT_DIR . '/phpcs.xml', '=')
             ->arg(ROOT_DIR . '/src')
             ->arg(ROOT_DIR . '/tests')
             ->arg(__FILE__)
             ->run();
    }

    /**
     * Compress source files to zip archive
     *
     * @param string $name Optional name to be included in build package.
     */
    public function compress($name = '')
    {
        $this->say('Compressing project to zip archive.');
        $this->say('  This takes a while....');

        $this->taskFilesystemStack()->mkdir(ROOT_DIR . '/build')->run();

        $package = ROOT_DIR . '/build/' . date('Ymd_His');
        if ($name) {
            $package .= '_' . $name;
        }

        $package .= '_build.zip';

        $this->taskPack($package)
             ->add('config/bootstrap.php')
             ->add('config/config.file.example')
             ->add('config/routes.php')
             ->add('config/services.php')
             ->add('logs/.gitkeep')
             ->add('public')
             ->add('src')
             ->add('vendor')
             ->add('views')
             ->add('README.md')
             ->run();
    }

    /**
     * Open MySQL console.
     */
    public function dbConsole()
    {
        $this->say('Opening MySQL console.');

        $cmd = 'mysql '
             . ' -h' . $this->app->getSetting('DB_HOST')
             . ' -u' . $this->app->getSetting('DB_USER')
             . ' -p' . $this->app->getSetting('DB_PASS')
             . ' --show-warnings'
             . ' ' . $this->app->getSetting('DB_NAME');

        // Symfony process is dependency of Robo
        $process = new Symfony\Component\Process\Process($cmd);
        $process->setTty(true);
        $process->run();
    }

    /**
     * Perform syntax check of sourcecode files with php lint
     */
    public function lint()
    {
        $this->say('Running php syntax check (lint) for source files.');

        $lint = $this->taskExecStack()->stopOnFail();

        $files = array_merge(
            $this->glob(ROOT_DIR . '/src/*.php'),
            $this->glob(ROOT_DIR . '/tests/*.php'),
            $this->glob(ROOT_DIR . '/config/*.php')
        );

        foreach ($files as $f) {
            $lint->exec('/usr/bin/php -l ' . $f);
        }

        $lint->run();
    }

    /**
     * Create new empty migration file
     */
    public function migrationCreate()
    {
        $name = $this->askDefault('Enter name for the migration', 'name_in_snake_case');
        $name = strtolower(str_replace(' ', '_', $name));

        $file = ROOT_DIR . '/config/migrations/' . date('Ymd_His') . '_' . $name . '.sql';
        $this->taskFilesystemStack()->touch($file)->run();

        $this->say('New empty migration file ' . $file . ' created.');
    }

    /**
     * Run all new migrations
     */
    public function migrationRun()
    {
        $timestampFile = ROOT_DIR . '/config/migrations/.lastmigrationrun';

        if (file_exists($timestampFile)) {
            $lastRun = intval(file_get_contents($timestampFile), 10);
        } else {
            $lastRun = 0;
        }

        $lastRunDate = DateTime::createFromFormat('U', $lastRun);

        $migrations = $this->glob(ROOT_DIR . '/config/migrations/*.sql');
        $needToRun = [];
        foreach ($migrations as $file) {
            $name = basename($file);
            preg_match('/([0-9]{8}_[0-9]{6})/', $file, $captured);

            if (count($captured) === 2) {
                $migarationDate = DateTime::createFromFormat('Ymd_His', $captured[0]);

                if ($migarationDate && $lastRunDate < $migarationDate) {
                    $needToRun[] = $file;
                }
            }
        }

        if (!$needToRun) {
            $this->say('No new migrations found.');
            return 0;
        }

        foreach ($needToRun as $file) {
            $this->taskExec('mysql')
                 ->option('host', $this->app->getSetting('DB_HOST'), '=')
                 ->option('user', $this->app->getSetting('DB_USER'), '=')
                 ->option('password', $this->app->getSetting('DB_PASS'), '=')
                 ->arg($this->app->getSetting('DB_NAME'))
                 ->rawArg('<')
                 ->arg($file)
                 ->run();
        }

        file_put_contents($timestampFile, time());
    }

    /**
     * Start development server
     */
    public function serve($port = 8000, $public = false)
    {
        $server = $this->taskServer($port);

        if ($public) {
            $server->host('0.0.0.0');
        } else {
            $server->host('localhost');
        }

        $server->dir(ROOT_DIR . '/public')->run();
    }

    /**
     * Run all unit tests with PHPUnit
     */
    public function test(array $args)
    {
        $this->taskExec(ROOT_DIR . '/vendor/bin/phpunit')
             ->option('configuration', ROOT_DIR . '/phpunit.xml')
             ->args($args)
             ->run();
    }

    // Some internal helpers -->

    private function glob($pattern, $exclude = null)
    {
        $files = glob($pattern);

        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            $files = array_merge($files, $this->glob($dir . '/' . basename($pattern), $exclude));
        }

        return array_filter($files, function($f) use ($exclude) {
            return $exclude === null || !preg_match($exclude, $f);
        });
    }

    private function zipFiles($dir, $month, $logFiles)
    {
        $zipFile = $dir . '/' . $month . '_logs.zip';
        $this->say("Compressing logs from $dir and $month to $zipFile");

        if (count($logFiles) === 0) {
            $this->say(' ... no log files found.');
            return;
        }

        $zip = new ZipArchive();
        if (!$zip->open($zipFile, ZIPARCHIVE::CREATE)) {
            $this->io()->error("Can\'t create archive file $zipFile!");
            return;
        }

        foreach ($logFiles as $f) {
            $this->say("Adding file $f");
            $zip->addFile($f, basename($f));
        }

        $zip->close();

        foreach ($logFiles as $f) {
            $this->say("Removing file $f");
            unlink($f);
        }
    }

}
