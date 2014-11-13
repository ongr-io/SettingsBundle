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

namespace Fox\AdminBundle\Tests\Functional\DependencyInjection;

use Fox\AdminBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Data provider for testConfiguration
     *
     * @return array
     */
    public function configurationData()
    {
        $defaultConnection = [
                'index_name' => 'fox-settings',
                'host' => '127.0.0.1',
                'port' => 9200
            ];

        // #0 test default values

        $out[] = [
            [],
            [
                'connection' => $defaultConnection,
                'domains' => ['default'],
                'index_settings' => []
            ]
        ];

        // #1 test custom values
        $config = [
                    'index_name' => 'fox-test',
                    'host' => '127.5.0.1',
                    'port' => 9205
            ];
        $out[] = [
            ['connection' => $config],
            [
                'connection' => $config,
                'domains' => ['default'],
                'index_settings' => []
            ]
        ];

        // #2 test loading array of domains
        $out[] = [
            ['domains' => ['one', 'two']],
            [
                'connection' => $defaultConnection,
                'domains' => ['one', 'two'],
                'index_settings' => []
            ]
        ];

        return $out;
    }

    /**
     * Tests if expected configuration structure works well
     *
     * @param $config array
     * @param $expectedConfig array
     * @dataProvider configurationData
     */
    public function testConfiguration($config, $expectedConfig)
    {
        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration(), [$config]);

        $this->assertEquals($expectedConfig, $processedConfig);
    }
}
