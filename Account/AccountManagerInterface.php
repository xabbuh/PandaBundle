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
 * Interface definition for account manager implementations.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
interface AccountManagerInterface
{
    /**
     * Register an account provider.
     *
     * @param AccountProviderInterface $provider The provider being registered
     */
    public function registerProvider(AccountProviderInterface $provider);

    /**
     * Register an account on this manager.
     *
     * @param $key Assign the account to this key
     * @param Account $account Account to register
     */
    public function registerAccount($key, Account $account);

    /**
     * Get the account for a key.
     *
     * @param $key The internal key
     * @return \Xabbuh\PandaBundle\Account\Account
     * @throws \InvalidArgumentException if no account for the given key exists
     */
    public function getAccount($key);

    /**
     * Get the application's default account
     *
     * @return Account
     */
    public function getDefaultAccount();
}
