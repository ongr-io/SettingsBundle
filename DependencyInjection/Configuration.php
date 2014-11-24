<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Loader;

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

        $root = $treeBuilder->root('ongr_admin');

        $root->children()
                ->arrayNode('power_user')
                    ->children()
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
