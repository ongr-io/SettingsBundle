<?php

/*
 *************************************************************************
 * NFQ eXtremes CONFIDENTIAL
 * [2013] - [2014] NFQ eXtremes UAB
 * All Rights Reserved.
 *************************************************************************
 * NOTICE: 
 * All information contained herein is, and remains the property of NFQ eXtremes UAB.
 * Dissemination of this information or reproduction of this material is strictly forbidden
 * unless prior written permission is obtained from NFQ eXtremes UAB.
 *************************************************************************
 */

namespace Fox\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages bundle configuration
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fox_admin');
        $rootNode
            ->children()
                ->arrayNode('index_settings')
                    ->prototype('variable')
                        ->treatNullLike([])
                    ->end()
                ->end()
                ->arrayNode('domains')
                    ->defaultValue(['default'])
                    ->prototype('scalar')
                    ->end()
                ->end()
                ->arrayNode('connection')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('index_name')
                                ->defaultValue('fox-settings')
                                ->info('Index name for settings')
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
            ->end();

        return $treeBuilder;
    }
}
