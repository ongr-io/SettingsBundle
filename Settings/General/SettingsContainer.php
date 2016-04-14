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
use ONGR\SettingsBundle\Settings\Personal\PersonalSettingsManager;
use ONGR\SettingsBundle\Settings\General\Provider\SettingsProviderInterface;
use ONGR\SettingsBundle\Settings\General\Provider\ManagerAwareSettingProvider;
use Stash\Interfaces\ItemInterface;
use Stash\Interfaces\PoolInterface;
use ONGR\ElasticsearchBundle\Service\Manager;

/**
 * This class provides access to application settings.
 */
class SettingsContainer implements SettingsContainerInterface
{
    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * Personal settings manager for handling
     * selected profiles
     *
     * @var PersonalSettingsManager
     */
    private $manager;

    /**
     * Elasticsearch manager for building profiders
     * @var Manager
     */
    private $esManager;

    /**
     * @var array
     */
    private $profiles = [];

    /**
     * @var SettingsProviderInterface[]
     */
    private $providers = [];

    /**
     * @var array
     */
    private $settings = [];

    /**
     * Constructor.
     *
     * @param PoolInterface              $pool
     * @param PersonalSettingsManager    $manager
     * @param Manager    $esManager
     */
    public function __construct(
        PoolInterface $pool,
        PersonalSettingsManager $manager,
        Manager $esManager
    )
    {
        $this->pool = $pool;
        $this->manager = $manager;
        $this->esManager = $esManager;
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
        $this->profiles = $this->manager->getActiveProfiles();

        foreach ($this->profiles as $profile) {
            $this->addProvider($this->buildProvider($profile));
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
        $this->pool->save($cachedSetting);
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
        return $this->pool->getItem('ongr_settings.settings_cache');
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

    /**
     * BuildProvider.
     *
     * @param string $profile
     *
     * @return ManagerAwareSettingProvider
     */
    private function buildProvider($profile)
    {
        $provider = new ManagerAwareSettingProvider($profile);
        $provider->setManager($this->esManager);

        return $provider;
    }
}
