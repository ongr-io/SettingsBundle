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

use Fox\AdminBundle\DependencyInjection\Compiler\SettingAwareFactoryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class SettingAwareFactoryPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for process()
     */
    public function testProcess()
    {
        $container = new ContainerBuilder();

        $definition = new Definition();
        $definition->addTag('fox_admin.setting_aware', ['setting' => 'setting_1']);
        $definition->addTag('fox_admin.setting_aware', ['setting' => 'setting_2', 'method' => 'setAnother']);
        $definition->addTag('custom_tag');
        $container->setDefinition('fox_admin.fake_service', $definition);

        $pass = new SettingAwareFactoryPass();
        $pass->process($container);

        $this->assertTrue($container->hasDefinition('fox_admin.fake_service'), 'target service');
        $this->assertTrue($container->hasDefinition('fox_admin.fake_service_base'), 'base service');
        $this->assertTrue($container->getDefinition('fox_admin.fake_service')->hasTag('custom_tag'), 'custom tag');

        $callMap = $container->getDefinition('fox_admin.fake_service')->getArgument(0);

        $expectedCallMap = [
            'setting_1' => null,
            'setting_2' => 'setAnother',
        ];

        $this->assertEquals($expectedCallMap, $callMap);
    }
}
