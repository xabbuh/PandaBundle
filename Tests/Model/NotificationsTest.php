<?php

/*
* This file is part of the XabbuhPandaBundle package.
*
* (c) Christian Flothmann <christian.flothmann@xabbuh.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Xabbuh\PandaBundle\Tests\Model;

use Xabbuh\PandaBundle\Model\NotificationEvent;
use Xabbuh\PandaBundle\Model\Notifications;

/**
 * Test the Notifications model class.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class NotificationsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test setting and getting the notification url.
     */
    public function testUrl()
    {
        $notifications = new Notifications();
        $this->assertNull($notifications->getUrl());
        $notifications->setUrl("http://www.example.com/notify");
        $this->assertEquals("http://www.example.com/notify", $notifications->getUrl());
    }

    /**
     * Test if adding, retrieving and removing events works properly.
     */
    public function testEvents()
    {
        $notifications = new Notifications();
        $this->assertNull($notifications->getNotificationEvent("video-created"));
        $videoCreatedEvent = new NotificationEvent("video-created", true);
        $notifications->addNotificationEvent($videoCreatedEvent);
        $this->assertEquals(
            $videoCreatedEvent,
            $notifications->getNotificationEvent("video-created")
        );
        $videoCreatedEvent2 = new NotificationEvent("video-created", true);
        $this->assertEquals(
            $videoCreatedEvent2,
            $notifications->getNotificationEvent("video-created")
        );
        $this->assertNull($notifications->getNotificationEvent("video-encoded"));
        $notifications->removeNotificationEvent($videoCreatedEvent2);
        $this->assertNull($notifications->getNotificationEvent("video-created"));
    }
}
