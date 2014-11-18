<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace ONGR\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Pass that collects settings structure modifying tag.
 */
class SettingsModifierPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $collectionService = $container->getDefinition('ongr_utils.settings.settings_structure');

        $tagName = 'ongr_utils.settings_provider';
        $taggedDefinitions = $container->findTaggedServiceIds($tagName);

        foreach ($taggedDefinitions as $id => $tags) {
            foreach ($tags as $tag) {
                $method = 'getSettings';
                if (isset($tag['method'])) {
                    $method = $tag['method'];
                }
                $collectionService->addMethodCall('extractSettings', [new Reference($id), $method]);
            }
        }
    }
}
