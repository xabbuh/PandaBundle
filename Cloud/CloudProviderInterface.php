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

/**
 * Common interface of all cloud providers.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
interface CloudProviderInterface
{
    /**
     * Initialise the cloud provider.
     *
     * This method is called by the cloud manager after the provider is
     * registered.
     */
    public function initialise();
}
