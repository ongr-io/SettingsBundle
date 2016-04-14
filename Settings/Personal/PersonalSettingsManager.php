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

use Stash\Pool;
use ONGR\SettingsBundle\Settings\Personal\SettingsStructure;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

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
     * @var string
     */
    private $userParam;

    /**
     * Stash name for the current user
     *
     * @var string
     */
    private $stash;

    /**
     * @var AuthorizationChecker
     */
    private $securityContext;

    /**
     * @var TokenStorage
     */
    private $token;

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
     * @param AuthorizationChecker     $authorization
     * @param TokenStorage             $token
     * @param String                   $userParam
     * @param Pool                     $pool
     */
    public function __construct($settingsStructure, $authorization, $token, $userParam, $pool)
    {
        $this->settingsStructure = $settingsStructure;
        $this->securityContext = $authorization;
        $this->token = $token;
        $this->pool = $pool;
        $this->userParam = $userParam;
    }

    /**
     * Sets settings for the current user from stash
     */
    public function setSettingsFromStash()
    {
        $this->stash = $this->getStashName($this->token->getToken()->getUser());
        $stashedSettings = $this->pool->getItem($this->stash)->get();
        if (is_array($stashedSettings)) {
            $this->userSettings = $stashedSettings;
        }
    }

    /**
     * @param array $settings
     */
    public function setSettings(array $settings)
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
        $this->setSettingsFromStash();
        if (isset($this->userSettings[$settingName])) {
            return $this->userSettings[$settingName];
        }

        return false;
    }

    /**
     * Saves the current settings to stash
     *
     * @throws \BadMethodCallException
     */
    public function save()
    {
        $stashedSettings = $this->pool->getItem($this->stash);
        $stashedSettings->set($this->userSettings);
        $this->pool->save($stashedSettings);
    }

    /**
     * Clears the stash
     */
    public function stashClear()
    {
        $this->pool->deleteItem($this->stash);
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

    /**
     * Returns the full name of the stash for the current
     * User. If the unique user property value cant be determined
     * returns false.
     *
     * @param $user
     *
     * @return string
     * @throws \BadMethodCallException
     */
    private function getStashName($user)
    {
        $property = $this->guessPropertyOrMethodName($user);
        $stashName =  self::STASH_NAME.'_';
        try {
            if ($property[0] == 'public') {
                $stashName = $stashName . $user->$property[1];
            } else {
                $method = $property[1];
                $stashName = $stashName . $user->$method();
            }
            $this->stash = $stashName;
            return $stashName;
        } catch (\Exception $e) {
            throw new \BadMethodCallException(
                'Ongr could not guess the getter method for your defined user property.'
            );
        }
    }

    /**
     * Returns the property visibility and if its
     * private, guesses the name of the getter
     *
     * @param $user
     *
     * @return array
     */
    private function guessPropertyOrMethodName($user)
    {
        $property = $this->userParam;
        if (isset($user->$property)) {
            return ['public', $this->userParam];
        } else {
            return ['private', $this->toCamelCase($this->userParam)];
        }
    }

    /**
     * Converts a string to camel case
     *
     * @param string $name
     *
     * @return string
     */
    private function toCamelCase($name)
    {
        $return = explode('_', $name);
        foreach ($return as $key => $item) {
            $return[$key] = ucfirst($item);
        }
        return 'get'.implode('', $return);
    }
}
