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

namespace ONGR\AdminBundle\Security\Core;

use ONGR\AdminBundle\Security\Authentication\Token\SessionlessToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Security context for querying whether access is granted for attribute.
 */
class SessionlessSecurityContext implements SecurityContextInterface
{
    /**
     * @var TokenInterface
     */
    private $token;

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * {@inheritdoc}
     */
    public function setToken(TokenInterface $token = null)
    {
        $this->token = $token;
    }

    /**
     * {@inheritdoc}
     *
     * Grants access for any attribute if cookie is of SessionlessToken instance.
     */
    public function isGranted($attributes, $object = null)
    {
        if ($this->getToken() instanceof SessionlessToken) {
            return true;
        }

        return false;
    }
}
