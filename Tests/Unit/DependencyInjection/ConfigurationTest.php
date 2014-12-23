<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Functional\DependencyInjection;

use ONGR\AdminBundle\DependencyInjection\Configuration;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends ElasticsearchTestCase
{
    /**
     * Data provider for testConfiguration.
     *
     * @return array
     */
    public function configurationData()
    {
        $defaultConnection = [
            'index_name' => 'ongr-settings',
            'host' => '127.0.0.1',
            'port' => 9200,
        ];

        // A #0 test default values.

        $out[] = [
            [],
            [
                'connection' => $defaultConnection,
                'profiles' => ['default'],
                'index_settings' => [],
            ],
        ];

        // A #1 test custom values.
        $config = [
            'index_name' => 'ongr-test',
            'host' => '127.5.0.1',
            'port' => 9205,
        ];
        $out[] = [
            ['connection' => $config],
            [
                'connection' => $config,
                'profiles' => ['default'],
                'index_settings' => [],
            ],
        ];

        // A #2 test loading array of domains.
        $out[] = [
            ['profiles' => ['one', 'two']],
            [
                'connection' => $defaultConnection,
                'profiles' => ['one', 'two'],
                'index_settings' => [],
            ],
        ];

        return $out;
    }

    /**
     * Tests if expected configuration structure works well.
     *
     * @param array $config
     * @param array $expectedConfig
     *
     * @dataProvider configurationData
     */
    public function testConfiguration($config, $expectedConfig)
    {
        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration(), [$config]);

        $this->assertEquals($expectedConfig, $processedConfig);
    }
}
