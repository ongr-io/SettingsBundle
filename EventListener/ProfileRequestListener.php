<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\EventListener;

use ONGR\AdminBundle\Service\UnderscoreEscaper;
use ONGR\AdminBundle\Settings\Common\Provider\ManagerAwareSettingProvider;
use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\AdminBundle\Settings\Common\SettingsContainer;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Listens for request event and sets selected profiles from power-user cookie to SettingsContainer.
 */
class ProfileRequestListener
{
    /**
     * @var UserSettingsManager
     */
    protected $userSettingsManager;

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
        $settings = $this->userSettingsManager->getSettings();
        foreach ($settings as $id => $value) {
            $prefix = 'ongr_admin_profile_';
            if (strpos($id, $prefix) === 0 && $value === true) {
                $escapedProfile = mb_substr($id, strlen($prefix), null, 'UTF-8');
                $profile = UnderscoreEscaper::unescape($escapedProfile);
                $this->settingsContainer->addProfile($profile);
                $this->settingsContainer->addProvider($this->buildProvider($profile));
            }
        }
    }

    /**
     * @param \ONGR\AdminBundle\Settings\UserSettingsManager $userSettingsManager
     */
    public function setUserSettingsManager($userSettingsManager)
    {
        $this->userSettingsManager = $userSettingsManager;
    }

    /**
     * @param \ONGR\AdminBundle\Settings\Common\SettingsContainer $settingsContainer
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
