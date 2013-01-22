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
 * Interface definition for cloud factory implementations.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
interface CloudFactoryInterface
{
    /**
     * Create a cloud instance for a cloud given by its id and panda account.
     *
     * @param $cloudId The cloud id
     * @param $accountKey The internal account key (pass null to use the default account)
     * @return Cloud The generated instance
     */
    public function get($cloudId, $accountKey = null);
}
