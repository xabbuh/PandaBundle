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
use Xabbuh\PandaClient\Api\RestClient;
use Xabbuh\PandaClient\Transformer\TransformerFactory;

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
     * @var TransformerFactory
     */
    private $transformerFactory;

    public function __construct(
        AccountManagerInterface $accountManager,
        TransformerFactory $transformerFactory
    ) {
        $this->accountManager = $accountManager;
        $this->transformerFactory = $transformerFactory;
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

        $restClient = new RestClient($cloudId, $account);

        return new Cloud($restClient, $this->transformerFactory);
    }
}
