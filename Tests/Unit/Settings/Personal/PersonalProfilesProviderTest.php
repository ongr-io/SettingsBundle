<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Unit\Settings\Personal;

use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use ONGR\SettingsBundle\Settings\Personal\PersonalProfilesProvider;

/**
 * Test how PersonalProfilesProvider collects General settings from ES.
 */
class PersonalProfilesProviderTest extends ElasticsearchTestCase
{
    /**
     * Test method getSettings.
     */
    public function testGetSettings()
    {
        $manager = new PersonalProfilesProvider();
        $manager->setProfileManager($this->getProfilesManagerMock());

        $expectedArray = [
            'ongr_settings_profile_profile' => [
                'name' => 'profile',
                'category' => 'ongr_settings_profiles',
            ],
            'ongr_settings_profile_profile2' => [
                'name' => 'profile2',
                'category' => 'ongr_settings_profiles',
            ],
            'ongr_settings_profile_profile3' => [
                'name' => 'profile3',
                'category' => 'ongr_settings_profiles',
            ],
            'ongr_settings_profile_profile4' => [
                'name' => 'profile4',
                'category' => 'ongr_settings_profiles',
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
            'ONGR\SettingsBundle\Settings\Personal\ProfilesManager',
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
