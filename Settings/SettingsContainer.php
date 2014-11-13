<?php

/*
 *************************************************************************
 * NFQ eXtremes CONFIDENTIAL
 * [2013] - [2014] NFQ eXtremes UAB
 * All Rights Reserved.
 *************************************************************************
 * NOTICE: 
 * All information contained herein is, and remains the property of NFQ eXtremes UAB.
 * Dissemination of this information or reproduction of this material is strictly forbidden
 * unless prior written permission is obtained from NFQ eXtremes UAB.
 *************************************************************************
 */

namespace Fox\AdminBundle\Settings;

use Fox\AdminBundle\Event\SettingChangeEvent;
use Fox\AdminBundle\Exception\SettingNotFoundException;
use Stash\Interfaces\ItemInterface;
use Stash\Interfaces\PoolInterface;

/**
 * This class provides access to application settings
 */
class SettingsContainer implements SettingsContainerInterface
{
    /**
     * @var PoolInterface
     */
    protected $pool;

    /**
     * Array of selected domains / profiles to apply.
     *
     * @var array
     */
    protected $domains;

    /**
     * @var SettingsProviderInterface[]
     */
    protected $providers = [];

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * Constructor
     *
     * @param PoolInterface $pool
     * @param array $domains
     */
    public function __construct(PoolInterface $pool, $domains = ['default'])
    {
        $this->pool = $pool;
        $this->domains = $domains;
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
            if (in_array($provider->getDomain(), $this->domains)) {
                $settings = array_merge($settings, $provider->getSettings());
            }
        }

        $cachedSetting->set(json_encode($settings));
        $this->settings = array_merge($this->settings, $settings);

        return $this->getSetting($setting);
    }

    /**
     * Returns settings cache item
     *
     * @return ItemInterface
     */
    protected function getCache()
    {
        return $this->pool->getItem('fox_admin.settings_cache', join($this->domains, ','));
    }

    /**
     * Returns setting value
     *
     * @param string $setting
     * @param bool   $throwException
     *
     * @return mixed
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
     * Handles setting change event
     *
     * @param SettingChangeEvent $event
     */
    public function onSettingChange(
        /** @noinspection PhpUnusedParameterInspection */
        SettingChangeEvent $event
    ) {
        $this->pool->getItem('fox_admin.settings_cache')->clear();
    }

    /**
     * @return array
     */
    public function getDomains()
    {
        return $this->domains;
    }

    /**
     * @param array $domains
     */
    public function setDomains($domains)
    {
        $this->domains = $domains;
    }

    /**
     * @param string $domain
     */
    public function addDomain($domain)
    {
        $this->domains[] = $domain;
    }
}
