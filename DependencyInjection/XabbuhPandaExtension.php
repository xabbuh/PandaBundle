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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * XabbuhPandaExtension.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class XabbuhPandaExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(
            'xabbuh_panda.video_uploader.multiple_files',
            $config['video_uploader']['multiple_files']
        );
        $container->setParameter(
            'xabbuh_panda.video_uploader.cancel_button',
            $config['video_uploader']['cancel_button']
        );
        $container->setParameter(
            'xabbuh_panda.video_uploader.progress_bar',
            $config['video_uploader']['progress_bar']
        );

        $container->setParameter('xabbuh_panda.account.default', $config['default_account']);
        $container->setParameter('xabbuh_panda.cloud.default', $config['default_cloud']);

        // and load the service definitions
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('account_manager.xml');
        $loader->load('cloud_manager.xml');
        $loader->load('cloud_factory.xml');
        $loader->load('controller.xml');
        $loader->load('transformers.xml');
        $loader->load('video_uploader_extension.xml');

        $this->loadAccounts($config['accounts'], $container);
        $this->loadClouds($config['clouds'], $container);
    }

    private function loadAccounts(array $accounts, ContainerBuilder $container)
    {
        $accountManagerDefinition = $container->getDefinition('xabbuh_panda.account_manager');

        foreach ($accounts as $name => $accountConfig) {
            // register each account as a service
            $accountDefinition = new Definition(
                'Xabbuh\PandaClient\Api\Account',
                array(
                    $accountConfig['access_key'],
                    $accountConfig['secret_key'],
                    $accountConfig['api_host']
                )
            );
            $id = 'xabbuh_panda.'.strtr($name, ' -', '_').'_account';
            $container->setDefinition($id, $accountDefinition);

            // and pass it to the manager's registerAccount() method
            $accountManagerDefinition->addMethodCall(
                'registerAccount',
                array($name, new Reference($id))
            );
        }
    }

    private function loadClouds(array $clouds, ContainerBuilder $container)
    {
        $cloudManagerDefinition = $container->getDefinition('xabbuh_panda.cloud_manager');

        foreach ($clouds as $name => $cloudConfig) {
            // register each cloud as a service
            $cloudDefinition = new Definition(
                'Xabbuh\PandaClient\Api\Cloud',
                array(
                    $cloudConfig['id'],
                    isset($cloudConfig['account']) ? $cloudConfig['account'] : null,
                )
            );
            $cloudDefinition->setFactoryService('xabbuh_panda.cloud_factory');
            $cloudDefinition->setFactoryMethod('get');
            $id = 'xabbuh_panda.'.strtr($name, ' -', '_').'_cloud';
            $container->setDefinition($id, $cloudDefinition);

            // and pass it to the manager's registerAccount() method
            $cloudManagerDefinition->addMethodCall(
                'registerCloud',
                array($name, new Reference($id))
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getNamespace()
    {
        return 'http://xabbuh.de/schema/dic/xabbuh/panda';
    }

    /**
     * {@inheritDoc}
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }
}
