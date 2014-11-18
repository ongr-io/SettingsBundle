<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
