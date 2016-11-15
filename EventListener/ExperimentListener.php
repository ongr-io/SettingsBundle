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

use DeviceDetector\DeviceDetector;
use ONGR\CookiesBundle\Cookie\Model\GenericCookie;
use ONGR\SettingsBundle\Service\SettingsManager;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class ExperimentListener
{
    /**
     * @var GenericCookie
     */
    private $cookie;

    /**
     * @var SettingsManager
     */
    private $settingsManager;

    /**
     * @var string
     */
    private $activeExperimentsSettingName;

    /**
     * ExperimentListener constructor.
     * @param GenericCookie   $cookie
     * @param SettingsManager $settingsManager
     * @param string          $settingName
     */
    public function __construct(GenericCookie $cookie, SettingsManager $settingsManager, $settingName)
    {
        $this->cookie = $cookie;
        $this->settingsManager = $settingsManager;
        $this->activeExperimentsSettingName = $settingName;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest() || (is_array($this->cookie->getValue()) && !empty($this->cookie->getValue()))) {
            return;
        }

        $experiments = $this->settingsManager->getActiveExperiments();

        if (empty($experiments)) {
            return;
        }

        $dd = new DeviceDetector($event->getRequest()->headers->get('User-Agent'));
        $experimentalProfiles = [];
        $dd->parse();

        foreach ($experiments as $experiment) {
            $experiment = $this->settingsManager->getCachedExperiment($experiment);
            $targets = json_decode($experiment['value'], true);

            if (isset($targets['Clients'])) {
                if (isset($targets['Clients']['types']) &&
                    !in_array(
                        strtolower($dd->getClient()['type']),
                        array_map('strtolower', $targets['Clients']['types'])
                    )
                ) {
                    continue;
                }

                if (isset($targets['Clients']['clients']) &&
                    !in_array($dd->getClient()['name'], $targets['Clients']['clients'])
                ) {
                    continue;
                }
            }

            if (isset($targets['OS']['types']) && !in_array($dd->getOs()['name'], $targets['OS']['types'])) {
                continue;
            }

            if (isset($targets['Devices'])) {
                if (isset($targets['Devices']['types']) &&
                    !in_array($dd->getDeviceName(), $targets['Devices']['types'])
                ) {
                    continue;
                }

                if (isset($targets['Devices']['brands']) && !in_array($dd->getBrand(), $targets['Devices']['brands'])) {
                    continue;
                }
            }

            $experimentalProfiles = array_merge($experimentalProfiles, $experiment['profile']);
        }

        if (empty($experimentalProfiles)) {
            return;
        }

        $this->cookie->setValue($experimentalProfiles);
    }
}
