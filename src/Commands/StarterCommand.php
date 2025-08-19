<?php

namespace YukataRm\Laravel\Starter\Commands;

use YukataRm\Laravel\Command\ManageShellCommand;

use YukataRm\Laravel\Starter\Commands\PublishStubsCommand;
use YukataRm\Laravel\Auth\Commands\PublishStubsCommand as PublishAuthStubsCommand;
use YukataRm\Laravel\Exception\Commands\PublishStubsCommand as PublishExceptionStubsCommand;
use YukataRm\Laravel\Frontend\Commands\PublishStubsCommand as PublishFrontendStubsCommand;
use YukataRm\Laravel\Lang\Commands\PublishStubsCommand as PublishLangStubsCommand;
use YukataRm\Laravel\Logging\Commands\PublishStubsCommand as PublishLoggingStubsCommand;

use YukataRm\Laravel\Frontend\Commands\ManagePackageCommand as ManageFrontendPackageCommand;

/**
 * Starter Command
 *
 * @package YukataRm\Laravel\Starter\Commands
 */
class StarterCommand extends ManageShellCommand
{
    /**
     * command signature
     *
     * @var string
     */
    protected $signature = "starter {--appName=} {--dbName=}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Run starter commands uniformly";

    /*----------------------------------------*
     * Parameter
     *----------------------------------------*/

    /**
     * .env APP_NAME
     *
     * @var string|null
     */
    protected string|null $appName;

    /**
     * .env DB_DATABASE
     *
     * @var string|null
     */
    protected string|null $dbName;

    /**
     * set parameter
     *
     * @return void
     */
    protected function setParameter(): void
    {
        $this->appName = $this->option("appName");
        $this->dbName  = $this->option("dbName");
    }

    /*----------------------------------------*
     * Process
     *----------------------------------------*/

    /**
     * run command process
     *
     * @return array<mixed>
     */
    protected function process(): array
    {
        $this->publishStubs();
        $this->deleteUnnecessary();
        $this->managePackage();
        $this->formatEnv();
        $this->runArtisan();

        return [];
    }

    /*----------------------------------------*
     * Publish Stubs
     *----------------------------------------*/

    /**
     * call publish stubs commands
     *
     * @return void
     */
    protected function publishStubs(): void
    {
        $this->call(PublishAuthStubsCommand::class);
        $this->call(PublishExceptionStubsCommand::class);
        $this->call(PublishFrontendStubsCommand::class);
        $this->call(PublishLangStubsCommand::class);
        $this->call(PublishLoggingStubsCommand::class);

        $this->call(PublishStubsCommand::class);
    }

    /*----------------------------------------*
     * Delete Unnecessary
     *----------------------------------------*/

    /**
     * delete unnecessary
     *
     * @return void
     */
    protected function deleteUnnecessary(): void
    {
        $deletes = [
            "app/Http/Controllers/Controller.php" => "file",

            "config/auth.php"         => "file",
            "config/broadcasting.php" => "file",
            "config/cache.php"        => "file",
            "config/concurency.php"   => "file",
            "config/cors.php"         => "file",
            "config/database.php"     => "file",
            "config/filesystems.php"  => "file",
            "config/hashing.php"      => "file",
            "config/mail.php"         => "file",
            "config/queue.php"        => "file",
            "config/services.php"     => "file",
            "config/session.php"      => "file",
            "config/view.php"         => "file",

            "resources/views/welcome.blade.php" => "file",
            "resources/css"                     => "directory",
            "resources/js"                      => "directory",

            "postcss.config.js"  => "file",
            "tailwind.config.js" => "file",
            "webpack.mix.js"     => "file",
        ];

        foreach ($deletes as $path => $type) {
            if ($type === "file") {
                $this->deleteUnnecessaryFile($path);
            } elseif ($type === "directory") {
                $this->deleteUnnecessaryDirectory($path);
            }
        }
    }

    /**
     * delete unnecessary file
     *
     * @param string $filePath
     * @return void
     */
    protected function deleteUnnecessaryFile(string $filePath): void
    {
        $path = base_path($filePath);

        if (!file_exists($path)) return;

        $message = sprintf("Delete file [%s]", str_replace(base_path() . "/", "", $path));

        $this->task($message, function () use ($path) {
            return unlink($path);
        });
    }

    /**
     * delete unnecessary directory
     *
     * @param string $directoryPath
     * @return void
     */
    protected function deleteUnnecessaryDirectory(string $directoryPath): void
    {
        $path = base_path($directoryPath);

        if (!is_dir($path)) return;

        $message = sprintf("Delete directory [%s]", str_replace(base_path() . "/", "", $path));

        $this->task($message, function () use ($path) {
            $deleteRecursive = function ($path) use (&$deleteRecursive) {
                if (!is_dir($path)) return unlink($path);

                $items = scandir($path);
                if ($items === false) return false;

                foreach ($items as $item) {
                    if ($item === "." || $item === "..") continue;

                    $itemPath = $path . DIRECTORY_SEPARATOR . $item;

                    if (is_dir($itemPath)) {
                        if (!$deleteRecursive($itemPath)) return false;
                    } else {
                        if (!unlink($itemPath)) return false;
                    }
                }
                return rmdir($path);
            };

            return $deleteRecursive($path);
        });
    }

    /*----------------------------------------*
     * Manage Package
     *----------------------------------------*/

    /**
     * manage package command
     *
     * @return void
     */
    protected function managePackage(): void
    {
        $this->call(ManageFrontendPackageCommand::class);

        // composer
        $this->outputInfo("Manage Composer packages.");

        $this->composerRequire("barryvdh/laravel-debugbar", true);
        $this->composerRequire("beyondcode/laravel-query-detector", true);
        $this->composerRequire("squizlabs/php_codesniffer", true);
        $this->composerRequire("larastan/larastan", true);
        $this->composerDumpAutoload();

        // npm
        $this->outputInfo("Manage NPM packages.");

        $this->npmUninstall("postcss");
        $this->npmUninstall("tailwindcss");
        $this->npmInstall();
        $this->npmRun("build");
    }

    /*----------------------------------------*
     * Format Env
     *----------------------------------------*/

    /**
     * whether copy .env.example to .env
     *
     * @var bool
     */
    protected bool $copyEnv = false;

    /**
     * format env
     *
     * @return void
     */
    protected function formatEnv(): void
    {
        $this->outputInfo("Formatting .env file.");

        $this->formatAppName();
        $this->formatDbDatabase();

        if ($this->copyEnv) {
            $this->task("Copy .env.example to .env", function () {
                return copy(base_path(".env.example"), base_path(".env"));
            });
        }
    }

    /**
     * format APP_NAME
     *
     * @return void
     */
    protected function formatAppName(): void
    {
        if (is_null($this->appName)) return;

        $appName = $this->appName;

        $this->task("Format APP_NAME", function () use ($appName) {
            $path = base_path(".env.example");

            return file_put_contents($path, preg_replace(
                "/^APP_NAME=\"[^\"]+\"/",
                "APP_NAME=\"{$appName}\"",
                file_get_contents($path)
            ));
        });

        $this->copyEnv = true;
    }

    /**
     * format DB_DATABASE
     *
     * @return void
     */
    protected function formatDbDatabase(): void
    {
        if (is_null($this->dbName)) return;

        $dbName = $this->dbName;

        $this->task("Format DB_DATABASE", function () use ($dbName) {
            $path = base_path(".env.example");

            return file_put_contents($path, preg_replace(
                "/DB_DATABASE=[^\n]+/",
                "DB_DATABASE={$dbName}",
                file_get_contents($path)
            ));
        });

        $this->copyEnv = true;
    }

    /*----------------------------------------*
     * Run Artisan
     *----------------------------------------*/

    /**
     * run artisan command
     *
     * @return void
     */
    protected function runArtisan(): void
    {
        $this->outputInfo("Run Artisan Command");

        $this->artisanKeyGenerate();
        $this->artisanStorageLink();
        $this->artisanMigrate();
    }
}
