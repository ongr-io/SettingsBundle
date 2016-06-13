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
                ->arrayNode('repos')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('profile')
                                ->defaultValue('es.manager.setting.profile')
                                ->info('Repository service name for profile type')
                            ->end()
                            ->scalarNode('setting')
                                ->defaultValue('es.manager.setting.setting')
                                ->info('Repository service name for settings type')
                            ->end()
                        ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
