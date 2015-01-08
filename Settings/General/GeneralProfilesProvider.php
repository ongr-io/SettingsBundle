<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Settings\General;

use ONGR\SettingsBundle\Service\ProfilesManager;
use ONGR\SettingsBundle\Service\UnderscoreEscaper;
use ONGR\SettingsBundle\Settings\General\SettingsStructure;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Responsible for collecting all settings' profiles from ES.
 */
class GeneralProfilesProvider
{
    /**
     * @var ProfilesManager
     */
    protected $profileManager;

    /**
     * @var SettingsStructure
     */
    protected $settingsStructure;

    /**
     * On kernel request.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $routeName = $event->getRequest()->get('_route');
        if ($routeName != 'ongr_settings_settings_settings') {
            return;
        }

        $this->settingsStructure->extractSettings($this, 'getSettings');
    }

    /**
     * Get profile list to display in admin-user select list.
     *
     * @return array
     */
    public function getSettings()
    {
        $profiles = $this->profileManager->getProfiles();

        $settings = [];
        foreach ($profiles as $profile) {
            $profileId = 'ongr_settings_profile_' . UnderscoreEscaper::escape($profile['profile']);
            $settings[$profileId] = [
                'name' => $profile['profile'],
                'category' => 'ongr_settings_profiles',
            ];
        }

        return $settings;
    }

    /**
     * @param ProfilesManager $profileSettingsProvider
     */
    public function setProfileManager($profileSettingsProvider)
    {
        $this->profileManager = $profileSettingsProvider;
    }

    /**
     * @param SettingsStructure $settingsStructure
     */
    public function setSettingsStructure($settingsStructure)
    {
        $this->settingsStructure = $settingsStructure;
    }
}
