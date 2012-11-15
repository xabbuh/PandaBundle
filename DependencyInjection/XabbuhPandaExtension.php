<?php

/*
* This file is part of the XabbuhPandaBundle package.
*
* (c) Christian Flothmann <christian.flothmann@xabbuh.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Xabbuh\PandaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * XabbuhPandaExtension.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class XabbuhPandaExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // parse the bundle's configuration
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        // set parameters
        $container->setParameter("xabbuh_panda.cloud_id", $config["cloud_id"]);
        $container->setParameter("xabbuh_panda.access_key", $config["access_key"]);
        $container->setParameter("xabbuh_panda.secret_key", $config["secret_key"]);
        $container->setParameter("xabbuh_panda.api_host", $config["api_host"]);

        // and load the service definition
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('client.xml');
    }
}
