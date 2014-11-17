<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Fixtures\Cookie;

use ONGR\CookiesBundle\Cookie\Model\JsonCookie;
use ONGR\AdminBundle\Utils\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Test controller for testing cookie model modifications.
 */
class CookieController
{
    use ContainerAwareTrait;

    /**
     * Read cookie value.
     *
     * @return JsonResponse
     */
    public function readAction()
    {
        /** @var JsonCookie $cookie */
        $cookie = $this->container->get('project.cookie_foo');

        return new JsonResponse($cookie->getValue());
    }

    /**
     * Update cookie value.
     *
     * @return JsonResponse
     */
    public function updateAction()
    {
        /** @var JsonCookie $cookie */
        $cookie = $this->container->get('project.cookie_foo');
        $cookie->setValue(['bar']);
        $cookie->setExpiresTime(2000000000);

        return new JsonResponse(['updated']);
    }

    /**
     * Clear cookie.
     *
     * @return JsonResponse
     */
    public function clearAction()
    {
        /** @var JsonCookie $cookie */
        $cookie = $this->container->get('project.cookie_foo');
        $cookie->setClear(true);

        return new JsonResponse(['cleared']);
    }
}
