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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use ONGR\SettingsBundle\Event\SettingChangeEvent;

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
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

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
     * @param String                   $userParam
     * @param SettingsStructure        $settingsStructure
     * @param EventDispatcherInterface $eventDispatcher
     * @param AuthorizationChecker     $authorization
     * @param TokenStorage             $token
     * @param Pool                     $pool
     */
    public function __construct($userParam, $settingsStructure, $eventDispatcher, $authorization, $token, $pool)
    {
        $this->settingsStructure = $settingsStructure;
        $this->eventDispatcher = $eventDispatcher;
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
        $this->stash = $this->getStashName();
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
     *
     * @return bool
     */
    public function getSettingEnabled($settingName)
    {
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

        $this->eventDispatcher->dispatch('ongr_settings.setting_change', new SettingChangeEvent('save'));
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
     * Gets active profiles
     *
     * @return array
     */
    public function getActiveProfiles()
    {
        $profiles = [];
        $this->setSettingsFromStash();
        foreach ($this->userSettings as $name => $userSetting) {
            if (preg_match('/^ongr_settings_profile_.*/', $name) && $userSetting) {
                $escapedProfile = mb_substr($name, 22, null, 'UTF-8');
                $profiles[] = UnderscoreEscaper::unescape($escapedProfile);
            }
        }
        return $profiles;
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
     * @return string
     */
    private function getStashName()
    {
        return self::STASH_NAME.'_'.$this->getUsername();
    }

    /**
     * Gets the defined unique user setting value
     *
     * @return string
     * @throws \BadMethodCallException
     */
    public function getUsername()
    {
        $user = $this->token->getToken()->getUser();
        $call = $this->guessPropertyOrMethodName($user);
        try {
            if ($call[0] == 'public') {
                return $user->$call[1];
            } else {
                $method = $call[1];
                if ($user == 'anon.') {
                    return $user;
                } else {
                    return $user->$method();
                }
            }
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
