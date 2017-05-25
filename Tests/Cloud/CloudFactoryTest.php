<?php

/*
 * This file is part of the XabbuhPandaBundle package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\PandaBundle\Tests\Cloud;

use Http\Discovery\HttpClientDiscovery;
use PHPUnit\Framework\TestCase;
use Xabbuh\PandaBundle\Cloud\CloudFactory;
use Xabbuh\PandaClient\Api\Account;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * @group legacy
 */
class CloudFactoryTest extends TestCase
{
    /**
     * @var CloudFactory
     */
    private $cloudFactory;

    /**
     * @var \Xabbuh\PandaClient\Api\Account
     */
    private $account;

    /**
     * @var \Xabbuh\PandaClient\Api\AccountManagerInterface
     */
    private $accountManager;

    /**
     * @var \Xabbuh\PandaClient\Transformer\TransformerRegistryInterface
     */
    private $transformerRegistry;

    protected function setUp()
    {
        $this->createAccountManagerMock();
        $this->createTransformerRegistryMock();
        $this->cloudFactory = new CloudFactory(
            $this->accountManager,
            $this->transformerRegistry
        );

        HttpClientDiscovery::prependStrategy('Http\Discovery\Strategy\MockClientStrategy');
    }

    public function testGet()
    {
        $cloud = $this->cloudFactory->get(md5(uniqid()), 'foo');

        $this->assertInstanceOf('\Xabbuh\PandaClient\Api\Cloud', $cloud);
    }

    public function testHttpClientFromGet()
    {
        $cloudId = md5(uniqid());
        $cloud = $this->cloudFactory->get($cloudId, 'foo');
        $httpClient = $cloud->getHttpClient();

        $this->assertEquals($cloudId, $httpClient->getCloudId());
        $this->assertEquals($this->account, $httpClient->getAccount());
    }

    public function testTransformerRegistryFromGet()
    {
        $cloud = $this->cloudFactory->get(md5(uniqid()), 'foo');

        $this->assertEquals($this->transformerRegistry, $cloud->getTransformers());
    }

    private function createAccount()
    {
        $this->account = new Account(md5(uniqid()), md5(uniqid()), 'example.com');
    }

    private function createAccountManagerMock()
    {
        $this->createAccount();
        $this->accountManager = $this->getMockBuilder('\Xabbuh\PandaClient\Api\AccountManagerInterface')->getMock();
        $this->accountManager
            ->expects($this->any())
            ->method('getAccount')
            ->will($this->returnValue($this->account));
    }

    private function createTransformerRegistryMock()
    {
        $this->transformerRegistry = $this->getMockBuilder('\Xabbuh\PandaClient\Transformer\TransformerRegistryInterface')->getMock();
    }
}
