<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Unit\Settings\Admin;

use ONGR\AdminBundle\Settings\Admin\AdminProfilesProvider;

/**
 * Test how AdminProfilesProvider collects Admin settings from ES.
 */
class AdminProfilesProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test method getSettings.
     */
    public function testGetSettings()
    {
        $manager = new AdminProfilesProvider();
        $manager->setProfileManager($this->getProfilesManagerMock());

        $expectedArray = [
            'ongr_admin_profile_profile' => [
                'name' => 'profile',
                'category' => 'ongr_admin_profiles',
            ],
            'ongr_admin_profile_profile2' => [
                'name' => 'profile2',
                'category' => 'ongr_admin_profiles',
            ],
            'ongr_admin_profile_profile3' => [
                'name' => 'profile3',
                'category' => 'ongr_admin_profiles',
            ],
            'ongr_admin_profile_profile4' => [
                'name' => 'profile4',
                'category' => 'ongr_admin_profiles',
            ],
        ];
        $this->assertEquals($manager->getSettings(), $expectedArray);
    }

    /**
     * Returns mock of Profiles Manager.
     *
     * @return ProfilesManager
     */
    protected function getProfilesManagerMock()
    {
        $profileSettingsProvider = $this->getMock(
            'ONGR\AdminBundle\Service\ProfilesManager',
            ['getProfiles'],
            [],
            '',
            false
        );

        $profileSettingsProvider->expects(
            $this->once()
        )->method('getProfiles')
            ->willReturn(
                [
                    ['profile' => 'profile'],
                    ['profile' => 'profile2'],
                    ['profile' => 'profile3'],
                    ['profile' => 'profile4'],
                ]
            );

        return $profileSettingsProvider;
    }
}
