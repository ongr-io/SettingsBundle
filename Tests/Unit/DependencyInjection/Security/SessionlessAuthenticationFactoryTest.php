<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Functional\DependencyInjection;

use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use ONGR\SettingsBundle\DependencyInjection\Security\SessionlessAuthenticationFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

/**
 * This class checks if we are able to create container.
 */
class SessionlessAuthenticationFactoryTest extends ElasticsearchTestCase
{
    /**
     * Checks.
     */
    public function testFirewallsServices()
    {
        $service = $this->getContainer()
            ->get('ongr_settings.firewall.listener.sessionless_authentication.sessionless_authentication_secured');
        $this->assertInstanceOf(
            'ONGR\SettingsBundle\Security\Authentication\Firewall\SessionlessAuthenticationListener',
            $service
        );
    }

    /**
     * Test FirewallFactory.
     */
    public function testFirewallFactory()
    {
        $container = new ContainerBuilder();
        $id = 'test_id';
        $config = [];
        $userProvider = 'up';
        $defaultEntryPoint = 'defaultEntryPoint';

        $firewallFactory = new SessionlessAuthenticationFactory();
        $firewallDetails = $firewallFactory->create($container, $id, $config, $userProvider, $defaultEntryPoint);

        // Test decorators.
        $this->assertEquals($this->getFirewallsDetails(), $firewallDetails);

        // Test getPosition.
        $this->assertEquals('pre_auth', $firewallFactory->getPosition());

        // Test get key.
        $this->assertEquals('ongr_sessionless_authentication', $firewallFactory->getKey());

        // Test addConfiguration.
        $stub = $this->getMockForAbstractClass(
            'Symfony\Component\Config\Definition\Builder\NodeDefinition',
            ['testName']
        );
        $this->assertEquals(null, $firewallFactory->addConfiguration($stub));
    }

    /**
     * @return array
     */
    public function getFirewallsDetails()
    {
        return [
            'ongr_settings.firewall.provider.sessionless_authentication.test_id',
            'ongr_settings.firewall.listener.sessionless_authentication.test_id',
            'defaultEntryPoint',
        ];
    }
}
