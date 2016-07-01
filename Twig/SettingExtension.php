<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Twig;
use Doctrine\Common\Cache\PhpFileCache;
use ONGR\SettingsBundle\Document\Setting;
use ONGR\SettingsBundle\Service\SettingsManager;

/**
 * Class SettingExtension to show settings value on twig.
 */
class SettingExtension extends \Twig_Extension
{
    /**
     * Extension name
     */
    const NAME = 'ongr_setting_extension';

    /**
     * @var SettingsManager
     */
    private $manager;

    /**
     * @var PhpFileCache
     */
    private $cache;

    /**
     * @param SettingsManager $manager
     * @param PhpFileCache    $cache
     */
    public function __construct($manager, $cache)
    {
        $this->manager = $manager;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('ongr_setting', [$this, 'getSettingValue']),
        ];
    }

    /**
     * @param string $name
     * @param bool   $default
     *
     * @return mixed
     */
    public function getSettingValue($name, $default = false)
    {
        $settingName = 'ongr_setting.'.$name;

        if ($this->cache->contains($settingName)) {
            return $this->cache->fetch($settingName);
        }

        $allProfiles = $this->cache->fetch('active_profiles');

        if ($allProfiles === false) {
            $allProfiles = $this->manager->getAllProfilesNameList(true);
            $this->cache->save('active_profiles', $allProfiles);
        }

        /** @var Setting $setting */
        $setting = $this->manager->get($name);
        $settingProfile = is_array($setting->getProfile())?$setting->getProfile():[$setting->getProfile()];
        if ($setting && array_intersect($settingProfile, $allProfiles)) {
            $settingValue = $setting->getValue();
        } else {
            $settingValue = $default;
        }

        $this->cache->save($settingName, $settingValue);

        return $settingValue;
    }
}
