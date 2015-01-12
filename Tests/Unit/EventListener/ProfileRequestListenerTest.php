<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Unit\EventListener;

use ONGR\SettingsBundle\EventListener\ProfileRequestListener;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;

class ProfileRequestListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test cases for testOnKernelRequest.
     *
     * @return array
     */
    public function getTestOnKernelRequestCases()
    {
        $cases = [];

        // Case #0. No profiles selected.
        $cases[] = [[], []];
        // Case #1. One profile selected.
        $cases[] = [ ['ongr_settings_profile_foo-2e-com' => true], ['foo.com'] ];
        // Case #2. One profile unselected.
        $cases[] = [
            ['ongr_settings_profile_foo-2e-com' => false, 'ongr_settings_profile_bar-2e-com' => true],
            ['bar.com'],
        ];

        return $cases;
    }

    /**
     * Test onKernelRequest method.
     *
     * @param array $userSettings
     * @param array $expectedProfiles
     *
     * @dataProvider getTestOnKernelRequestCases()
     */
    public function testOnKernelRequest($userSettings, $expectedProfiles)
    {
        // Get settings container.
        $manager = $this->getManagerMock();

        // Mock settings container.
        $settingsContainer = $this
            ->getMockBuilder('\ONGR\SettingsBundle\Settings\General\SettingsContainer')
            ->disableOriginalConstructor()
            ->getMock();
        $i = 0;
        foreach ($expectedProfiles as $expectedProfile) {
            $settingsContainer->expects($this->at($i))->method('addProfile')->with($expectedProfile);
            $settingsContainer->expects($this->at($i + 1))->method('addProvider');
            $i += 2;
        }

        // Mock users manager.
        $personalSettingsManager = $this
            ->getMockBuilder('\ONGR\SettingsBundle\Settings\Personal\PersonalSettingsManager')
            ->disableOriginalConstructor()
            ->getMock();
        $personalSettingsManager->expects($this->once())->method('getSettings')->willReturn($userSettings);

        // Test.
        $listener = new ProfileRequestListener();
        $listener->setSettingsContainer($settingsContainer);
        $listener->setPersonalSettingsManager($personalSettingsManager);
        $listener->setManager($manager);
        $event = $this
            ->getMockBuilder('\Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $listener->onKernelRequest($event);
    }

    /**
     * Returns mock of ORM manager.
     *
     * @return Manager
     */
    protected function getManagerMock()
    {
        $managerMock = $this->getMockBuilder('ONGR\ElasticsearchBundle\ORM\Manager')
            ->disableOriginalConstructor()
            ->getMock();

        return $managerMock;
    }
}
