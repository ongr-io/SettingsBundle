<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Settings\Admin;

use Symfony\Component\Security\Core\SecurityContextInterface;
use ONGR\AdminBundle\Settings\Admin\SettingsStructure;

/**
 * Service responsible as a gateway to user settings.
 */
class AdminSettingsManager
{
    /**
     * @var string
     */
    const ROLE_GRANTED = 'ROLE_ADMIN';

    /**
     * @var string
     */
    const ROLE_DEFAULT = 'ROLE_SETTINGS_USER';

    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @var SettingsStructure
     */
    protected $settingsStructure;

    /**
     * @var array;
     */
    protected $userSettings = [];

    /**
     * @param SecurityContextInterface $securityContext
     * @param SettingsStructure        $settingsStructure
     */
    public function __construct($securityContext, $settingsStructure)
    {
        $this->securityContext = $securityContext;
        $this->settingsStructure = $settingsStructure;
    }

    /**
     * @param array $cookie
     */
    public function setSettingsFromCookie(array $cookie)
    {
        $this->userSettings = $cookie;
    }

    /**
     * @param array $cookie
     */
    public function addSettingsFromCookie(array $cookie)
    {
        $this->userSettings = array_merge($this->userSettings, $cookie);
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
