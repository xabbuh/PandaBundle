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
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root("xabbuh_pandastream_encoder");
        
        $rootNode
            ->children()
                ->arrayNode("cloud")
                    ->children()
                        ->scalarNode("cloud_id")->end()
                        ->scalarNode("access_key")->end()
                        ->scalarNode("secret_key")->end()
                        ->scalarNode("api_host")->defaultValue("api.pandastream.com")->end()
                    ->end()
                ->end()
                ->arrayNode("client")
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode("class")
                            ->defaultValue("Xabbuh\PandaBundle\Services\Client")
                        ->end()
                        ->arrayNode("api")
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode("class")
                                    ->defaultValue("Xabbuh\PandaClient\PandaApi")
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode("rest")
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode("class")
                                    ->defaultValue("Xabbuh\PandaClient\PandaRestClient")
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode("controller")
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode("class")
                            ->defaultValue("Xabbuh\PandaBundle\Controller\Controller")
                        ->end()
                    ->end()
                ->end()
                ->arrayNode("transformer")
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode("class")
                            ->defaultValue("Xabbuh\PandaBundle\Services\TransformerFactory")
                        ->end()
                    ->end()
                ->end()
            ->end();
        
        return $treeBuilder;
    }
}
