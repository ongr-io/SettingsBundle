<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\Functional\Tests\Functional\DependencyInjection\Compiler;

use ONGR\SettingsBundle\DependencyInjection\Compiler\SettingsModifierPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SettingsModifierPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Cases for testProcess.
     *
     * @return array
     */
    public function getTestProcessCases()
    {
        $cases = [];
        // Case 0: when method is set, add call for it.
        $cases[] = [['method' => 'methodFoo'], 'methodFoo'];

        // Case 1: when no method is set, use default 'getSettings'.
        $cases[] = [[], 'getSettings'];

        return $cases;
    }

    /**
     * PRocesses test.
     *
     * @param array  $tagAttributes
     * @param string $expectedMethod
     *
     * @dataProvider getTestProcessCases()
     */
    public function testProcess(array $tagAttributes, $expectedMethod)
    {
        $structureService = $this
            ->getMockBuilder('\Symfony\Component\DependencyInjection\Definition')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject $container */
        $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerBuilder');
        $container->expects($this->once())->method('getDefinition')->willReturn($structureService);
        $container
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with('ongr_settings.personal_profiles_provider')
            ->willReturn(
                [
                    'service_id' => [$tagAttributes],
                ]
            );
        $structureService
            ->expects($this->once())
            ->method('addMethodCall')
            ->with(
                'extractSettings',
                $this->callback(
                    function ($value) use ($expectedMethod) {
                        return $value[1] == $expectedMethod;
                    }
                )
            );
        $pass = new SettingsModifierPass($container);
        $pass->process($container);
    }
}
