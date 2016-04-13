<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\UtilsBundle\Tests\Unit\Twig;

use ONGR\SettingsBundle\Twig\HiddenExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

class HiddenExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param Request $request
     *
     * @return Container
     */
    public function getContainer(Request $request = null)
    {
        $container = new Container();
        if ($request !== null) {
            $container->set('request', $request);
        }

        return $container;
    }

    /**
     * Tests name.
     */
    public function testName()
    {
        $extension = new HiddenExtension(new Container());
        $this->assertEquals('ongr_hidden', $extension->getName());
    }

    /**
     * Test if extension has functions.
     */
    public function testHasFunctions()
    {
        $extension = new HiddenExtension($this->getContainer());
        $this->assertNotEmpty($extension->getFunctions());
    }

    /**
     * Data provider for testGenerate.
     *
     * @return array
     */
    public function getTestGenerateData()
    {
        $data = [];
        $template = 'ONGRSettingsBundle:Utils:hidden.html.twig';

        // Case 0: dont check request.
        $data0 = [
            'test1' => 1,
            'nested2' => [5, 4],
        ];
        $env0 = $this->getMock('stdClass', ['render']);
        $env0
            ->expects($this->once())
            ->method('render')
            ->with(
                $template,
                [
                    'data' => [
                        [
                            'value' => 1,
                            'name' => 'test1',
                        ],
                        [
                            'value' => 5,
                            'name' => 'nested2[]',
                        ],
                        [
                            'value' => 4,
                            'name' => 'nested2[]',
                        ],
                    ],
                ]
            );
        $container0 = $this->getContainer();

        $data[] = [$data0, false, $env0, $container0];

        // Case 1: check request.
        $data1 = [
            'test1' => 1,
            'test12' => 12,
            'nested2' => [5, 4],
        ];
        $env1 = $this->getMock('stdClass', ['render']);
        $env1
            ->expects($this->once())
            ->method('render')
            ->with(
                $template,
                [
                    'data' => [],
                ]
            );
        $container1 = $this->getContainer(
            new Request(
                [
                    'test12' => 'smth',
                    'nested2' => 'smth',
                ]
            )
        );

        $data[] = [$data1, true, $env1, $container1];

        return $data;
    }

    /**
     * Test for generate method.
     *
     * @param array              $data
     * @param bool               $checkRequest
     * @param \Twig_Environment  $env
     * @param ContainerInterface $container
     *
     * @dataProvider getTestGenerateData
     */
    public function testGenerate($data, $checkRequest, $env, $container)
    {
        $extension = new HiddenExtension($container);
        $extension->generate($env, $data, $checkRequest);
    }
}
