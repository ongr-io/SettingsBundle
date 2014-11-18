<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Security\Authentication\Cookie;

/**
 * Service for signing data with secret key.
 */
class SessionlessSignatureGenerator
{
    /**
     * @var string
     */
    private $secret;

    /**
     * @param string $secret
     */
    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    /**
     * Generate.
     *
     * @param string $username
     * @param string $password
     * @param int    $expirationTime
     * @param string $ipAddress
     *
     * @return string
     */
    public function generate($username, $password, $expirationTime, $ipAddress)
    {
        $data = [$username, $password, (string)$expirationTime, $ipAddress, $this->secret];
        $signature = str_replace('=', '', base64_encode(substr(hash('sha256', implode('|', $data), true), 0, 16)));

        return $signature;
    }
}
