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

use Symfony\Component\Console\Command\Command;
use Xabbuh\PandaBundle\Command\ChangeUrlCommand;
use Xabbuh\PandaClient\Model\Notifications;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ChangeUrlCommandTest extends CloudCommandTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->apiMethod = 'setNotifications';
    }

    public function testCommand()
    {
        $notifications = new Notifications();
        $notifications->setUrl('http://example.com/notify');
        $this->defaultCloud
            ->expects($this->once())
            ->method('setNotifications')
            ->with($this->equalTo($notifications));
        $this->runCommand(
            'panda:notifications:change-url',
            array('url' => 'http://example.com/notify')
        );
    }

    public function testCommandWithoutArguments()
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Not enough arguments');

        $this->runCommand('panda:notifications:change-url');
    }

    protected function createCommand(): Command
    {
        return new ChangeUrlCommand($this->cloudManager);
    }

    protected function getDefaultCommandArguments()
    {
        return array('url' => 'http://example.com/notify');
    }
}
