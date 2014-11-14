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

namespace ONGR\AdminBundle\EventListener;

use ONGR\AdminBundle\Service\UnderscoreEscaper;
use ONGR\AdminBundle\Settings\Provider\SessionModelAwareProvider;
use ONGR\AdminBundle\Settings\SettingsContainer;
use Fox\DDALBundle\Session\SessionModelInterface;
use ONGR\UtilsBundle\Settings\UserSettingsManager;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Listens for request event and sets selected profiles from power-user cookie to SettingsContainer
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

    /** @var  SessionModelInterface */
    protected $settingModel;

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(
        /** @noinspection PhpUnusedParameterInspection */
        GetResponseEvent $event
    ) {
        $settings = $this->userSettingsManager->getSettings();

        foreach ($settings as $id => $value) {
            $prefix = 'ongr_admin_domain_';
            if (strpos($id, $prefix) === 0 && $value === true) {
                $escapedDomain = mb_substr($id, strlen($prefix), null, 'UTF-8');
                $domain = UnderscoreEscaper::unescape($escapedDomain);
                $this->settingsContainer->addDomain($domain);
                $this->settingsContainer->addProvider($this->buildProvider($domain));
            }
        }
    }

    /**
     * @param \ONGR\UtilsBundle\Settings\UserSettingsManager $userSettingsManager
     */
    public function setUserSettingsManager($userSettingsManager)
    {
        $this->userSettingsManager = $userSettingsManager;
    }

    /**
     * @param \ONGR\AdminBundle\Settings\SettingsContainer $settingsContainer
     */
    public function setSettingsContainer($settingsContainer)
    {
        $this->settingsContainer = $settingsContainer;
    }

    /**
     * @param SessionModelInterface $settingModel
     */
    public function setSettingModel(SessionModelInterface $settingModel)
    {
        $this->settingModel = $settingModel;
    }

    /**
     * @param string $domain
     * @return SessionModelAwareProvider
     */
    private function buildProvider($domain)
    {
        $provider = new SessionModelAwareProvider($domain);
        $provider->setSessionModel($this->settingModel);

        return $provider;
    }
}
