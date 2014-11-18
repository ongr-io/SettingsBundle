<?php

/*
 *************************************************************************
 * NFQ eXtremes CONFIDENTIAL
 * [2013] - [2014] NFQ eXtremes UAB
 * All Rights Reserved.
 *************************************************************************
 * NOTICE:
 * All information contained herein is, and remains the property of NFQ eXtremes UAB.
 * Dissemination of this information or reproduction of this material is strictly forbidden
 * unless prior written permission is obtained from NFQ eXtremes UAB.
 *************************************************************************
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
