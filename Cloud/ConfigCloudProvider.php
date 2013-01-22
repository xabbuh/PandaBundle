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

use Xabbuh\PandaBundle\Account\AccountManager;

/**
 * Provider for clouds configured in the application's configuration.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ConfigCloudProvider implements CloudProviderInterface
{
    /**
     * @var CloudManagerInterface
     */
    private $cloudManager;

    /**
     * @var CloudFactory
     */
    private $cloudFactory;

    /**
     * @var array
     */
    private $cloudConfig = array();


    public function __construct(CloudManagerInterface $cloudManager, CloudFactory $cloudFactory, array $cloudConfig)
    {
        $this->cloudManager = $cloudManager;
        $this->cloudFactory = $cloudFactory;
        $this->cloudConfig = $cloudConfig;
    }

    /**
     * {@inheritDoc}
     */
    public function initialise()
    {
        // register all configured clouds on the the cloud manager
        foreach ($this->cloudConfig as $name => $cloud) {
            $this->cloudManager->registerCloud($name, $this->cloudFactory->get($cloud["id"], $cloud["account"]));
        }
    }
}
