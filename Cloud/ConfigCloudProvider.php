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
 * Provider for clouds configured in the application's configuration.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ConfigCloudProvider implements CloudProviderInterface
{
    /**
     * The CloudFactory
     * @var CloudFactory
     */
    private $cloudFactory;

    /**
     * Local Cloud configuration
     * @var array
     */
    private $cloudConfig = array();


    /**
     * Constructs the ConfigCloudProvider.
     *
     * @param CloudFactory $cloudFactory Factory for creating cloud instances
     * @param array $cloudConfig The app's Cloud configuration
     */
    public function __construct(CloudFactory $cloudFactory, array $cloudConfig)
    {
        $this->cloudFactory = $cloudFactory;
        $this->cloudConfig = $cloudConfig;
    }

    /**
     * {@inheritDoc}
     */
    public function initialise(CloudManagerInterface $cloudManager)
    {
        // register all configured clouds on the the cloud manager
        foreach ($this->cloudConfig as $name => $cloud) {
            $cloudManager->registerCloud($name, $this->cloudFactory->get($cloud["id"], $cloud["account"]));
        }
    }
}
