<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Functional;

use ONGR\AdminBundle\Security\Authentication\Cookie\SessionlessAuthenticationCookieService;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\BrowserKit\Cookie;

/**
 * Helper for setting test cookies.
 */
class CookieTestHelper
{
    /**
     * Construct authenticated cookie and set it in the cookie jar.
     *
     * This method is also used by fox-admin.
     *
     * @param Client $client
     * @param int    $expirationTime
     */
    public static function setAuthenticationCookie(Client $client, $expirationTime = null)
    {
        /** @var SessionlessAuthenticationCookieService $cookieService */
        $cookieService = $client->getContainer()->get('ongr_admin.authentication.authentication_cookie_service');
        $cookieService->setMockTime($expirationTime);
        $value = $cookieService->create('foo_user', 'foo_password', '127.0.0.1');

        $cookie = new Cookie('ongr_admin_user_auth', json_encode($value));
        $client->getCookieJar()->set($cookie);
    }

    /**
     * Create settings cookies and set it in the cookie jar.
     *
     * @param Client $client
     * @param array  $cookies
     */
    public static function setSettingsCookie(Client $client, array $cookies)
    {
        foreach ($cookies as $name => $cookieSettings) {
            $cookie = new Cookie($name, json_encode($cookieSettings));
            $client->getCookieJar()->set($cookie);
        }
    }
}
