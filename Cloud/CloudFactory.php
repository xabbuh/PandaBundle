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

use Xabbuh\PandaBundle\Account\AccountManager;
use Xabbuh\PandaBundle\Services\TransformerFactory;
use Xabbuh\PandaClient\Api;
use Xabbuh\PandaClient\RestClient;

/**
 * Factory for creating cloud instances.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class CloudFactory
{
    /**
     * @var \Xabbuh\PandaBundle\Account\AccountManager
     */
    private $accountManager;

    /**
     * @var \Xabbuh\PandaBundle\Services\TransformerFactory
     */
    private $transformerFactory;


    public function __construct(AccountManager $accountManager, TransformerFactory $transformerFactory)
    {
        $this->accountManager = $accountManager;
        $this->transformerFactory = $transformerFactory;
    }

    /**
     * Create a cloud instance for a cloud given by its id and panda account.
     *
     * @param $cloudId The cloud id
     * @param $accountKey The internal account key (pass null to use the default account)
     * @return Cloud The generated instance
     */
    public function get($cloudId, $accountKey = null)
    {
        if ($accountKey == null) {
            $account = $this->accountManager->getDefaultAccount();
        } else {
            $account = $this->accountManager->getAccount($accountKey);
        }
        $restClient = new RestClient(
            $cloudId,
            $account->getAccessKey(),
            $account->getSecretKey(),
            $account->getApiHost()
        );
        $api = new Api($restClient);
        return new Cloud($api, $this->transformerFactory);
    }
}
