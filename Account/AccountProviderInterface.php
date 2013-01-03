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
 * Common interface of all account providers.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
interface AccountProviderInterface
{
    /**
     * Initialise the account provider.
     *
     * This method is called by the account manager after the provider is
     * registered.
     */
    public function initialise();
}
