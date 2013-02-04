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
 * Interface definition for cloud manager implementations.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
interface CloudManagerInterface
{
    /**
     * Register a cloud provider.
     *
     * @param CloudProviderInterface $provider The provider being registered
     */
    public function registerProvider(CloudProviderInterface $provider);

    /**
     * Register a cloud on this manager.
     *
     * @param string $key Assign the cloud to this key
     * @param Cloud $cloud Cloud to register
     */
    public function registerCloud($key, Cloud $cloud);

    /**
     * Get the cloud for a key.
     *
     * @param $key The internal key
     * @return Cloud
     * @throws \InvalidArgumentException if no account for the given key exists
     */
    public function getCloud($key);

    /**
     * Get the application's default cloud
     *
     * @return Cloud
     */
    public function getDefaultCloud();

    /**
     * Get all managed clouds.
     *
     * An associative array is returned where the internal keys is associated
     * the configured cloud.
     *
     * @return array
     */
    public function getClouds();
}
