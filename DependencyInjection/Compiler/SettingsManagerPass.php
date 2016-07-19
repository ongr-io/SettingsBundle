<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class SettingsManagerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $repo = $container->getParameter('ongr_settings.repo');
        $filterManager = new Definition('ONGR\FilterManagerBundle\Search\FilterManager', [
            new Reference('ongr_settings.filter_container'),
            new Reference($repo)
        ]);

        $container->setDefinition('ongr_filter_manager.settings', $filterManager);

        $settingsManager = new Definition('ONGR\SettingsBundle\Service\SettingsManager', [
            new Reference($repo),
            new Reference('event_dispatcher')
        ]);
        $settingsManager->addMethodCall('setActiveProfilesSettingName', ['%ongr_settings.active_profiles%']);
        
        $container->setDefinition('ongr_settings.settings_manager', $settingsManager);
    }
}