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
        if ($accountKey == null) {
            $account = $this->accountManager->getDefaultAccount();
        } else {
            $account = $this->accountManager->getAccount($accountKey);
        }

        $httpClient = new HttpClient($cloudId, $account);

        return new Cloud($httpClient, $this->transformerRegistry);
    }
}
