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

use Fox\AdminBundle\Service\DomainsManager;
use Fox\AdminBundle\Service\UnderscoreEscaper;
use Fox\UtilsBundle\Settings\SettingsStructure;
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
        if ($routeName != 'fox_utils_settings_settings') {
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
            $domainId = 'fox_admin_domain_' . UnderscoreEscaper::escape($domain);
            $settings[$domainId] = [
                'name' => $domain,
                'category' => 'fox_admin_domains',
            ];
        }

        return $settings;
    }

    /**
     * @param \Fox\AdminBundle\Settings\PowerUserDomainsProvider $domainSettingsProvider
     */
    public function setDomainManager($domainSettingsProvider)
    {
        $this->domainManager = $domainSettingsProvider;
    }

    /**
     * @param \Fox\UtilsBundle\Settings\SettingsStructure $settingsStructure
     */
    public function setSettingsStructure($settingsStructure)
    {
        $this->settingsStructure = $settingsStructure;
    }
}
