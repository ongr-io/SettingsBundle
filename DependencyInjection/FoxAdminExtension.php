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

namespace Fox\AdminBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages bundle configuration
 */
class FoxAdminExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // set connection settings
        $container->setParameter('fox_admin.connection.index_name', $config['connection']['index_name']);
        $container->setParameter('fox_admin.connection.port', $config['connection']['port']);
        $container->setParameter('fox_admin.connection.host', $config['connection']['host']);

        // set domains
        $container->setParameter('fox_admin.settings_container.domains', $config['domains']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('browser.yml');
        $loader->load('injection.yml');
        $loader->load('connection.yml');

        $this->injectDDAL($config, $container);
        $this->injectPowerUserSettings($container);
    }

    /**
     * Injects mapping and creates sessions for ddal
     */
    protected function injectDDAL($config, ContainerBuilder $container)
    {
        if (!empty($config['index_settings'])) {
            $settings = $container->getParameter('fox_admin.settings_model_connection.settings');
            $settings['index'] = array_merge($settings['index'], $config['index_settings']);
            $container->setParameter('fox_admin.settings_model_connection.settings', $settings);
        }

        $ddalConfig = $container->getParameter('fox_ddal.model_map');
        $contentConfig = $container->getParameter('fox_admin.fox_ddal.model_map');
        $ddalConfig = array_merge($ddalConfig, $contentConfig);
        $container->setParameter('fox_ddal.model_map', $ddalConfig);
    }

    /**
     * Injects additional Power User settings to service container
     *
     * @param ContainerBuilder $container
     */
    protected function injectPowerUserSettings(ContainerBuilder $container)
    {
        // Add category for FOXX admin settings
        $categories = $container->getParameter('fox_utils.settings.categories');
        $categories['fox_admin_settings'] = [
            'name' => 'FOXX admin',
            'description' => 'Special settings for FOXX admin',
        ];
        $categories['fox_admin_domains'] = [
            'name' => 'FOXX admin domains',
            'description' => 'Profiles for domain settings',
        ];
        $container->setParameter('fox_utils.settings.categories', $categories);


        // Inject custom Power User settings
        $settings = $container->getParameter('fox_utils.settings.settings');
        $settings['fox_admin_live_settings'] = [
            'name' => 'Show settings widget in frontend',
            'category' => 'fox_admin_settings',
            'description' => 'Enables Edit button in shop frontend',
        ];
        $container->setParameter('fox_utils.settings.settings', $settings);
    }
}
