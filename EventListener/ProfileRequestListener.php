<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\EventListener;

use ONGR\SettingsBundle\Service\UnderscoreEscaper;
use ONGR\SettingsBundle\Settings\General\Provider\ManagerAwareSettingProvider;
use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\SettingsBundle\Settings\General\SettingsContainer;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use ONGR\SettingsBundle\Settings\Personal\PersonalSettingsManager;

/**
 * Listens for request event and sets selected profiles from admin-user cookie to SettingsContainer.
 */
class ProfileRequestListener
{
    /**
     * @var PersonalSettingsManager
     */
    protected $generalSettingsManager;

    /**
     * @var SettingsContainer
     */
    protected $settingsContainer;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * On kernel request.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(
        /** @noinspection PhpUnusedParameterInspection */
        GetResponseEvent $event
    ) {
        $settings = $this->generalSettingsManager->getSettings();
        foreach ($settings as $id => $value) {
            $prefix = 'ongr_settings_profile_';
            if (strpos($id, $prefix) === 0 && $value === true) {
                $escapedProfile = mb_substr($id, strlen($prefix), null, 'UTF-8');
                $profile = UnderscoreEscaper::unescape($escapedProfile);
                $this->settingsContainer->addProfile($profile);
                $this->settingsContainer->addProvider($this->buildProvider($profile));
            }
        }
    }

    /**
     * @param \ONGR\SettingsBundle\Settings\Personal\PersonalSettingsManager $generalSettingsManager
     */
    public function setPersonalSettingsManager($generalSettingsManager)
    {
        $this->generalSettingsManager = $generalSettingsManager;
    }

    /**
     * @param \ONGR\SettingsBundle\Settings\General\SettingsContainer $settingsContainer
     */
    public function setSettingsContainer($settingsContainer)
    {
        $this->settingsContainer = $settingsContainer;
    }

    /**
     * @param Manager $manager
     */
    public function setManager(Manager $manager)
    {
        $this->manager = $manager;
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
        $provider->setManager($this->manager);

        return $provider;
    }
}
