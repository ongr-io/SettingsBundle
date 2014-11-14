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

        // Set domains.
        $container->setParameter('ongr_admin.settings_container.domains', $config['domains']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('browser.yml');
        $loader->load('injection.yml');
        $loader->load('connection.yml');

        $this->injectDDAL($config, $container);
        $this->injectPowerUserSettings($container);
    }

    /**
     * Injects mapping and creates sessions for ddal.
     *
     * @param array            $config    Asfsdf.
     * @param ContainerBuilder $container Ijioi.
     */
    protected function injectDDAL($config, ContainerBuilder $container)
    {
        if (!empty($config['index_settings'])) {
            $settings = $container->getParameter('ongr_admin.settings_model_connection.settings');
            $settings['index'] = array_merge($settings['index'], $config['index_settings']);
            $container->setParameter('ongr_admin.settings_model_connection.settings', $settings);
        }

        $ddalConfig = $container->getParameter('ongr_admin.ongr_ddal.model_map');
        $contentConfig = $container->getParameter('ongr_admin.ongr_ddal.model_map');
        $ddalConfig = array_merge($ddalConfig, $contentConfig);
        $container->setParameter('ongr_ddal.model_map', $ddalConfig);
    }

    /**
     * Injects additional Power User settings to service container.
     *
     * @param ContainerBuilder $container
     */
    protected function injectPowerUserSettings(ContainerBuilder $container)
    {
        // Add category for FOXX admin settings.
        $categories = $container->getParameter('ongr_utils.settings.categories');
        $categories['ongr_admin_settings'] = [
            'name' => 'FOXX admin',
            'description' => 'Special settings for FOXX admin',
        ];
        $categories['ongr_admin_domains'] = [
            'name' => 'FOXX admin domains',
            'description' => 'Profiles for domain settings',
        ];
        $container->setParameter('ongr_utils.settings.categories', $categories);


        // Inject custom Power User settings.
        $settings = $container->getParameter('ongr_utils.settings.settings');
        $settings['ongr_admin_live_settings'] = [
            'name' => 'Show settings widget in frontend',
            'category' => 'ongr_admin_settings',
            'description' => 'Enables Edit button in shop frontend',
        ];
        $container->setParameter('ongr_utils.settings.settings', $settings);
    }
}
