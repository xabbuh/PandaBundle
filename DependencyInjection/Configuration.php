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
                    ->end()
                ->end()
            ->end();
        
        return $treeBuilder;
    }
}
