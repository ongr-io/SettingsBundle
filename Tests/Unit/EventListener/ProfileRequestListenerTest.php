<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Unit\EventListener;

use ONGR\AdminBundle\EventListener\ProfileRequestListener;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;

class ProfileRequestListenerTest extends ElasticsearchTestCase
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
        $cases[] = [ ['ongr_admin_profile_foo-2e-com' => true], ['foo.com'] ];
        // Case #2. One profile unselected.
        $cases[] = [
            ['ongr_admin_profile_foo-2e-com' => false, 'ongr_admin_profile_bar-2e-com' => true],
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
        $manager = $this->getManager();

        // Mock settings container.
        $settingsContainer = $this
            ->getMockBuilder('\ONGR\AdminBundle\Settings\Common\SettingsContainer')
            ->disableOriginalConstructor()
            ->getMock();
        $i = 0;
        foreach ($expectedProfiles as $expectedProfile) {
            $settingsContainer->expects($this->at($i))->method('addProfile')->with($expectedProfile);
            $settingsContainer->expects($this->at($i + 1))->method('addProvider');
            $i += 2;
        }

        // Mock users manager.
        $adminSettingsManager = $this
            ->getMockBuilder('\ONGR\AdminBundle\Settings\Admin\AdminSettingsManager')
            ->disableOriginalConstructor()
            ->getMock();
        $adminSettingsManager->expects($this->once())->method('getSettings')->willReturn($userSettings);

        // Test.
        $listener = new ProfileRequestListener();
        $listener->setSettingsContainer($settingsContainer);
        $listener->setAdminSettingsManager($adminSettingsManager);
        $listener->setManager($manager);
        $event = $this
            ->getMockBuilder('\Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $listener->onKernelRequest($event);
    }
}
