<?php

/*
* This file is part of the XabbuhPandaBundle package.
*
* (c) Christian Flothmann <christian.flothmann@xabbuh.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Xabbuh\PandaBundle\Account;

/**
 * Provider for accounts configured in the application's configuration.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ConfigAccountProvider implements AccountProviderInterface
{
    /**
     * The accounts provided by this ConfigAccountProvider
     * @var array
     */
    private $accounts = array();


    /**
     * @param array $accounts List of configured accounts that are provided
     * by this account provider
     */
    public function __construct(array $accounts)
    {
        $this->accounts = $accounts;
    }

    /**
     * {@inheritDoc}
     */
    public function initialise(AccountManagerInterface $accountManager)
    {
        foreach ($this->accounts as $name => $account) {
            $accountManager->registerAccount(
                $name,
                new Account(
                    $account["access_key"],
                    $account["secret_key"],
                    $account["api_host"]
                )
            );
        }
    }
}
