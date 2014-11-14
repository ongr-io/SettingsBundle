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

namespace ONGR\AdminBundle\Settings;

use ONGR\AdminBundle\Service\DomainsManager;
use ONGR\AdminBundle\Service\UnderscoreEscaper;
use ONGR\UtilsBundle\Settings\SettingsStructure;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Responsible for collecting all settings' domains from DDAL
 */
class PowerUserDomainsProvider
{
    /**
     * @var DomainsManager
     */
    protected $domainManager;

    /**
     * @var SettingsStructure
     */
    protected $settingsStructure;

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $routeName = $event->getRequest()->get('_route');
        if ($routeName != 'ongr_utils_settings_settings') {
            return;
        }

        $this->settingsStructure->extractSettings($this, 'getSettings');
    }

    /**
     * Get domain list to display in power-user select list
     *
     * @return array
     */
    public function getSettings()
    {
        $domains = $this->domainManager->getDomains();

        $settings = [];
        foreach ($domains as $domain) {
            $domainId = 'ongr_admin_domain_' . UnderscoreEscaper::escape($domain);
            $settings[$domainId] = [
                'name' => $domain,
                'category' => 'ongr_admin_domains',
            ];
        }

        return $settings;
    }

    /**
     * @param \ONGR\AdminBundle\Settings\PowerUserDomainsProvider $domainSettingsProvider
     */
    public function setDomainManager($domainSettingsProvider)
    {
        $this->domainManager = $domainSettingsProvider;
    }

    /**
     * @param \ONGR\UtilsBundle\Settings\SettingsStructure $settingsStructure
     */
    public function setSettingsStructure($settingsStructure)
    {
        $this->settingsStructure = $settingsStructure;
    }
}
