<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Settings\Personal;

use Symfony\Component\Security\Core\SecurityContextInterface;
use ONGR\SettingsBundle\Settings\Personal\SettingsStructure;

/**
 * Service responsible as a gateway to user settings.
 */
class PersonalSettingsManager
{
    /**
     * @var string
     */
    const ROLE_GRANTED = 'ROLE_ADMIN';

    /**
     * @var string
     */
    const STASH_NAME = 'ongr_settings';

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @var SettingsStructure
     */
    private $settingsStructure;

    /**
     * @var array;
     */
    private $userSettings = [];

    /**
     * Stash for storing personal settings
     *
     * @var \Pool
     */
    private $pool;

    /**
     * @param SettingsStructure        $settingsStructure
     * @param                          $security
     * @param \Pool                    $pool
     */
    public function __construct($settingsStructure, $security, $pool)
    {
        $this->settingsStructure = $settingsStructure;
        $this->securityContext = $security;
        $this->pool = $pool;
        $this->userSettings = $pool->getItem(self::STASH_NAME)->get();
    }

    /**
     * @param array $stash
     */
    public function setSettingsFromStash(array $stash)
    {
        $this->userSettings = $stash;
    }

    /**
     * @param array $stash
     */
    public function addSettingsFromStash(array $stash)
    {
        $this->userSettings = array_merge($this->userSettings, $stash);
    }

    /**
     * @param array $settings
     */
    public function setSettingsFromForm(array $settings)
    {
        $this->userSettings = $settings;
    }

    /**
     * If user logged in, returns setting value from cookie. Else, returns false.
     *
     * @param string $settingName
     * @param bool   $mustAuthorize
     *
     * @return bool
     */
    public function getSettingEnabled($settingName, $mustAuthorize = true)
    {
        if ($mustAuthorize && !$this->isAuthenticated()) {
            return false;
        }

        if (isset($this->userSettings[$settingName])) {
            return $this->userSettings[$settingName];
        }

        return false;
    }

    /**
     * Saves the current settings to stash
     */
    public function save()
    {
        $stashedSettings = $this->pool->getItem(self::STASH_NAME);
        $stashedSettings->set($this->userSettings);
        $this->pool->save($stashedSettings);
    }

    /**
     * @return bool
     */
    public function isAuthenticated()
    {
        return $this->securityContext->isGranted(self::ROLE_GRANTED);
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->userSettings;
    }

    /**
     * @return array
     */
    public function getSettingsMap()
    {
        return $this->settingsStructure->getStructure();
    }

    /**
     * @return array
     */
    public function getCategoryMap()
    {
        return $this->settingsStructure->getCategoriesStructure();
    }
}
