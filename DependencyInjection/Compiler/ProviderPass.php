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
 * Collects settings providers and injects to settings container.
 */
class ProviderPass implements CompilerPassInterface
{
    /**
     * Default priority index for providers.
     */
    const DEFAULT_PROVIDER_PRIORITY = 1000;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $orderedProfiles = $container->getParameter('ongr_settings.settings_container.profiles');
        $providers = array_flip($orderedProfiles);

        $providerDefinitions = $container->findTaggedServiceIds('ongr_settings.settings_provider');

        foreach ($providerDefinitions as $serviceId => $tags) {
            foreach ($tags as $tag) {
                $priority = !empty($tag['priority']) ? $tag['priority'] : self::DEFAULT_PROVIDER_PRIORITY;
                if (isset($tag['profile']) && array_key_exists($tag['profile'], $providers)) {
                    $providers[$tag['profile']] = ['id' => $serviceId, 'priority' => $priority];
                } else {
                    $providers[] = ['id' => $serviceId, 'priority' => $priority];
                }
            }
        }

        foreach ($providers as $profile => &$tempProvider) {
            if (!is_array($tempProvider) && is_string($profile)) {
                $tempProvider = [
                    'id' => $this->generateProvider($container, $profile),
                    'priority' => self::DEFAULT_PROVIDER_PRIORITY,
                ];
            }
        }

        // Sort providers by priority, higher to the end. Need to use array_reverse here as array with all equal
        // elements comes out reversed compared to original.
        uasort(
            $providers,
            function ($a, $b) {
                if ($a['priority'] == $b['priority']) {
                    return 0;
                }

                return ($a['priority'] < $b['priority']) ? 1 : -1;
            }
        );
        $providers = array_reverse($providers);

        $settingsContainer = $container->getDefinition('ongr_settings.settings_container');

        foreach ($providers as $provider) {
            $settingsContainer->addMethodCall('addProvider', [new Reference($provider['id'])]);
        }
    }

    /**
     * Generates provider by given profile.
     *
     * @param ContainerBuilder $container
     * @param string           $profile
     *
     * @return string
     */
    protected function generateProvider(ContainerBuilder $container, $profile)
    {
        $id = "ongr_settings.dynamic_provider.{$profile}";

        $manager = $container->getParameter('ongr_settings.connection.manager');

        $provider = new Definition($container->getParameter('ongr_settings.settings_provider.class'), [$profile]);
        $provider->addMethodCall('setManager', [new Reference($manager)]);
        $provider->addTag('ongr_settings.settings_provider', ['profile' => $profile]);
        $container->setDefinition($id, $provider);

        return $id;
    }
}
