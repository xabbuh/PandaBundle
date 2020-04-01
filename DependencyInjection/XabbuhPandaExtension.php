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
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Xabbuh\PandaClient\Api\CloudInterface;

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
        $loader->load('commands.xml');
        $loader->load('controller.xml');
        $loader->load('transformers.xml');
        $loader->load('video_uploader_extension.xml');

        $knownAccounts = $this->loadAccounts($config['accounts'], $container);
        $knownClouds = $this->loadClouds($config['clouds'], $container, $knownAccounts, $config['default_account']);

        if (isset($knownClouds[$config['default_cloud']])) {
            $container->setAlias(CloudInterface::class, new Alias($knownClouds[$config['default_cloud']], false));
        }

        $baseHttpClientDefinition = $container->getDefinition('xabbuh_panda.http_client');

        foreach (array('client' => 0, 'request_factory' => 1, 'stream_factory' => 2) as $key => $argumentIndex) {
            if (null !== $config['httplug'][$key]) {
                $baseHttpClientDefinition->replaceArgument($argumentIndex, new Reference($config['httplug'][$key]));
            }
        }
    }

    private function loadAccounts(array $accounts, ContainerBuilder $container)
    {
        $accountManagerDefinition = $container->getDefinition('xabbuh_panda.account_manager');

        $knownAccounts = array();

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
            $knownAccounts[$name] = $id;
        }

        return $knownAccounts;
    }

    private function loadClouds(array $clouds, ContainerBuilder $container, array $knownAccounts, $defaultAccount)
    {
        $cloudManagerDefinition = $container->getDefinition('xabbuh_panda.cloud_manager');

        $knownClouds = array();

        foreach ($clouds as $name => $cloudConfig) {
            $accountName = isset($cloudConfig['account']) ? $cloudConfig['account'] : $defaultAccount;

            if (!isset($knownAccounts[$accountName])) {
                throw new \InvalidArgumentException(sprintf('The account `%s` configured for the cloud `%s` is not one of the configured accounts.', $accountName, $name));
            }

            if (class_exists('Symfony\Component\DependencyInjection\ChildDefinition')) {
                $httpClientDefinition = new ChildDefinition('xabbuh_panda.http_client');
            } else {
                $httpClientDefinition = new DefinitionDecorator('xabbuh_panda.http_client');
            }

            $httpClientDefinition->setPublic(false);
            // Get a reference to the account service directly, to avoid instantiating the AccountManager (and all other
            // accounts due to the AccountManager not supporting lazy-loading)
            $httpClientDefinition->addMethodCall('setAccount', array(new Reference($knownAccounts[$accountName])));
            $httpClientDefinition->addMethodCall('setCloudId', array($cloudConfig['id']));

            $httpClientId = 'xabbuh_panda.http_client.'.strtr($name, ' -', '_');

            $container->setDefinition($httpClientId, $httpClientDefinition);

            // register each cloud as a service
            $cloudDefinition = new Definition('Xabbuh\PandaClient\Api\Cloud');
            $cloudDefinition->addMethodCall('setHttpClient', array(new Reference($httpClientId)));
            $cloudDefinition->addMethodCall('setTransformers', array(new Reference('xabbuh_panda.transformer')));

            $id = 'xabbuh_panda.'.strtr($name, ' -', '_').'_cloud';
            $container->setDefinition($id, $cloudDefinition);

            // and pass it to the manager's registerAccount() method
            $cloudManagerDefinition->addMethodCall(
                'registerCloud',
                array($name, new Reference($id))
            );
            $knownClouds[$name] = $id;
        }

        return $knownClouds;
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
