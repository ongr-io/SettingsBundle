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


use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RequestListener
{
    private $cache;

    public function __construct($cache)
    {
        $this->cache = $cache;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        // ...
    }
}
