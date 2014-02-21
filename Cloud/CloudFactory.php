<?php

/*
 * This file is part of the XabbuhPandaBundle package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\PandaBundle\Cloud;

use Guzzle\Http\Client;
use Xabbuh\PandaClient\Api\AccountManagerInterface;
use Xabbuh\PandaClient\Api\Cloud;
use Xabbuh\PandaClient\Api\HttpClient;
use Xabbuh\PandaClient\Transformer\TransformerRegistryInterface;

/**
 * Factory to create Cloud instances.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class CloudFactory implements CloudFactoryInterface
{
    /**
     * The account manager being used to manage all configured Accounts
     * @var AccountManagerInterface
     */
    private $accountManager;

    /**
     * Factory for creating model transformers
     * @var TransformerRegistryInterface
     */
    private $transformerRegistry;

    public function __construct(
        AccountManagerInterface $accountManager,
        TransformerRegistryInterface $transformerFactory
    ) {
        $this->accountManager = $accountManager;
        $this->transformerRegistry = $transformerFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function get($cloudId, $accountKey = null)
    {
        $account = $this->accountManager->getAccount($accountKey);

        $guzzleClient = new Client('https://'.$account->getApiHost().'/v2');

        $httpClient = new HttpClient();
        $httpClient->setCloudId($cloudId);
        $httpClient->setAccount($account);
        $httpClient->setGuzzleClient($guzzleClient);

        $cloud = new Cloud();
        $cloud->setHttpClient($httpClient);
        $cloud->setTransformers($this->transformerRegistry);

        return $cloud;
    }
}
