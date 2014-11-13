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

namespace Fox\AdminBundle\Tests\Functional\Service;

use Fox\AdminBundle\Exception\SettingNotFoundException;
use Fox\AdminBundle\Service\SettingAwareServiceFactory;
use Fox\AdminBundle\Settings\SettingsContainerInterface;

/**
 * Test class for SettingAwareServiceFactory
 */
class SettingAwareServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests get with existing setters
     */
    public function testGet()
    {
        $expectedValues = [
            'test1' => 'testValue1',
            'test2' => 'testValue2',
            'test3' => null,
            'test_variable_4' => 'testValue4',
        ];
        /** @var \PHPUnit_Framework_MockObject_MockObject|SettingsContainerInterface $settingsContainer */
        $settingsContainer = $this->getMockBuilder('Fox\AdminBundle\Settings\SettingsContainerInterface')
            ->setMethods(['addProvider', 'get'])
            ->getMock();

        $settingsContainer->expects($this->any())->method('get')->willReturnCallback(
            function ($setting) use ($expectedValues) {
                if ($expectedValues[$setting] === null) {
                    throw new SettingNotFoundException('test');
                }
                return $expectedValues[$setting];
            }
        );

        $object = $this->getMockBuilder('\StdClass')
            ->setMethods(['setTest1', 'setTest2', 'setTest3', 'setTestVariable4', 'setTestVariable5'])
            ->getMock();

        $object->expects($this->once())->method('setTest1')->with('testValue1');
        $object->expects($this->once())->method('setTest2')->with('testValue2');
        $object->expects($this->never())->method('setTest3');
        $object->expects($this->once())->method('setTestVariable4')->with('testValue4');

        $jobCalls = [
            'test1' => 'setTest1',
            'test2' => 'setTest2',
            'test3' => 'setTest3',
            'test_variable_4' => null,
        ];

        $factory = new SettingAwareServiceFactory($settingsContainer);
        $factory->get($jobCalls, $object);
    }

    /**
     * Tests get when it's supposed to throw logical exception
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage Undefined method setTest
     */
    public function testGetException()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|SettingsContainerInterface $settingsContainer */
        $settingsContainer = $this->getMockBuilder('Fox\AdminBundle\Settings\SettingsContainerInterface')
            ->setMethods(['addProvider', 'get'])
            ->getMock();

        $settingsContainer->expects($this->any())->method('get')->willReturn('testValue');
        $jobCalls = ['test' => 'setTest'];
        $object = $this->getMock('\StdClass');

        $factory = new SettingAwareServiceFactory($settingsContainer);
        $factory->get($jobCalls, $object);
    }
}
