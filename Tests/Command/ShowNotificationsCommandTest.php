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
use Symfony\Component\Console\Command\Command;
use Xabbuh\PandaBundle\Command\ShowNotificationsCommand;
use Xabbuh\PandaClient\Model\NotificationEvent;
use Xabbuh\PandaClient\Model\Notifications;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ShowNotificationsCommandTest extends CloudCommandTest
{
    use SetUpTearDownTrait;

    private function doSetUp()
    {
        parent::setUp();

        $this->apiMethod = 'getNotifications';
    }

    public function testCommand()
    {
        $notifications = new Notifications();
        $notifications->addNotificationEvent(
            new NotificationEvent('video-created', true)
        );
        $notifications->addNotificationEvent(
            new NotificationEvent('video-encoded', true)
        );
        $notifications->addNotificationEvent(
            new NotificationEvent('encoding-progress', false)
        );
        $notifications->addNotificationEvent(
            new NotificationEvent('encoding-completed', true)
        );
        $this->defaultCloud
            ->expects($this->once())
            ->method('getNotifications')
            ->will($this->returnValue($notifications));
        $this->runCommand('panda:notifications:show');

        $this->validateTableRows(array(
            array('video-created', 'enabled'),
            array('video-encoded', 'enabled'),
            array('encoding-progress', 'disabled'),
            array('encoding-completed', 'enabled'),
        ));
    }

    public function testCommandWithInversedStatus()
    {
        $notifications = new Notifications();
        $notifications->addNotificationEvent(
            new NotificationEvent('video-created', false)
        );
        $notifications->addNotificationEvent(
            new NotificationEvent('video-encoded', false)
        );
        $notifications->addNotificationEvent(
            new NotificationEvent('encoding-progress', true)
        );
        $notifications->addNotificationEvent(
            new NotificationEvent('encoding-completed', false)
        );
        $this->defaultCloud
            ->expects($this->once())
            ->method('getNotifications')
            ->will($this->returnValue($notifications));
        $this->runCommand('panda:notifications:show');

        $this->validateTableRows(array(
            array('video-created', 'disabled'),
            array('video-encoded', 'disabled'),
            array('encoding-progress', 'enabled'),
            array('encoding-completed', 'disabled'),
        ));
    }

    protected function createCommand(): Command
    {
        return new ShowNotificationsCommand($this->cloudManager);
    }
}
