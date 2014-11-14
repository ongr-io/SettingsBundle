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

namespace ONGR\AdminBundle\Tests\Functional\EventListener;

use ONGR\AdminBundle\EventListener\ProfileRequestListener;

class ProfileRequestListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test cases for testOnKernelRequest
     *
     * @return array
     */
    public function getTestOnKernelRequestCases()
    {
        $cases = [];

        // Case #0. No profiles selected
        $cases[] = [[], []];
        // Case #1. One profile selected
        $cases[] = [['ongr_admin_domain_foo-2e-com' => true], ['foo.com']];
        // Case #3. One profile unselected
        $cases[] = [
            ['ongr_admin_domain_foo-2e-com' => false, 'ongr_admin_domain_bar-2e-com' => true],
            ['bar.com']
        ];

        return $cases;
    }

    /**
     * Test onKernelRequest method
     *
     * @dataProvider getTestOnKernelRequestCases()
     */
    public function testOnKernelRequest($userSettings, $expectedDomains)
    {
        // Mock settings container

        $sessionModel = $this->getMockBuilder('Fox\DDALBundle\Session\SessionModelInterface')
            ->getMockForAbstractClass();

        $settingsContainer = $this
            ->getMockBuilder('\ONGR\AdminBundle\Settings\SettingsContainer')
            ->disableOriginalConstructor()
            ->getMock();
        $i = 0;
        foreach ($expectedDomains as $expectedDomain) {
            $settingsContainer->expects($this->at($i))->method('addDomain')->with($expectedDomain);
            $settingsContainer->expects($this->at($i + 1))->method('addProvider');
            $i += 2;
        }

        // Mock users manager

        $userSettingsManager = $this
            ->getMockBuilder('\ONGR\UtilsBundle\Settings\UserSettingsManager')
            ->disableOriginalConstructor()
            ->getMock();
        $userSettingsManager->expects($this->once())->method('getSettings')->willReturn($userSettings);

        // Test

        $listener = new ProfileRequestListener();
        $listener->setSettingsContainer($settingsContainer);
        $listener->setUserSettingsManager($userSettingsManager);
        $listener->setSettingModel($sessionModel);
        $event = $this
            ->getMockBuilder('\Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $listener->onKernelRequest($event);
    }
}
