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

namespace ONGR\AdminBundle\Tests\Functional\DependencyInjection;

use ONGR\AdminBundle\DependencyInjection\FoxAdminExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FoxAdminExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Prepare container for testing
     *
     * @return ContainerBuilder
     */
    public function getContainer()
    {
        $container = new ContainerBuilder();

        $container->setParameter('ongr_ddal.model_map', []);
        $container->setParameter('ongr_utils.settings.categories', []);
        $container->setParameter('ongr_utils.settings.settings', []);
        $container->setParameter(
            'ongr_ddal.driver_map.elastic_search',
            [
                'map' => []
            ]
        );

        return $container;
    }

    /**
     * Test if DDAL settings are loaded
     */
    public function testLoadDDALSettings()
    {
        $container = $this->getContainer();
        $extension = new FoxAdminExtension();

        $extension->load([], $container);

        $this->assertEquals(
            [
                'SettingModel.class' => 'ONGR\AdminBundle\Model\SettingModel',
            ],
            $container->getParameter('ongr_ddal.model_map')
        );
        $this->assertTrue($container->hasDefinition('ongr_admin.elastic_search_driver'));
        $definition = $container->getDefinition('ongr_admin.elastic_search_driver');
        $this->assertEquals('Fox\DDALBundle\ElasticSearch\ElasticSearchDriver', $definition->getClass());
        $this->assertArrayHasKey('setting', $container->getParameter('ongr_admin.connection.mapping'));
    }

    /**
     * Tests if SettingContainer parameters are loaded correctly
     */
    public function testLoadContainerSettings()
    {
        $container = $this->getContainer();
        $extension = new FoxAdminExtension();

        $extension->load(['ongr_admin' => ['domains' => ['one', 'two']]], $container);

        $this->assertTrue($container->hasParameter('ongr_admin.settings_container.domains'));
        $this->assertEquals(['one', 'two'], $container->getParameter('ongr_admin.settings_container.domains'));
    }

    /**
     * Data provider for testLoadConnectionSettings
     *
     * @return array[]
     */
    public function loadConnectionSettingsData()
    {
        // #0 default values are used
        $container = $this->getContainer();
        $config = [
            'connection' => [
                    'index_name' => 'fox-settings',
                    'host' => '127.0.0.1',
                    'port' => 9200
                ]
        ];
        $out[] = [
            $container,
            $config,
            9200,
            '127.0.0.1',
            'fox-settings',
            [
                'index' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 1,
                    'refresh_interval' => -1
                ]
            ]
        ];

        // #1 custom
        $container = $this->getContainer();
        $config = [
            'index_settings' => [
                'number_of_shards' => 3
            ],
            'connection' => [
                    'index_name' => 'fox-test',
                    'host' => '156.39.58.10',
                    'port' => 6666
                ]
        ];
        $out[] = [
            $container,
            $config,
            6666,
            '156.39.58.10',
            'fox-test',
            [
                'index' => [
                    'number_of_shards' => 3,
                    'number_of_replicas' => 1,
                    'refresh_interval' => -1
                ]
            ]
        ];

        return $out;
    }



    /**
     * Tests if port host and index are loaded correctly i.e. tests connection settings
     *
     * @param ContainerBuilder $container
     * @param array $config
     * @param integer $expectedPort
     * @param string $expectedHost
     * @param string $expectedIndex
     * @param string $expectedSettings
     *
     * @dataProvider loadConnectionSettingsData
     */
    public function testLoadConnectionSettings(
        $container,
        $config,
        $expectedPort,
        $expectedHost,
        $expectedIndex,
        $expectedSettings
    ) {

        $extension = new FoxAdminExtension();

        $extension->load(['ongr_admin' => $config], $container);

        // index
        $this->assertTrue($container->hasParameter('ongr_admin.connection.index_name'));
        $this->assertEquals($expectedIndex, $container->getParameter('ongr_admin.connection.index_name'));

        // host
        $this->assertTrue($container->hasParameter('ongr_admin.connection.host'));
        $this->assertEquals($expectedHost, $container->getParameter('ongr_admin.connection.host'));

        // port
        $this->assertTrue($container->hasParameter('ongr_admin.connection.port'));
        $this->assertEquals($expectedPort, $container->getParameter('ongr_admin.connection.port'));

        // index settings
        $this->assertTrue($container->hasParameter('ongr_admin.settings_model_connection.settings'));
        $this->assertEquals(
            $expectedSettings,
            $container->getParameter('ongr_admin.settings_model_connection.settings')
        );
    }
}
