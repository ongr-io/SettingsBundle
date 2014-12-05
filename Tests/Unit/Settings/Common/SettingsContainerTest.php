<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Unit\Settings;

use ONGR\AdminBundle\Settings\Common\SettingsContainer;
use ONGR\AdminBundle\Settings\Common\Provider\SettingsProviderInterface;
use Stash\Interfaces\PoolInterface;

class SettingsContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param bool  $miss
     * @param array $settings
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|PoolInterface
     */
    private function getPool($miss = true, $settings = [])
    {
        $itemMock = $this->getMock('Stash\Interfaces\ItemInterface');
        $itemMock
            ->expects($this->once())
            ->method('isMiss')
            ->will($this->returnValue($miss));

        if (!$miss) {
            $itemMock
                ->expects($this->once())
                ->method('get')
                ->will($this->returnValue(json_encode($settings)));
        }

        $poolMock = $this->getMock('Stash\Interfaces\PoolInterface');
        $poolMock
            ->expects($this->once())
            ->method('getItem')
            ->will($this->returnValue($itemMock));

        return $poolMock;
    }

    /**
     * @param string $domain
     * @param array  $settings
     *
     * @return SettingsProviderInterface
     */
    private function getProvider($domain, $settings)
    {
        $providerMock = $this->getMock('ONGR\AdminBundle\Settings\Common\Provider\SettingsProviderInterface');
        $providerMock
            ->expects($this->once())
            ->method('getProfile')
            ->will($this->returnValue($domain));
        $providerMock
            ->expects($this->once())
            ->method('getSettings')
            ->will($this->returnValue($settings));

        return $providerMock;
    }

    /**
     * Data provider for testGetProviders.
     *
     * @return array
     */
    public function getTestProvidersData()
    {
        $out = [];

        // Case #0 simple value and setting, both exist.
        $out[] = [
            'settings' => ['setting1' => 'value1', 'setting2' => 'value2'],
            'toGet' => 'setting2',
            'expected' => 'value2',
        ];

        // Case #1 test value 0.
        $out[] = [
            'settings' => ['setting1' => 0],
            'toGet' => 'setting1',
            'expected' => 0,
        ];

        // Case #2 test value false.
        $out[] = [
            'settings' => ['setting1' => false],
            'toGet' => 'setting1',
            'expected' => false,
        ];

        return $out;
    }

    /**
     * Test if get method works.
     *
     * @param array  $settings
     * @param string $toGet
     * @param string $expected
     *
     * @dataProvider getTestProvidersData
     */
    public function testGet($settings, $toGet, $expected)
    {
        // Loads from provider.
        $poolMock = $this->getPool();
        $providerMock = $this->getProvider('default', $settings);

        $container = new SettingsContainer($poolMock);
        $container->addProvider($providerMock);

        $result = $container->get($toGet);
        $this->assertEquals($expected, $result, 'failed to get parameter from provider');

        // Loads from stored local property.
        $result2 = $container->get($toGet);
        $this->assertEquals($expected, $result2, 'failed to get parameter from local property');

        // Loads from cached stash.
        $poolMock3 = $this->getPool(false, $settings);
        $container3 = new SettingsContainer($poolMock3);
        $result3 = $container3->get($toGet);
        $this->assertEquals($expected, $result3, 'failed to get parameter from stash');
    }

    /**
     * Test if exception is being thrown.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testException()
    {
        $poolMock = $this->getPool(false);
        $container = new SettingsContainer($poolMock);
        $container->get('whatever');
    }

    /**
     * Test setDomains method.
     */
    public function testSetDomains()
    {
        $container = new SettingsContainer($this->getMock('\Stash\Interfaces\PoolInterface'));
        $container->setProfiles(['foo']);
        $this->assertEquals(['foo'], $container->getProfiles());
    }
}
