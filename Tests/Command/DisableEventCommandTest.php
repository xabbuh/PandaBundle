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

use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use Xabbuh\PandaBundle\Command\DisableEventCommand;
use Xabbuh\PandaClient\Model\NotificationEvent;
use Xabbuh\PandaClient\Model\Notifications;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * @group legacy
 */
class DisableEventCommandTest extends CloudCommandTest
{
    use SetUpTearDownTrait;

    private function doSetUp()
    {
        $this->command = new DisableEventCommand();
        $this->apiMethod = 'setNotifications';

        parent::setUp();
    }

    public function testCommand()
    {
        $notificationEvent = new NotificationEvent('video-created', false);
        $notifications = new Notifications();
        $notifications->addNotificationEvent($notificationEvent);
        $this->defaultCloud
            ->expects($this->once())
            ->method('setNotifications')
            ->with($this->equalTo($notifications));
        $this->runCommand(
            'panda:notifications:disable',
            array('event' => 'video-created')
        );
    }

    public function testCommandWithoutArguments()
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Not enough arguments');

        $this->runCommand('panda:notifications:disable');
    }

    protected function getDefaultCommandArguments()
    {
        return array('event' => 'video-created');
    }
}
