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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * XabbuhPandaExtension configuration structure.
 * 
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root("xabbuh_panda");
        
        $rootNode
            ->children()
                ->scalarNode("default_account")->defaultValue("default")->end()
                ->arrayNode("accounts")
                    ->prototype("array")
                        ->children()
                            ->scalarNode("access_key")->isRequired()->end()
                            ->scalarNode("secret_key")->isRequired()->end()
                            ->scalarNode("api_host")->defaultValue("api.pandastream.com")->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode("default_cloud")->defaultValue("default")->end()
                ->arrayNode("clouds")
                    ->prototype("array")
                        ->children()
                            ->scalarNode("id")->isRequired()->end()
                            ->scalarNode("account")->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode("account")
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode("manager")
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode("class")
                                    ->defaultValue("Xabbuh\\PandaBundle\\Account\\AccountManager")
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode("config_provider")
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode("class")
                                    ->defaultValue("Xabbuh\\PandaBundle\\Account\\ConfigAccountProvider")
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode("cloud")
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode("manager")
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode("class")
                                    ->defaultValue("Xabbuh\\PandaBundle\\Cloud\\CloudManager")
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode("factory")
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode("class")
                                    ->defaultValue("Xabbuh\\PandaBundle\\Cloud\\CloudFactory")
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode("config_provider")
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode("class")
                                    ->defaultValue("Xabbuh\\PandaBundle\\Cloud\\ConfigCloudProvider")
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode("client")
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode("api")
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode("class")
                                    ->defaultValue("Xabbuh\\PandaClient\\PandaApi")
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode("rest")
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode("class")
                                    ->defaultValue("Xabbuh\\PandaClient\\PandaRestClient")
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode("controller")
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode("class")
                            ->defaultValue("Xabbuh\\PandaBundle\\Controller\\Controller")
                        ->end()
                    ->end()
                ->end()
                ->arrayNode("transformer")
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode("factory_class")
                            ->defaultValue("Xabbuh\\PandaBundle\\Services\\TransformerFactory")
                        ->end()
                        ->scalarNode("cloud_transformer_class")
                            ->defaultValue("Xabbuh\\PandaBundle\\Transformers\\CloudTransformer")
                        ->end()
                        ->scalarNode("encoding_transformer_class")
                            ->defaultValue("Xabbuh\\PandaBundle\\Transformers\\EncodingTransformer")
                        ->end()
                        ->scalarNode("notifications_transformer_class")
                            ->defaultValue("Xabbuh\\PandaBundle\\Transformers\\NotificationsTransformer")
                        ->end()
                        ->scalarNode("profile_transformer_class")
                            ->defaultValue("Xabbuh\\PandaBundle\\Transformers\\ProfileTransformer")
                        ->end()
                        ->scalarNode("video_transformer_class")
                            ->defaultValue("Xabbuh\\PandaBundle\\Transformers\\VideoTransformer")
                        ->end()
                    ->end()
                ->end()
                ->arrayNode("video_uploader_extension")
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode("class")
                            ->defaultValue("Xabbuh\\PandaBundle\\Form\\Extension\\VideoUploaderExtension")
                        ->end()
                    ->end()
                ->end()
                ->arrayNode("video_uploader")
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode("multiple_files")
                            ->defaultValue(false)
                        ->end()
                        ->booleanNode("cancel_button")
                            ->defaultValue(true)
                        ->end()
                        ->booleanNode("progress_bar")
                            ->defaultValue(true)
                        ->end()
                    ->end()
                ->end()
            ->end();
        
        return $treeBuilder;
    }
}
