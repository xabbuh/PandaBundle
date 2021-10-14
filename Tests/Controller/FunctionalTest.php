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
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class FunctionalTest extends WebTestCase
{
    /**
     * @var \Symfony\Contracts\EventDispatcher\Event
     */
    public $event;

    /**
     * @var int
     */
    public $eventCounter;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser
     */
    private $client;

    protected function setUp(): void
    {
        try {
            self::getKernelClass();
        } catch (\RuntimeException $e) {
            $this->markTestSkipped('missing kernel configuration');
        }

        $this->event = null;
        $this->eventCounter = 0;
        $this->client = static::createClient();
    }

    public function testSignWithoutTimestamp()
    {
        $this->client->request('GET', '/sign/'.$this->getDefaultCloudName());
        $decodedResponse = $this->validateJsonResponse($this->client->getResponse());

        $this->assertEquals('e122090f4e506ae9ee266c3eb78a8b67', $decodedResponse->cloud_id);
        $this->assertEquals('799572f795a5a09a251cf2cf46c419ab', $decodedResponse->access_key);
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

    private function getDefaultCloudName()
    {
        return 'default';
    }

    private function validateJsonResponse(Response $response)
    {
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('application/json', $response->headers->get('content-type'));

        return json_decode($response->getContent());
    }
}
