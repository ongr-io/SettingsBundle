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

namespace Fox\AdminBundle\Tests\Functional\DependencyInjection\Compiler;

use Fox\AdminBundle\DependencyInjection\Compiler\ProviderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ProviderPassTest extends \PHPUnit_Framework_TestCase
{
    public function getTestProcessData()
    {
        $domains = ['default', 'domain_2'];
        $out = [];

        // Case #0 default created providers
        $out[] = [
            $domains,
            [],
            [
                ['addProvider' => 'fox_admin.dynamic_provider.default'],
                ['addProvider' => 'fox_admin.dynamic_provider.domain_2'],
            ],
        ];

        // Case #1 multiple providers with tagged domains with no priority
        $out[] = [
            $domains,
            [
                'test_provider_1' => ['domain' => 'domain_2'],
            ],
            [
                ['addProvider' => 'fox_admin.dynamic_provider.default'],
                ['addProvider' => 'test_provider_1'],
            ],
        ];

        // Case #2 multiple providers with tagged domains with priority
        $out[] = [
            $domains,
            [
                'test_provider_1' => ['domain' => 'domain_2', 'priority' => 10],
            ],
            [
                ['addProvider' => 'test_provider_1'],
                ['addProvider' => 'fox_admin.dynamic_provider.default'],
            ],
        ];

        // Case #3 custom provider, no priority
        $out[] = [
            $domains,
            [
                'test_provider_1' => [],
            ],
            [
                ['addProvider' => 'fox_admin.dynamic_provider.default'],
                ['addProvider' => 'fox_admin.dynamic_provider.domain_2'],
                ['addProvider' => 'test_provider_1'],
            ],
        ];

        // Case #4 custom provider, with priority
        $out[] = [
            $domains,
            [
                'test_provider_1' => [],
                'test_provider_2' => ['priority' => 10],
            ],
            [
                ['addProvider' => 'test_provider_2'],
                ['addProvider' => 'fox_admin.dynamic_provider.default'],
                ['addProvider' => 'fox_admin.dynamic_provider.domain_2'],
                ['addProvider' => 'test_provider_1'],
            ],
        ];

        return $out;
    }

    /**
     * Test for process()
     *
     * @param array $domains
     * @param array $definitions
     * @param array $expectedResult
     *
     * @dataProvider getTestProcessData
     */
    public function testProcess($domains, $definitions, $expectedResult)
    {
        $container = new ContainerBuilder();
        $container->setParameter('fox_admin.settings_container.domains', $domains);
        $container->setParameter('fox_admin.settings_provider.class', 'test_class');

        $containerDefinition = new Definition();
        $container->setDefinition('fox_admin.settings_container', $containerDefinition);

        foreach ($definitions as $name => $options) {
            $definition = new Definition();
            $definition->addTag('fox_admin.settings_provider', $options);
            $container->setDefinition($name, $definition);
        }

        $providerPass = new ProviderPass();
        $providerPass->process($container);

        $result = $this->extractCalls($containerDefinition->getMethodCalls());

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Filters array like method => definition id
     *
     * @param array $calls
     *
     * @return array
     */
    protected function extractCalls($calls)
    {
        $result = [];
        foreach ($calls as $call) {
            $result[] = [$call[0] => (string) $call[1][0]];
        }

        return $result;
    }
}
