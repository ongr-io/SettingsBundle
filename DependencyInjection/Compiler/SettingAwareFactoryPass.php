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

namespace Fox\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Prepares proxies for settings aware services
 */
class SettingAwareFactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definitions = $container->findTaggedServiceIds('fox_admin.setting_aware');

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
            $proxy->setFactoryService('fox_admin.setting_aware_service_factory');
            $proxy->setFactoryMethod('get');

            unset($initialTags['fox_admin.setting_aware']);
            $proxy->setTags($initialTags);

            $container->setDefinition($serviceId, $proxy);
        }
    }
}
