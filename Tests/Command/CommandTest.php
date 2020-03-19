<?php

/*
 * This file is part of the XabbuhPandaBundle package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\PandaBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Xabbuh\PandaClient\Exception\ApiException;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
abstract class CommandTest extends TestCase
{
    use SetUpTearDownTrait;

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var \Symfony\Component\Console\Command\Command
     */
    protected $command;

    /**
     * @var CommandTester
     */
    protected $commandTester;

    private function doSetUp()
    {
        $this->command = $this->createCommand();

        $this->application = new Application();
        $this->application->add($this->command);
    }

    abstract protected function createCommand(): Command;

    protected function runCommand($commandName, array $arguments = array())
    {
        $command = $this->application->find($commandName);
        $this->commandTester = new CommandTester($command);
        $input = array_merge($arguments, array('command' => $command->getName()));
        $this->commandTester->execute($input);
    }

    /**
     * @return \Xabbuh\PandaClient\Exception\ApiException
     */
    protected function createApiException()
    {
        return new ApiException();
    }
}
