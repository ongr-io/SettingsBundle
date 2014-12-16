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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages bundle configuration.
 */
class ONGRAdminExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Set connection settings.
        $container->setParameter('ongr_admin.connection.index_name', $config['connection']['index_name']);
        $container->setParameter('ongr_admin.connection.port', $config['connection']['port']);
        $container->setParameter('ongr_admin.connection.host', $config['connection']['host']);

        // Set profiles.
        $container->setParameter('ongr_admin.settings_container.profiles', $config['profiles']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services/auth.yml');
        $loader->load('services/flash_bag.yml');
        $loader->load('services/twig_extension.yml');
        $loader->load('services/admin_settings.yml');
        $loader->load('services/settings.yml');
        $loader->load('services/parameters.yml');

        if (isset($config['admin_user'])) {
            $this->loadAdminSettings($config['admin_user'], $container);
        }

        $this->injectAdminSettings($container);
    }

    /**
     * Sets parameters for admin user.
     *
     * @param array            $config
     * @param ContainerBuilder $containerBuilder
     */
    protected function loadAdminSettings($config, ContainerBuilder $containerBuilder)
    {
        $containerBuilder->setParameter('ongr_admin.settings.categories', $config['categories']);
        $containerBuilder->setParameter('ongr_admin.settings.settings', $config['settings']);
    }

    /**
     * Injects additional Admin User settings to service container.
     *
     * @param ContainerBuilder $container
     */
    protected function injectAdminSettings(ContainerBuilder $container)
    {
        // Add category for ONGR admin settings.
        $categories = $container->getParameter('ongr_admin.settings.categories');
        $categories['ongr_admin_settings'] = [
            'name' => 'ONGR admin',
            'description' => 'Special settings for ONGR admin',
        ];
        $categories['ongr_admin_profiles'] = [
            'name' => 'ONGR admin profiles',
            'description' => 'Profiles for profile settings',
        ];
        $container->setParameter('ongr_admin.settings.categories', $categories);

        // Inject custom Admin User settings.
        $settings = $container->getParameter('ongr_admin.settings.settings');
        $settings['ongr_admin_live_settings'] = [
            'name' => 'Show settings widget in frontend',
            'category' => 'ongr_admin_settings',
            'description' => 'Enables Edit button in shop frontend',
        ];
        $container->setParameter('ongr_admin.settings.settings', $settings);
    }
}
