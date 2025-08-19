<?php

namespace YukataRm\Laravel\Starter\Commands;

use YukataRm\Laravel\Command\PublishStubsCommand as BaseCommand;

/**
 * Publish Stubs Command
 *
 * @package YukataRm\Laravel\Starter\Commands
 */
class PublishStubsCommand extends BaseCommand
{
    /**
     * command signature
     *
     * @var string
     */
    protected $signature = "starter:publish";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Publish starter resources";

    /*----------------------------------------*
     * Parameter
     *----------------------------------------*/

    /**
     * set parameter
     *
     * @return void
     */
    protected function setParameter(): void {}

    /*----------------------------------------*
     * Process
     *----------------------------------------*/

    /**
     * assets name
     *
     * @var string
     */
    protected string $assetsName = "starter";

    /**
     * stubs directory path
     *
     * @var string
     */
    protected string $stubsDirectory = __DIR__ . "/../../stubs";
}
