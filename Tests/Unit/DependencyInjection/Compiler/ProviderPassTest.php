<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Unit\DependencyInjection\Compiler;

use ONGR\SettingsBundle\DependencyInjection\Compiler\ProviderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ProviderPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for process.
     */
    public function testProcess()
    {
        $container = new ContainerBuilder();
        $container->setDefinition('ongr_settings.settings_container', new Definition());
        $container->setParameter('ongr_settings.settings_container.profiles', ['default', 'custom']);
        $container->setParameter(
            'ongr_settings.settings_provider.class',
            'ONGR\\SettingsBundle\\Settings\\Personal\\Provider\\ManagerAwareSettingProvider'
        );
        $container->setParameter('ongr_settings.connection.manager', 'es.manager');

        $definition = new Definition();
        $definition->addTag('ongr_settings.settings_provider', ['profile' => 'custom', 'priority' => 9]);
        $container->setDefinition('ongr_settings.custom_settings_provider', $definition);

        $definition = new Definition();
        $definition->addTag('ongr_settings.settings_provider');
        $container->setDefinition('ongr_settings.unregistered_settings_provider', $definition);

        $pass = new ProviderPass();
        $pass->process($container);

        $methodCalls = $container->getDefinition('ongr_settings.settings_container')->getMethodCalls();
        $this->assertCount(3, $methodCalls);
    }
}
