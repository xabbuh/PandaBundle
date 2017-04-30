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
use Symfony\Component\HttpFoundation\Request;
use Xabbuh\PandaBundle\Event\EventFactory;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class EventFactoryTest extends TestCase
{
    /**
     * @dataProvider createRequestWithEvent
     */
    public function testCreateEventFromRequest(Request $request, $expectedEventClass)
    {
        $event = EventFactory::createEventFromRequest($request);

        $this->assertInstanceOf($expectedEventClass, $event);
    }

    /**
     * @dataProvider createRequestWithEncodingCompleteEvent
     */
    public function testCreateEncodingCompleteEventFromRequest(Request $request)
    {
        $event = EventFactory::createEncodingCompleteEventFromRequest($request);

        $this->assertInstanceOf('Xabbuh\PandaBundle\Event\EncodingCompleteEvent', $event);
        $this->assertEquals('0e06bdeb513e9e5f495d769fd993d8be', $event->getVideoId());
        $this->assertEquals('9078f6bd53e9b90979823574f56c94b0', $event->getEncodingId());
    }

    /**
     * @dataProvider createRequestWithEncodingProgressEvent
     */
    public function testCreateEncodingProgressEventFromRequest(Request $request)
    {
        $event = EventFactory::createEncodingProgressEventFromRequest($request);

        $this->assertInstanceOf('Xabbuh\PandaBundle\Event\EncodingProgressEvent', $event);
        $this->assertEquals('0e06bdeb513e9e5f495d769fd993d8be', $event->getVideoId());
        $this->assertEquals('9078f6bd53e9b90979823574f56c94b0', $event->getEncodingId());
        $this->assertEquals(99, $event->getProgress());
    }

    /**
     * @dataProvider createRequestWithVideoCreatedEvent
     */
    public function testCreateVideoCreatedEventFromRequest(Request $request)
    {
        $event = EventFactory::createVideoCreatedEventFromRequest($request);

        $this->assertInstanceOf('Xabbuh\PandaBundle\Event\VideoCreatedEvent', $event);
        $this->assertEquals('0e06bdeb513e9e5f495d769fd993d8be', $event->getVideoId());
        $this->assertEquals(
            array('4d2bc87f7e68f14c9b4c8394fe3aff3f', '403301e0d6f2fc502ad8e9a54afc38fc'),
            $event->getEncodingIds()
        );
    }

    /**
     * @dataProvider createRequestWithVideoEncodedEvent
     */
    public function testCreateVideoEncodedEventFromRequest(Request $request)
    {
        $event = EventFactory::createVideoEncodedEventFromRequest($request);

        $this->assertInstanceOf('Xabbuh\PandaBundle\Event\VideoEncodedEvent', $event);
        $this->assertEquals('0e06bdeb513e9e5f495d769fd993d8be', $event->getVideoId());
        $this->assertEquals(
            array('4d2bc87f7e68f14c9b4c8394fe3aff3f', '403301e0d6f2fc502ad8e9a54afc38fc'),
            $event->getEncodingIds()
        );
    }

    public function createRequestWithEvent()
    {
        $requestWithEncodingCompleteEvent = $this->createRequestWithEncodingCompleteEvent();
        $requestWithEncodingProgressEvent = $this->createRequestWithEncodingProgressEvent();
        $requestWithVideoCreatedEvent = $this->createRequestWithVideoCreatedEvent();
        $requestWithVideoEncodedEvent = $this->createRequestWithVideoEncodedEvent();

        return array(
            array(
                $requestWithEncodingCompleteEvent[0][0],
                'Xabbuh\PandaBundle\Event\EncodingCompleteEvent',
            ),
            array(
                $requestWithEncodingProgressEvent[0][0],
                'Xabbuh\PandaBundle\Event\EncodingProgressEvent',
            ),
            array(
                $requestWithVideoCreatedEvent[0][0],
                'Xabbuh\PandaBundle\Event\VideoCreatedEvent',
            ),
            array(
                $requestWithVideoEncodedEvent[0][0],
                'Xabbuh\PandaBundle\Event\VideoEncodedEvent',
            ),
        );
    }

    public function createRequestWithEncodingCompleteEvent()
    {
        $request = new Request();
        $request->request->set('event', 'encoding-complete');
        $request->request->set('video_id', '0e06bdeb513e9e5f495d769fd993d8be');
        $request->request->set('encoding_id', '9078f6bd53e9b90979823574f56c94b0');

        return array(array($request));
    }

    public function createRequestWithEncodingProgressEvent()
    {
        $request = new Request();
        $request->request->set('event', 'encoding-progress');
        $request->request->set('video_id', '0e06bdeb513e9e5f495d769fd993d8be');
        $request->request->set('encoding_id', '9078f6bd53e9b90979823574f56c94b0');
        $request->request->set('progress', 99);

        return array(array($request));
    }

    public function createRequestWithVideoCreatedEvent()
    {
        $request = new Request();
        $request->request->set('event', 'video-created');
        $request->request->set('video_id', '0e06bdeb513e9e5f495d769fd993d8be');
        $request->request->set(
            'encoding_ids',
            array('4d2bc87f7e68f14c9b4c8394fe3aff3f', '403301e0d6f2fc502ad8e9a54afc38fc')
        );

        return array(array($request));
    }

    public function createRequestWithVideoEncodedEvent()
    {
        $request = new Request();
        $request->request->set('event', 'video-encoded');
        $request->request->set('video_id', '0e06bdeb513e9e5f495d769fd993d8be');
        $request->request->set(
            'encoding_ids',
            array('4d2bc87f7e68f14c9b4c8394fe3aff3f', '403301e0d6f2fc502ad8e9a54afc38fc')
        );

        return array(array($request));
    }
}
