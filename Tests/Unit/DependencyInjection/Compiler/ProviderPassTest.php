<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Integration\DependencyInjection\Compiler;

use ONGR\AdminBundle\DependencyInjection\Compiler\ProviderPass;
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
        $container->setDefinition('ongr_admin.settings_container', new Definition());
        $container->setParameter('ongr_admin.settings_container.profiles', ['default', 'custom']);
        $container->setParameter(
            'ongr_admin.settings_provider.class',
            'ONGR\\AdminBundle\\Settings\\Common\\Provider\\ManagerAwareSettingProvider'
        );

        $definition = new Definition();
        $definition->addTag('ongr_admin.settings_provider', ['profile' => 'custom', 'priority' => 9]);
        $container->setDefinition('ongr_admin.custom_settings_provider', $definition);

        $definition = new Definition();
        $definition->addTag('ongr_admin.settings_provider');
        $container->setDefinition('ongr_admin.unregistered_settings_provider', $definition);

        $pass = new ProviderPass();
        $pass->process($container);

        $methodCalls = $container->getDefinition('ongr_admin.settings_container')->getMethodCalls();
        $this->assertCount(3, $methodCalls);
    }
}
