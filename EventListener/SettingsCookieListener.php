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

use ONGR\CookiesBundle\Cookie\Model\JsonCookie;
use ONGR\CookiesBundle\Utils\ContainerAwareTrait;
use ONGR\AdminBundle\Settings\UserSettingsManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Listener that checks for settings cookie and passes it to user settings manager.
 */
class SettingsCookieListener
{
    use ContainerAwareTrait;

    /**
     * @var UserSettingsManager
     */
    protected $userSettingsManager;

    /**
     * {@inheritdoc}
     */
    public function onKernelRequest(
        /** @noinspection PhpUnusedParameterInspection */
        GetResponseEvent $event
    ) {
        $settingsMap = $this->userSettingsManager->getSettingsMap();
        $cookiesServiceNames = array_map(
            function ($setting) {
                return $setting['cookie'];
            },
            $settingsMap
        );
        $cookiesServiceNames = array_unique($cookiesServiceNames);

        foreach ($cookiesServiceNames as $cookieServiceName) {
            /** @var JsonCookie $cookie */
            $cookie = $this->container->get($cookieServiceName, ContainerInterface::NULL_ON_INVALID_REFERENCE);
            if ($cookie !== null && $cookie->getValue() !== null) {
                $this->userSettingsManager->addSettingsFromCookie($cookie->getValue());
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
}
