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
        
        // set services class names parameters
        $container->setParameter(
            "xabbuh_panda.account.manager.class",
            $config["account"]["manager"]["class"]
        );
        $container->setParameter(
            "xabbuh_panda.account.config_provider.class",
            $config["account"]["config_provider"]["class"]
        );
        $container->setParameter(
            "xabbuh_panda.cloud.manager.class",
            $config["cloud"]["manager"]["class"]
        );
        $container->setParameter(
            "xabbuh_panda.cloud.factory.class",
            $config["cloud"]["factory"]["class"]
        );
        $container->setParameter(
            "xabbuh_panda.cloud.config_provider.class",
            $config["cloud"]["config_provider"]["class"]
        );
        $container->setParameter(
            "xabbuh_panda.client.api.class",
            $config["client"]["api"]["class"]
        );
        $container->setParameter(
            "xabbuh_panda.client.rest.class",
            $config["client"]["rest"]["class"]
        );
        $container->setParameter(
            "xabbuh_panda.controller.class",
            $config["controller"]["class"]
        );
        $container->setParameter(
            "xabbuh_panda.transformer.class",
            $config["transformer"]["class"]
        );
        $container->setParameter(
            "xabbuh_panda.video_uploader_extension.class",
            $config["video_uploader_extension"]["class"]
        );

        $container->setParameter("xabbuh_panda.account.default", $config["default_account"]);
        $container->setParameter("xabbuh_panda.cloud.default", $config["default_cloud"]);

        // and load the service definitions
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load("account_manager.xml");
        $loader->load("cloud_manager.xml");
        $loader->load("cloud_factory.xml");
        $loader->load("controller.xml");
        $loader->load("transformers.xml");
        $loader->load("video_uploader_extension.xml");

        $this->loadConfigAccountProvider($config["accounts"], $container, $loader);
        $this->loadConfigCloudProvider($config["clouds"], $container, $loader);
    }

    private function loadConfigAccountProvider(array $accounts, ContainerBuilder $container, XmlFileLoader $loader)
    {
        $loader->load("config_account_provider.xml");
        $configAccountProvider = $container->getDefinition("xabbuh_panda.config_account_provider");
        $configAccountProvider->addArgument($accounts);
    }

    private function loadConfigCloudProvider(array $clouds, ContainerBuilder $container, XmlFileLoader $loader)
    {
        // add missing account key if the default account should be used
        foreach ($clouds as &$cloud) {
            if (!isset($cloud["account"])) {
                $cloud["account"] = $container->getParameter("xabbuh_panda.account.default");
            }
        }
        $loader->load("config_cloud_provider.xml");
        $configCloudProvider = $container->getDefinition("xabbuh_panda.config_cloud_provider");
        $configCloudProvider->addArgument($clouds);
    }
}
