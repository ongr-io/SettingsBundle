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

/**
 * Prepares proxies for settings aware services.
 */
class SettingAwareFactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definitions = $container->findTaggedServiceIds('ongr_settings.setting_aware');

        foreach ($definitions as $serviceId => $tags) {
            $definition = $container->getDefinition($serviceId);
            $initialTags = $definition->getTags();
            $definition->clearTags();
            $container->setDefinition("{$serviceId}_base", $definition);

            $callMap = [];

            foreach ($tags as $tag) {
                $callMap[$tag['setting']] = isset($tag['method']) ? $tag['method'] : null;
            }

            $proxy = new Definition(
                $definition->getClass(),
                [$callMap, new Reference("{$serviceId}_base")]
            );
            $factory = new Reference('ongr_settings.setting_aware_service_factory');
            $proxy->setFactory([$factory, 'Get']);

            unset($initialTags['ongr_settings.setting_aware']);
            $proxy->setTags($initialTags);

            $container->setDefinition($serviceId, $proxy);
        }
    }
}
