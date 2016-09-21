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
    private $cookie;

    /**
     * @var SettingsManager
     */
    private $settingsManager;

    public function __construct($cookie, $settingsManager)
    {
        $this->cookie = $cookie;
        $this->settingsManager = $settingsManager;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $profiles = $this->cookie->getValue();

        if (is_array($profiles)) {
            $this->settingsManager->appendActiveProfilesList($profiles);
        }
    }
}
