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
class CloudManager implements CloudManagerInterface
{
    private $defaultCloudKey;

    private $clouds = array();

    public function __construct($defaultCloudName)
    {
        $this->defaultCloudKey = $defaultCloudName;
    }

    /**
     * {@inheritDoc}
     */
    public function registerProvider(CloudProviderInterface $provider)
    {
        $provider->initialise();
    }

    /**
     * {@inheritDoc}
     */
    public function registerCloud($key, Cloud $cloud)
    {
        $this->clouds[$key] = $cloud;
    }

    /**
     * {@inheritDoc}
     */
    public function getCloud($key)
    {
        if (!isset($this->clouds[$key])) {
            throw new \InvalidArgumentException("No cloud for key $key configured.");
        }
        return $this->clouds[$key];
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultCloud()
    {
        return $this->getCloud($this->defaultCloudKey);
    }

    /**
     * {@inheritDoc}
     */
    public function getClouds()
    {
        return $this->clouds;
    }
}
