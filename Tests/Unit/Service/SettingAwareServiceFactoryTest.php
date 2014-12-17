<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Unit\Service;

use ONGR\AdminBundle\Service\SettingAwareServiceFactory;
use ONGR\AdminBundle\Exception\SettingNotFoundException;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use Symfony\Component\Form\Exception\LogicException;


class SettingAwareServiceFactoryTest extends ElasticsearchTestCase
{

    /**
     * Tests guess name settings method.
     */
    public function testGuessNameMethod()
    {
        $mockSettingInterface = $this->getMock('ONGR\AdminBundle\Settings\Common\SettingsContainerInterface');
        $factory = new SettingAwareServiceFactory($mockSettingInterface);

        $method = new \ReflectionMethod(
            'ONGR\AdminBundle\Service\SettingAwareServiceFactory',
            'guessName'
        );
        $method->setAccessible(true);
        $this->assertEquals('setTestClass', $method->invoke($factory, 'testClass'));
        $this->assertEquals('setTestClass', $method->invoke($factory, 'test class'));
        $this->assertEquals('setTestClass', $method->invoke($factory, 'test_Class'));
    }

    /**
     * Tests get method on SettingNotFoundException.
     *
     * @expectedException LogicException
     */
    public function testGetMethod2()
    {
        $settingsContainer = $this->getMock('ONGR\AdminBundle\Settings\Common\SettingsContainerInterface');

        $settingsContainer->expects(
            $this->at(0)
        )->method('get')
            ->will($this->throwException(new SettingNotFoundException));

        $settingAwareServiceFactory = new SettingAwareServiceFactory($settingsContainer);

        $logger = $this->getMock('Psr\Log\LoggerInterface');

        $logger->expects(
            $this->once()
        )->method('notice')
            ->with("Setting 'key1' was not found.");

        $settingAwareServiceFactory->setLogger($logger);

        $callMap = [
            'key1' => 'SettingNotFoundException',
            'key2' => 'LogicException',
        ];
        $this->throwException(new LogicException(), $settingAwareServiceFactory->get($callMap, $settingAwareServiceFactory));
    }
}
