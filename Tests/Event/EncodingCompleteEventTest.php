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

use PHPUnit\Framework\TestCase;
use Xabbuh\PandaBundle\Event\EncodingCompleteEvent;

/**
 * Test the EncodingCompletedEvent class.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class EncodingCompleteEventTest extends TestCase
{
    /**
     * Test to ensure that the getter methods return the expected values.
     */
    public function testGetterMethods()
    {
        $event = new EncodingCompleteEvent(
            "0e06bdeb513e9e5f495d769fd993d8be",
            "9078f6bd53e9b90979823574f56c94b0"
        );
        $this->assertEquals("0e06bdeb513e9e5f495d769fd993d8be", $event->getVideoId());
        $this->assertEquals("9078f6bd53e9b90979823574f56c94b0", $event->getEncodingId());
    }
}
