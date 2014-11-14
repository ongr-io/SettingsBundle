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

namespace ONGR\AdminBundle\Tests\Integration\DependencyInjection\Compiler;

use ONGR\AdminBundle\DependencyInjection\Compiler\ProviderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ProviderPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for process()
     */
    public function testProcess()
    {
        $container = new ContainerBuilder();
        $container->setDefinition('ongr_admin.settings_container', new Definition());
        $container->setParameter('ongr_admin.settings_container.domains', ['default', 'custom']);
        $container->setParameter(
            'ongr_admin.settings_provider.class',
            'Fox\\AdminBundle\\Settings\\Provider\\SessionModelAwareProvider'
        );

        $definition = new Definition();
        $definition->addTag('ongr_admin.settings_provider', ['domain' => 'custom']);
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
