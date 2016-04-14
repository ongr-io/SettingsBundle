<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that loads and manages bundle configuration.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $root = $treeBuilder->root('ongr_settings');

        $root->children()
                ->arrayNode('index_settings')
                    ->prototype('variable')
                        ->treatNullLike([])
                    ->end()
                ->end()
                ->arrayNode('profiles')
                    ->defaultValue(['default'])
                    ->prototype('scalar')
                    ->end()
                ->end()
                ->arrayNode('connection')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('index_name')
                                ->defaultValue('ongr-settings')
                                ->info('Index name for settings')
                            ->end()
                            ->scalarNode('repository')
                                ->defaultValue('es.manager.settings.setting')
                                ->info('Elasticsearch repository for settings')
                            ->end()
                            ->scalarNode('host')
                                ->info('Address of your settings database')
                                ->defaultValue('127.0.0.1')
                            ->end()
                            ->integerNode('port')
                                ->info('Port of your settings database')
                                ->defaultValue(9200)
                            ->end()
                        ->end()
                ->end()
                ->arrayNode('user_settings')
                    ->children()
                        ->scalarNode('user_property')
                            ->defaultValue('username')
                            ->info('Unique user value to use for personal settings')
                        ->end()
                        ->arrayNode('categories')
                            ->isRequired()
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('name')
                                        ->isRequired()
                                        ->info('setting category name')
                                    ->end()
                                    ->scalarNode('description')
                                        ->info('setting category description')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('settings')
                            ->isRequired()
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('name')
                                        ->isRequired()
                                        ->info('setting name')
                                    ->end()
                                    ->scalarNode('description')
                                        ->info('setting description')
                                    ->end()
                                    ->scalarNode('category')
                                        ->isRequired()
                                        ->info('setting category')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
