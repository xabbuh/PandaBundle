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

@trigger_error('The Xabbuh\PandaBundle\Cloud\CloudFactoryInterface interface is deprecated since version 1.3 and will be removed in 2.0', E_USER_DEPRECATED);

use Xabbuh\PandaClient\Api\Cloud;

/**
 * Interface definition for cloud factory implementations.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * @deprecated since version 1.3, to be removed in 2.0
 */
interface CloudFactoryInterface
{
    /**
     * Create a cloud instance for a cloud given by its id and panda account.
     *
     * @param string $cloudId    The cloud id
     * @param string $accountKey The internal account key (use the default account if null is passed)
     *
     * @return Cloud The generated instance
     */
    public function get($cloudId, $accountKey = null);
}
