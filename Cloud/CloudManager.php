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

use Xabbuh\PandaBundle\Cloud\Cloud;
use Xabbuh\PandaBundle\Cloud\CloudProviderInterface;

/**
 * Manager of all clouds which can be used in the application.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class CloudManager
{
    private $defaultCloudKey;

    private $clouds = array();

    public function __construct($defaultCloudName)
    {
        $this->defaultCloudKey = $defaultCloudName;
    }

    /**
     * Register a cloud provider.
     *
     * @param CloudProviderInterface $provider The provider being registered
     */
    public function registerProvider(CloudProviderInterface $provider)
    {
        $provider->initialise();
    }

    /**
     * Register a cloud on this manager.
     *
     * @param $key Assign the cloud to this key
     * @param Cloud $cloud Cloud to register
     */
    public function registerCloud($key, Cloud $cloud)
    {
        $this->clouds[$key] = $cloud;
    }

    /**
     * Get the cloud for a key.
     *
     * @param $key The internal key
     * @return Cloud
     * @throws \InvalidArgumentException if no account for the given key exists
     */
    public function getCloud($key)
    {
        if (!isset($this->clouds[$key])) {
            throw new \InvalidArgumentException("No cloud for key $key configured.");
        }
        return $this->clouds[$key];
    }

    /**
     * Get the application's default cloud
     *
     * @return Cloud
     */
    public function getDefaultCloud()
    {
        return $this->getCloud($this->defaultCloudKey);
    }
}
