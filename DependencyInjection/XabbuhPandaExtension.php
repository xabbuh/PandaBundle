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
        
        // set cloud access parameters
        $container->setParameter("xabbuh_panda.cloud_id", $config["cloud"]["cloud_id"]);
        $container->setParameter("xabbuh_panda.access_key", $config["cloud"]["access_key"]);
        $container->setParameter("xabbuh_panda.secret_key", $config["cloud"]["secret_key"]);
        $container->setParameter("xabbuh_panda.api_host", $config["cloud"]["api_host"]);
        
        // set services class names parameters
        $container->setParameter("xabbuh_panda.client.class", $config["client"]["class"]);
        $container->setParameter("xabbuh_panda.client.api.class", $config["client"]["api"]["class"]);
        $container->setParameter("xabbuh_panda.client.rest.class", $config["client"]["rest"]["class"]);
        $container->setParameter("xabbuh_panda.controller.class", $config["controller"]["class"]);
        $container->setParameter("xabbuh_panda.transformer.class", $config["transformer"]["class"]);

        // and load the service definitions
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('client.xml');
        $loader->load("controller.xml");
        $loader->load("transformers.xml");
    }
}
