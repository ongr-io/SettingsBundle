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

use ONGR\SettingsBundle\DependencyInjection\Compiler\SettingAwareFactoryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class SettingAwareFactoryPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for process().
     */
    public function testProcess()
    {
        $container = new ContainerBuilder();

        $definition = new Definition();
        $definition->addTag('ongr_settings.setting_aware', ['setting' => 'setting_1']);
        $definition->addTag('ongr_settings.setting_aware', ['setting' => 'setting_2', 'method' => 'setAnother']);
        $definition->addTag('custom_tag');
        $container->setDefinition('ongr_settings.fake_service', $definition);

        $pass = new SettingAwareFactoryPass();
        $pass->process($container);

        $this->assertTrue($container->hasDefinition('ongr_settings.fake_service'), 'target service');
        $this->assertTrue($container->hasDefinition('ongr_settings.fake_service_base'), 'base service');
        $this->assertTrue($container->getDefinition('ongr_settings.fake_service')->hasTag('custom_tag'), 'custom tag');

        $callMap = $container->getDefinition('ongr_settings.fake_service')->getArgument(0);

        $expectedCallMap = [
            'setting_1' => null,
            'setting_2' => 'setAnother',
        ];

        $this->assertEquals($expectedCallMap, $callMap);
    }
}
