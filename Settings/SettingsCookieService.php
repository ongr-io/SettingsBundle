<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Settings;

use Symfony\Component\HttpFoundation\Cookie;

/**
 * Service for working with settings cookie values.
 *
 * @deprecated Will be removed in 2.x. Use cookie model 'ongr_admin.settings.settings_cookie'.
 * @codeCoverageIgnore
 */
class SettingsCookieService
{
    /**
     * @var string
     */
    private $settingsCookieName;

    /**
     * @param string $settingsCookieName
     */
    public function __construct($settingsCookieName)
    {
        $this->settingsCookieName = $settingsCookieName;
    }

    /**
     * Create cookie from settings array.
     *
     * @param array       $settings   Settings array.
     * @param null|string $cookieName Null for default name.
     *
     * @return Cookie
     */
    public function create(array $settings, $cookieName = null)
    {
        $expiration = time() + 365 * 24 * 60 * 60;
        $cookieValue = json_encode($settings);
        $cookie = new Cookie($cookieName ? : $this->settingsCookieName, $cookieValue, $expiration, '/');

        return $cookie;
    }
}
