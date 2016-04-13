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

use ONGR\SettingsBundle\Event\SettingChangeEvent;
use ONGR\SettingsBundle\Exception\SettingNotFoundException;
use Stash\Interfaces\ItemInterface;
use Stash\Interfaces\PoolInterface;
use ONGR\SettingsBundle\Settings\General\Provider\SettingsProviderInterface;

/**
 * This class provides access to application settings.
 */
class SettingsContainer implements SettingsContainerInterface
{
    /**
     * @var PoolInterface
     */
    protected $pool;

    /**
     * Array of profiles.
     *
     * Array of selected profiles / profiles to apply.
     *
     * @var array
     */
    protected $profiles;

    /**
     * @var SettingsProviderInterface[]
     */
    protected $providers = [];

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * Constructor.
     *
     * @param PoolInterface $pool
     * @param array         $profiles
     */
    public function __construct(PoolInterface $pool, $profiles = ['default'])
    {
        $this->pool = $pool;
        $this->profiles = $profiles;
    }

    /**
     * {@inheritdoc}
     */
    public function addProvider(SettingsProviderInterface $provider)
    {
        $this->providers[] = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function get($setting)
    {
        $result = $this->getSetting($setting, false);

        if ($result !== null) {
            return $result;
        }

        $cachedSetting = $this->getCache();

        if (!$cachedSetting->isMiss()) {
            $this->settings = json_decode($cachedSetting->get(), true);

            return $this->getSetting($setting);
        }

        $settings = [];

        foreach ($this->providers as $provider) {
            if (in_array($provider->getProfile(), $this->profiles)) {
                $settings = array_merge($settings, $provider->getSettings());
            }
        }

        $cachedSetting->set(json_encode($settings));
        $this->settings = array_merge($this->settings, $settings);

        return $this->getSetting($setting);
    }

    /**
     * Handles setting change event.
     *
     * @param SettingChangeEvent $event
     */
    public function onSettingChange(
        SettingChangeEvent $event
    ) {
        $this->pool->getItem('ongr_settings.settings_cache')->clear();
    }

    /**
     * @return array
     */
    public function getProfiles()
    {
        return $this->profiles;
    }

    /**
     * @param array $profiles
     */
    public function setProfiles($profiles)
    {
        $this->profiles = $profiles;
    }

    /**
     * @param string $profile
     */
    public function addProfile($profile)
    {
        $this->profiles[] = $profile;
    }

    /**
     * Returns settings cache item.
     *
     * @return ItemInterface
     */
    protected function getCache()
    {
        return $this->pool->getItem('ongr_settings.settings_cache', join($this->profiles, ','));
    }

    /**
     * Returns setting value.
     *
     * @param string $setting
     * @param bool   $throwException
     *
     * @return mixed
     *
     * @throws SettingNotFoundException
     */
    protected function getSetting($setting, $throwException = true)
    {
        if (array_key_exists($setting, $this->settings)) {
            return $this->settings[$setting];
        } elseif ($throwException) {
            throw new SettingNotFoundException("Setting '{$setting}' does not exist.");
        }

        return null;
    }
}
