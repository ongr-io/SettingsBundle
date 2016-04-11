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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages bundle configuration.
 */
class ONGRSettingsExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Set connection settings.
        $container->setParameter('ongr_settings.connection.index_name', $config['connection']['index_name']);
        $container->setParameter('ongr_settings.connection.port', $config['connection']['port']);
        $container->setParameter('ongr_settings.connection.host', $config['connection']['host']);
        $container->setParameter('ongr_settings.connection.repository', $config['connection']['repository']);

        // Set profiles.
        $container->setParameter('ongr_settings.settings_container.profiles', $config['profiles']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services/twig_extension.yml');
        $loader->load('services/personal_settings.yml');
        $loader->load('services/general_settings.yml');

        $loader->load('filter_container.yml');

        if (isset($config['user_settings'])) {
            $this->loadPersonalSettings($config['user_settings'], $container);
        }

        $this->injectPersonalSettings($container);

        $this->setFilterManager($container);
    }

    /**
     * Sets parameters for admin user.
     *
     * @param array            $config
     * @param ContainerBuilder $containerBuilder
     */
    protected function loadPersonalSettings($config, ContainerBuilder $containerBuilder)
    {
        $containerBuilder->setParameter('ongr_settings.settings.categories', $config['categories']);
        $containerBuilder->setParameter('ongr_settings.settings.settings', $config['settings']);
    }

    /**
     * Sets parameters for settings provider.
     *
     * @param array            $config
     * @param ContainerBuilder $containerBuilder
     */
    protected function loadSettingsProvider($config, ContainerBuilder $containerBuilder)
    {
        $containerBuilder->setParameter('ongr_settings.settings.categories', $config['categories']);
    }

    /**
     * Sets parameters for filter manager.
     *
     * @param ContainerBuilder $container
     */
    protected function setFilterManager(ContainerBuilder $container)
    {
        $definition = new Definition(
            'ONGR\FilterManagerBundle\Search\FilterManager',
            [
                new Reference('ongr_settings.filter_container'),
                new Reference($container->getParameter('ongr_settings.connection.repository')),
            ]
        );
        $container->setDefinition('ongr_settings.filter_manager', $definition);
    }

    /**
     * Injects additional General User settings to service container.
     *
     * @param ContainerBuilder $container
     */
    protected function injectPersonalSettings(ContainerBuilder $container)
    {
        // Add category for ONGR Settings settings.
        $categories = $container->getParameter('ongr_settings.settings.categories');
        $categories['ongr_settings_settings'] = [
            'name' => 'ONGR Settings',
            'description' => 'Special settings for ONGR Settings',
        ];
        $categories['ongr_settings_profiles'] = [
            'name' => 'ONGR Settings profiles',
            'description' => 'Profiles for profile settings',
        ];
        $container->setParameter('ongr_settings.settings.categories', $categories);

        // Inject custom General User settings.
        $settings = $container->getParameter('ongr_settings.settings.settings');
        $settings['ongr_settings_live_settings'] = [
            'name' => 'Show settings widget in frontend',
            'category' => 'ongr_settings_settings',
            'description' => 'Enables Edit button in shop frontend',
        ];
        $container->setParameter('ongr_settings.settings.settings', $settings);
    }
}
