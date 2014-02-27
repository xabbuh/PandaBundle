<?php

/**
 * This file is part of the XabbuhPandaBundle package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\PandaBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Xabbuh\PandaBundle\Event\EncodingProgressEvent;
use Xabbuh\PandaBundle\Event\EncodingCompleteEvent;
use Xabbuh\PandaBundle\Event\VideoCreatedEvent;
use Xabbuh\PandaBundle\Event\VideoEncodedEvent;
use Xabbuh\PandaClient\Api\Account;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ControllerTest extends WebTestCase
{
    /**
     * @var \Symfony\Component\EventDispatcher\Event
     */
    public $event;

    /**
     * @var int
     */
    public $eventCounter;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    private $client;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Xabbuh\PandaClient\Api\CloudInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $defaultCloud;

    public function setUp()
    {
        try {
            self::getKernelClass();
        } catch (\RuntimeException $e) {
            $this->markTestSkipped('missing kernel configuration');
        }

        $this->event = null;
        $this->eventCounter = 0;
        $this->initClient();
    }

    public function testSignWithoutTimestamp()
    {
        $this->client->request('GET', '/sign/'.$this->getDefaultCloudName());
        $decodedResponse = $this->validateJsonResponse($this->client->getResponse());

        $this->assertEquals('cloud-id', $decodedResponse->cloud_id);
        $this->assertEquals('access-key', $decodedResponse->access_key);
        $this->assertTrue(isset($decodedResponse->timestamp));
        $this->assertEquals(44, strlen($decodedResponse->signature));
    }

    public function testSignatureConsistency()
    {
        $timestamp = date('c');

        // first, request signature without specifying the request method
        // and the url path of the request to be signed (this implies that GET
        // and /videos.json will be used as default values)
        $this->client->request(
            'GET',
            '/sign/'.$this->getDefaultCloudName(),
            array('timestamp' => $timestamp)
        );
        $decodedResponse1 = $this->validateJsonResponse($this->client->getResponse());

        // do a second request where the request method (GET) and the url
        // path (/videos.json) are explicitly specified
        $this->initClient();
        $this->client->request(
            'GET',
            '/sign/'.$this->getDefaultCloudName(),
            array(
                'method' => 'GET',
                'path' => '/videos.json',
                'timestamp' => $timestamp
            )
        );
        $decodedResponse2 = $this->validateJsonResponse($this->client->getResponse());

        // check that both signatures are equal
        $this->assertEquals($decodedResponse1->signature, $decodedResponse2->signature);
    }

    public function testAuthoriseUploadAction()
    {
        $payload = new \stdClass();
        $payload->filename = 'video.mp4';
        $payload->filesize = 1722340;
        $payload->content_type = 'application/octet-stream';

        $apiResponse = new \stdClass();
        $apiResponse->location = 'http://vm-37gup6.upload.pandastream.com/upload/session?id=abcdefgh';
        $apiResponse->id = 'abcdefgh';

        $this->defaultCloud
            ->expects($this->once())
            ->method('registerUpload')
            ->with($payload->filename, $payload->filesize, null, true)
            ->will($this->returnValue($apiResponse));

        $this->client->request(
            'POST',
            '/authorise_upload/'.$this->getDefaultCloudName(),
            array('payload' => json_encode($payload))
        );
        $response = $this->client->getResponse();

        $decodedJson = $this->validateJsonResponse($response);
        $this->assertEquals($apiResponse->location, $decodedJson->upload_url);
    }

    public function testNotifyEncodingComplete()
    {
        $videoId = 'a0d3cae9af93fb16b90e709ad5d9279b';
        $encodingId = '56d6f222daab29ca8a1b278775e2eab4';
        $this->validateEventRequest(
            'encoding-complete',
            'Xabbuh\PandaBundle\Event\EncodingCompleteEvent',
            array(
                'event' => 'encoding-complete',
                'video_id' => $videoId,
                'encoding_id' => $encodingId
            )
        );
        $this->assertEquals($videoId, $this->event->getVideoId());
        $this->assertEquals($encodingId, $this->event->getEncodingId());
    }

    public function testNotifyEncodingProgress()
    {
        $videoId = 'a0d3cae9af93fb16b90e709ad5d9279b';
        $encodingId = '56d6f222daab29ca8a1b278775e2eab4';
        $progress = 63;
        $this->validateEventRequest(
            'encoding-progress',
            'Xabbuh\PandaBundle\Event\EncodingProgressEvent',
            array(
                'event' => 'encoding-progress',
                'video_id' => $videoId,
                'encoding_id' => $encodingId,
                'progress' => 63
            )
        );
        $this->assertEquals($videoId, $this->event->getVideoId());
        $this->assertEquals($encodingId, $this->event->getEncodingId());
        $this->assertEquals($progress, $this->event->getProgress());
    }

    public function testNotifyVideoCreated()
    {
        $videoId = 'a0d3cae9af93fb16b90e709ad5d9279b';
        $encodingIds = array(
            '56d6f222daab29ca8a1b278775e2eab4',
            'efde127ce32482342aea58973ee39f23'
        );
        $this->validateEventRequest(
            'video-created',
            'Xabbuh\PandaBundle\Event\VideoCreatedEvent',
            array(
                'event' => 'video-created',
                'video_id' => $videoId,
                'encoding_ids' => $encodingIds
            )
        );
        $this->assertEquals($videoId, $this->event->getVideoId());
        $this->assertEquals($encodingIds, $this->event->getEncodingIds());
    }

    public function testNotifyVideoEncoded()
    {
        $videoId = 'a0d3cae9af93fb16b90e709ad5d9279b';
        $encodingIds = array(
            '56d6f222daab29ca8a1b278775e2eab4',
            'efde127ce32482342aea58973ee39f23'
        );
        $this->validateEventRequest(
            'video-encoded',
            'Xabbuh\PandaBundle\Event\VideoEncodedEvent',
            array(
                'event' => 'video-encoded',
                'video_id' => $videoId,
                'encoding_ids' => $encodingIds
            )
        );
        $this->assertEquals($videoId, $this->event->getVideoId());
        $this->assertEquals($encodingIds, $this->event->getEncodingIds());
    }

    public function testNotifyWithoutEvent()
    {
        $this->client->request('POST', '/notify');

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testNotifyWithInvalidEvent()
    {
        $this->client->request('POST', '/notify', array('event' => 'video-complete'));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    private function initClient()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->createMocks();
    }

    private function createMocks()
    {
        $httpClient = $this->getMock('\Xabbuh\PandaClient\Api\HttpClientInterface');
        $httpClient->expects($this->any())
            ->method('getCloudId')
            ->will($this->returnValue('cloud-id'));
        $httpClient->expects($this->any())
            ->method('getAccount')
            ->will($this->returnValue(
                new Account('access-key', 'secret-key', 'api.pandastream.com')
            ));
        $this->defaultCloud = $this->getMock('\Xabbuh\PandaClient\Api\CloudInterface');
        $this->defaultCloud
            ->expects($this->any())
            ->method('getHttpClient')
            ->will($this->returnValue($httpClient));
        $cloudManager = $this->getMock('\Xabbuh\PandaClient\Api\CloudManagerInterface');
        $cloudManager->expects($this->any())
            ->method('getCloud')
            ->with($this->getDefaultCloudName())
            ->will($this->returnValue($this->defaultCloud));
        $this->container->set('xabbuh_panda.cloud_manager', $cloudManager);
    }

    private function getDefaultCloudName()
    {
        if (!$this->container->hasParameter('xabbuh_panda.cloud.default')) {
            $this->markTestSkipped('default cloud name has to be configured');
        }

        return $this->container->getParameter('xabbuh_panda.cloud.default');
    }

    private function validateJsonResponse(Response $response)
    {
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('application/json', $response->headers->get('content-type'));

        return json_decode($response->getContent());
    }

    private function validateEventRequest($eventName, $eventClassName, array $parameters)
    {
        $self = $this;
        $eventDispatcher = $this->container->get('event_dispatcher');
        $eventDispatcher->addListener(
            'xabbuh_panda.'.strtr($eventName, '-', '_'),
            function ($event) use ($self) {
                $self->event = $event;
                $self->eventCounter++;
            }
        );

        $this->client->request('POST', '/notify', $parameters);
        $response = $this->client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertInstanceOf($eventClassName, $this->event);
        $this->assertEquals(1, $this->eventCounter);
    }
}
