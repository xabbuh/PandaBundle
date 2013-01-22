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

use Xabbuh\PandaBundle\Account\Account;
use Xabbuh\PandaBundle\Account\AccountManagerInterface;

/**
 * Provider for accounts configured in the application's configuration.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ConfigAccountProvider implements AccountProviderInterface
{
    private $accounts = array();

    private $accountManager;

    public function __construct(AccountManagerInterface $accountManager, array $accounts)
    {
        $this->accounts = $accounts;
        $this->accountManager = $accountManager;
    }

    /**
     * {@inheritDoc}
     */
    public function initialise()
    {
        foreach ($this->accounts as $name => $account) {
            $this->accountManager->registerAccount(
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
