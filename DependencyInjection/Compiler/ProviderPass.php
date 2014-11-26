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
        $orderedDomains = $container->getParameter('ongr_admin.settings_container.domains');
        $providers = array_flip($orderedDomains);

        $providerDefinitions = $container->findTaggedServiceIds('ongr_admin.settings_provider');

        foreach ($providerDefinitions as $serviceId => $tags) {
            foreach ($tags as $tag) {
                $priority = !empty($tag['priority']) ? $tag['priority'] : self::DEFAULT_PROVIDER_PRIORITY;
                if (isset($tag['domain']) && array_key_exists($tag['domain'], $providers)) {
                    $providers[$tag['domain']] = ['id' => $serviceId, 'priority' => $priority];
                } else {
                    $providers[] = ['id' => $serviceId, 'priority' => $priority];
                }
            }
        }

        foreach ($providers as $domain => &$tempProvider) {
            if (!is_array($tempProvider) && is_string($domain)) {
                $tempProvider = [
                    'id' => $this->generateProvider($container, $domain),
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

        $settingsContainer = $container->getDefinition('ongr_admin.settings_container');

        foreach ($providers as $provider) {
            $settingsContainer->addMethodCall('addProvider', [new Reference($provider['id'])]);
        }
    }

    /**
     * Generates provider by given domain.
     *
     * @param ContainerBuilder $container
     * @param string           $domain
     *
     * @return string
     */
    protected function generateProvider(ContainerBuilder $container, $domain)
    {
        $id = "ongr_admin.dynamic_provider.{$domain}";
        $provider = new Definition($container->getParameter('ongr_admin.settings_provider.class'), [$domain]);
        $provider->addMethodCall('setManager', [new Reference('es.manager')]);
        $provider->addTag('ongr_admin.settings_provider', ['domain' => $domain]);
        $container->setDefinition($id, $provider);

        return $id;
    }
}
