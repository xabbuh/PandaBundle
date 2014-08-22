<?php

/*
 * This file is part of the XabbuhPandaBundle package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\PandaBundle\Tests\Event;

use Xabbuh\PandaBundle\Event\VideoCreatedEvent;

/**
 * Test the VideoCreatedEvent class.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class VideoCreatedEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test to ensure that the getter methods return the expected values.
     */
    public function testGetterMethods()
    {
        $event = new VideoCreatedEvent(
            "0e06bdeb513e9e5f495d769fd993d8be",
            array(
                "4d2bc87f7e68f14c9b4c8394fe3aff3f",
                "403301e0d6f2fc502ad8e9a54afc38fc"
            )
        );
        $this->assertEquals('xabbuh_panda.video_created', $event->getName());
        $this->assertEquals("0e06bdeb513e9e5f495d769fd993d8be", $event->getVideoId());
        $this->assertEquals(
            array(
                "4d2bc87f7e68f14c9b4c8394fe3aff3f",
                "403301e0d6f2fc502ad8e9a54afc38fc"
            ),
            $event->getEncodingIds()
        );
    }
}
