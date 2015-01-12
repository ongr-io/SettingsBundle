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

use ONGR\SettingsBundle\Flashbag\DirtyFlashBag;
use ONGR\CookiesBundle\Cookie\Model\CookieInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;

/**
 * Injects FlashBag cookie into FlashBag service on request and saves FlashBag data to cookie on response.
 */
class FlashBagCookieInjectorListener
{
    /**
     * @var CookieInterface
     */
    protected $flashBagCookie;

    /**
     * @var DirtyFlashBag
     */
    protected $flashBagService;

    /**
     * @param DirtyFlashBag   $flashBagService
     * @param CookieInterface $flashBagCookie
     */
    public function __construct(DirtyFlashBag $flashBagService, CookieInterface $flashBagCookie)
    {
        $this->setFlashBagService($flashBagService);
        $this->setFlashBagCookie($flashBagCookie);
    }

    /**
     * Load messages from flash-bag cookie into flash-bag service.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernel::MASTER_REQUEST !== $event->getRequestType() || $event->getRequest()->isXmlHttpRequest()) {
            return;
        }

        $messages = $this->flashBagCookie->getValue();

        if (!is_array($messages) || !count($messages)) {
            return;
        }

        $this->flashBagService->initialize($messages);
    }

    /**
     * Save messages from flash-bag service to flash-bag cookie.
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (HttpKernel::MASTER_REQUEST !== $event->getRequestType() || $event->getRequest()->isXmlHttpRequest()) {
            return;
        }

        if (!$this->flashBagService->isDirty()) {
            return;
        }

        $messages = $this->flashBagService->all();

        $this->flashBagCookie->setValue($messages);

        $response = $event->getResponse();
        $response->headers->setCookie($this->flashBagCookie->toCookie());
    }

    /**
     * @param CookieInterface $flashBagCookie
     *
     * @return $this
     */
    protected function setFlashBagCookie(CookieInterface $flashBagCookie)
    {
        $this->flashBagCookie = $flashBagCookie;

        return $this;
    }

    /**
     * @param DirtyFlashBag $flashBagService
     *
     * @return $this
     */
    protected function setFlashBagService(DirtyFlashBag $flashBagService)
    {
        $this->flashBagService = $flashBagService;

        return $this;
    }
}
