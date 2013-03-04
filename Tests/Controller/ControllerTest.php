<?php

/*
* This file is part of the XabbuhPandaBundle package.
*
* (c) Christian Flothmann <christian.flothmann@xabbuh.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Xabbuh\PandaBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Xabbuh\PandaBundle\Event\EncodingProgressEvent;
use Xabbuh\PandaBundle\Event\EncodingCompleteEvent;
use Xabbuh\PandaBundle\Event\VideoCreatedEvent;
use Xabbuh\PandaBundle\Event\VideoEncodedEvent;

/**
 * Test the bundle's controllers.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ControllerTest extends WebTestCase
{
    /**
     * An event dispatched by the tested application
     * @var \Symfony\Component\EventDispatcher\Event
     */
    public $event;

    /**
     * Count dispatched events
     * @var int
     */
    public $eventCounter;


    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->event = null;
        $this->eventCounter = 0;
    }

    /**
     * Ensure that a timestamp is added when not provided
     */
    public function testSignWithoutTimestamp()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $defaultCloud = $container->getParameter("xabbuh_panda.cloud.default");

        $client->request("GET", "/sign/$defaultCloud");
        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals("application/json", $response->headers->get("content-type"));

        $json = json_decode($response->getContent());
        $this->assertEquals("stdClass", get_class($json));
        $this->assertTrue(isset($json->cloud_id));
        $this->assertTrue(isset($json->access_key));
        $this->assertTrue(isset($json->timestamp));
        $this->assertEquals(44, strlen($json->signature));
    }

    /**
     * Ensure that the signatures don't vary for equal parameters and equal
     * timestamps.
     */
    public function testSignatureConsistency()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $defaultCloud = $container->getParameter("xabbuh_panda.cloud.default");

        $timestamp = date("c");

        // firstly, request signature without specifying the request method
        // and url path of the request to be signed (this implies that GET
        // and /videos.json will be used as default values)
        $client->request(
            "GET",
            "/sign/$defaultCloud",
            array("timestamp" => $timestamp)
        );
        $response1 = $client->getResponse();

        $this->assertTrue($response1->isSuccessful());
        $this->assertEquals("application/json", $response1->headers->get("content-type"));

        $json1 = json_decode($response1->getContent());
        $this->assertEquals("stdClass", get_class($json1));
        $this->assertTrue(isset($json1->cloud_id));
        $this->assertTrue(isset($json1->access_key));
        $this->assertTrue(isset($json1->timestamp));
        $this->assertEquals(44, strlen($json1->signature));

        // do a second request where the request method (GET) and the url
        // path (/videos.json) are explicitly specified
        $client->request(
            "GET",
            "/sign/$defaultCloud",
            array(
                "method" => "GET",
                "path" => "/videos.json",
                "timestamp" => $timestamp
            )
        );
        $response2 = $client->getResponse();

        $this->assertTrue($response2->isSuccessful());
        $this->assertEquals("application/json", $response2->headers->get("content-type"));

        $json2 = json_decode($response2->getContent());
        $this->assertEquals("stdClass", get_class($json2));
        $this->assertTrue(isset($json2->cloud_id));
        $this->assertTrue(isset($json2->access_key));
        $this->assertTrue(isset($json2->timestamp));
        $this->assertEquals(44, strlen($json2->signature));

        // check that both signatures are equal
        $this->assertEquals($json1->signature, $json2->signature);
    }

    /**
     * Tests the response of the authorise upload action.
     */
    public function testAuthoriseUploadAction()
    {
        $payload = new \stdClass();
        $payload->filename = "video.mp4";
        $payload->filesize = 1722340;
        $payload->content_type = "application/octet-stream";

        $client = static::createClient();
        $container = $client->getContainer();
        $defaultCloud = $container->getParameter("xabbuh_panda.cloud.default");
        $client->request(
            "POST",
            "/authorise_upload/$defaultCloud",
            array("payload" => json_encode($payload))
        );
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
        $decodedJson = json_decode($response->getContent());
        $this->assertTrue($decodedJson instanceof \stdClass);
        $this->assertTrue(isset($decodedJson->upload_url));
    }

    /**
     * Test the dispatching of VideoCreatedEvents.
     */
    public function testNotifyEncodingComplete()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        // hook into the event dispatcher and fetch information
        // on dispatched events
        $self = $this;
        $eventDispatcher = $container->get("event_dispatcher");
        $eventDispatcher->addListener(
            "xabbuh_panda.encoding_complete",
            function(EncodingCompleteEvent $event) use($self) {
                $self->event = $event;
                $self->eventCounter++;
            }
        );

        $videoId = "a0d3cae9af93fb16b90e709ad5d9279b";
        $encodingId = "56d6f222daab29ca8a1b278775e2eab4";
        $client->request(
            "POST",
            "/notify",
            array(
                "event" => "encoding-complete",
                "video_id" => $videoId,
                "encoding_id" => $encodingId
            )
        );
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Event\\EncodingCompleteEvent",
            get_class($this->event)
        );
        $this->assertEquals(1, $this->eventCounter);
        $this->assertEquals($videoId, $this->event->getVideoId());
        $this->assertEquals($encodingId, $this->event->getEncodingId());
    }

    /**
     * Test the dispatching of VideoCreatedEvents.
     */
    public function testNotifyEncodingProgress()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        // hook into the event dispatcher and fetch information
        // on dispatched events
        $self = $this;
        $eventDispatcher = $container->get("event_dispatcher");
        $eventDispatcher->addListener(
            "xabbuh_panda.encoding_progress",
            function(EncodingProgressEvent $event) use($self) {
                $self->event = $event;
                $self->eventCounter++;
            }
        );

        $videoId = "a0d3cae9af93fb16b90e709ad5d9279b";
        $encodingId = "56d6f222daab29ca8a1b278775e2eab4";
        $progress = 63;
        $client->request(
            "POST",
            "/notify",
            array(
                "event" => "encoding-progress",
                "video_id" => $videoId,
                "encoding_id" => $encodingId,
                "progress" => 63
            )
        );
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Event\\EncodingProgressEvent",
            get_class($this->event)
        );
        $this->assertEquals(1, $this->eventCounter);
        $this->assertEquals($videoId, $this->event->getVideoId());
        $this->assertEquals($encodingId, $this->event->getEncodingId());
        $this->assertEquals($progress, $this->event->getProgress());
    }

    /**
     * Test the dispatching of VideoCreatedEvents.
     */
    public function testNotifyVideoCreated()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        // hook into the event dispatcher and fetch information
        // on dispatched events
        $self = $this;
        $eventDispatcher = $container->get("event_dispatcher");
        $eventDispatcher->addListener(
            "xabbuh_panda.video_created",
            function(VideoCreatedEvent $event) use($self) {
                $self->event = $event;
                $self->eventCounter++;
            }
        );

        $videoId = "a0d3cae9af93fb16b90e709ad5d9279b";
        $encodingIds = array(
            "56d6f222daab29ca8a1b278775e2eab4",
            "efde127ce32482342aea58973ee39f23"
        );
        $client->request(
            "POST",
            "/notify",
            array(
                "event" => "video-created",
                "video_id" => $videoId,
                "encoding_ids" => $encodingIds
            )
        );
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Event\\VideoCreatedEvent",
            get_class($this->event)
        );
        $this->assertEquals(1, $this->eventCounter);
        $this->assertEquals($videoId, $this->event->getVideoId());
        $this->assertEquals($encodingIds, $this->event->getEncodingIds());
    }

    /**
     * Test the dispatching of VideoCreatedEvents.
     */
    public function testNotifyVideoEncoded()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        // hook into the event dispatcher and fetch information
        // on dispatched events
        $self = $this;
        $eventDispatcher = $container->get("event_dispatcher");
        $eventDispatcher->addListener(
            "xabbuh_panda.video_encoded",
            function(VideoEncodedEvent $event) use($self) {
                $self->event = $event;
                $self->eventCounter++;
            }
        );

        $videoId = "a0d3cae9af93fb16b90e709ad5d9279b";
        $encodingIds = array(
            "56d6f222daab29ca8a1b278775e2eab4",
            "efde127ce32482342aea58973ee39f23"
        );
        $client->request(
            "POST",
            "/notify",
            array(
                "event" => "video-encoded",
                "video_id" => $videoId,
                "encoding_ids" => $encodingIds
            )
        );
        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Event\\VideoEncodedEvent",
            get_class($this->event)
        );
        $this->assertEquals(1, $this->eventCounter);
        $this->assertEquals($videoId, $this->event->getVideoId());
        $this->assertEquals($encodingIds, $this->event->getEncodingIds());
    }

    /**
     * Ensure that the notify controller returns an error response if no
     * event is given.
     */
    public function testNotifyWithoutEvent()
    {
        $client = static::createClient();
        $client->request("POST", "/notify");
        $response = $client->getResponse();
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isClientError());
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * Ensure that the notify controller returns an error response if no
     * valid event is given.
     */
    public function testNotifyWithInvalidEvent()
    {
        $client = static::createClient();
        $client->request("POST", "/notify", array("event" => "video-complete"));
        $response = $client->getResponse();
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isClientError());
        $this->assertEquals(400, $response->getStatusCode());
    }
}
