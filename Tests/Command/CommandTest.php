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
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Xabbuh\PandaClient\Exception\ApiException;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
abstract class CommandTest extends TestCase
{
    /**
     * @var \Symfony\Component\Console\Command\Command
     */
    protected $command;

    /**
     * @var CommandTester
     */
    protected $commandTester;

    protected function runCommand($commandName, array $arguments = array())
    {
        $application = new Application();

        if (null !== $this->command) {
            $application->add($this->command);
        }

        $command = $application->find($commandName);
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
