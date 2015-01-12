<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Security\Authentication\Cookie;

use Symfony\Component\HttpFoundation\Cookie;

/**
 * Service for working with authentication cookie values and representation.
 */
class SessionlessAuthenticationCookieService
{
    /**
     * @var string
     */
    private $expirationInterval;

    /**
     * @var SessionlessSignatureGenerator
     */
    private $generator;

    /**
     * @var null|int Should mock time.
     */
    private $mockTime;

    /**
     * @param SessionlessSignatureGenerator $generator
     * @param int                           $expirationInterval
     */
    public function __construct($generator, $expirationInterval)
    {
        $this->generator = $generator;
        $this->expirationInterval = $expirationInterval;
    }

    /**
     * Creates cookies.
     *
     * @param string $username
     * @param string $password
     * @param string $ipAddress
     *
     * @return Cookie
     */
    public function create($username, $password, $ipAddress)
    {
        if ($this->mockTime === null) {
            $time = time();
        } else {
            $time = $this->mockTime;
        }

        $expiration = (new \DateTime())->setTimestamp($time)->add(new \DateInterval($this->expirationInterval));
        $expirationTimestamp = $expiration->getTimestamp();
        $signature = $this->generator->generate(
            $username,
            $password,
            $expirationTimestamp,
            $ipAddress
        );

        $cookieValue = [
            'username' => $username,
            'expiration' => $expirationTimestamp,
            'signature' => $signature,
        ];

        return $cookieValue;
    }

    /**
     * @param null|int $mockTime
     */
    public function setMockTime($mockTime)
    {
        $this->mockTime = $mockTime;
    }

    /**
     * Validate browser cookie value.
     *
     * @param array $cookie
     *
     * @return bool
     */
    public function validateCookie($cookie)
    {
        if (!is_array($cookie)) {
            return false;
        }

        if (!isset($cookie['username']) || !is_string($cookie['username']) || strlen($cookie['username']) === 0) {
            return false;
        }

        if (!isset($cookie['expiration']) || !is_int($cookie['expiration']) || $cookie['expiration'] < 1) {
            return false;
        }

        if (!isset($cookie['signature']) || !is_string($cookie['signature']) || strlen($cookie['signature']) === 0) {
            return false;
        }

        return true;
    }
}
