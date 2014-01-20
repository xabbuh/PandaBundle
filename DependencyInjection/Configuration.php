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
        $rootNode = $treeBuilder->root('xabbuh_panda');

        $rootNode
            ->fixXmlConfig('account')
            ->fixXmlConfig('cloud')
            ->children()
                ->scalarNode('default_account')->defaultValue('default')->end()
                ->arrayNode('accounts')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('access_key')->isRequired()->end()
                            ->scalarNode('secret_key')->isRequired()->end()
                            ->scalarNode('api_host')->defaultValue('api.pandastream.com')->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('default_cloud')->defaultValue('default')->end()
                ->arrayNode('clouds')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('id')->isRequired()->end()
                            ->scalarNode('account')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('video_uploader')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('multiple_files')
                            ->defaultValue(false)
                        ->end()
                        ->booleanNode('cancel_button')
                            ->defaultValue(true)
                        ->end()
                        ->booleanNode('progress_bar')
                            ->defaultValue(true)
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
