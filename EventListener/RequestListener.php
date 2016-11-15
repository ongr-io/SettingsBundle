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

use ONGR\CookiesBundle\Cookie\Model\GenericCookie;
use ONGR\SettingsBundle\Service\SettingsManager;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RequestListener
{
    /**
     * @var GenericCookie
     */
    private $profileCookie;

    /**
     * @var GenericCookie
     */
    private $experimentCookie;

    /**
     * @var SettingsManager
     */
    private $settingsManager;

    public function __construct($profileCookie, $experimentCookie, $settingsManager)
    {
        $this->profileCookie = $profileCookie;
        $this->experimentCookie = $experimentCookie;
        $this->settingsManager = $settingsManager;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $profileCookie = $this->profileCookie->getValue() ? $this->profileCookie->getValue() : [];
        $expCookie = $this->experimentCookie->getValue() ? $this->experimentCookie->getValue() : [];

        $profiles = array_merge($profileCookie, $expCookie);

        if (is_array($profiles)) {
            $this->settingsManager->appendActiveProfilesList($profiles);
        }
    }
}
