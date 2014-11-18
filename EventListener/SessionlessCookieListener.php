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

namespace Fox\UtilsBundle\EventListener;

use Fox\UtilsBundle\Cookie\Model\JsonCookie;
use Fox\UtilsBundle\Security\Authentication\Cookie\SessionlessAuthenticationCookieService;
use Fox\UtilsBundle\Security\Authentication\Provider\SessionlessAuthenticationProvider;
use Fox\UtilsBundle\Security\Authentication\Token\SessionlessToken;
use Fox\UtilsBundle\Security\Core\SessionlessSecurityContext;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Listener that checks for auth cookie and authenticates the user.
 */
class SessionlessCookieListener
{
    /**
     * @var SessionlessSecurityContext
     */
    private $securityContext;

    /**
     * @var SessionlessAuthenticationProvider
     */
    private $authenticationProvider;

    /**
     * @var SessionlessAuthenticationCookieService
     */
    private $authCookieService;

    /**
     * @var JsonCookie
     */
    protected $authCookie;

    /**
     * @param SessionlessSecurityContext $securityContext
     * @param AuthenticationProviderInterface $authenticationProvider
     * @param SessionlessAuthenticationCookieService $authCookieService
     */
    public function __construct(
        $securityContext,
        $authenticationProvider,
        $authCookieService
    ) {
        $this->securityContext = $securityContext;
        $this->authenticationProvider = $authenticationProvider;
        $this->authCookieService = $authCookieService;
    }

    /**
     * {@inheritdoc}
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $authenticatedToken = $this->authenticateCookie($event);

        if ($authenticatedToken instanceof SessionlessToken) {
            $this->securityContext->setToken($authenticatedToken);
        }
    }

    /**
     * @param GetResponseEvent $event
     * @return bool|\Fox\UtilsBundle\Security\Authentication\Token\SessionlessToken
     */
    private function authenticateCookie(GetResponseEvent $event)
    {
        $cookieData = $this->authCookie->getValue();

        if ($cookieData !== null) {
            if (!$this->authCookieService->validateCookie($cookieData)) {
                return false;
            }

            $token = new SessionlessToken(
                $cookieData['username'],
                $cookieData['expiration'],
                $event->getRequest()->getClientIp(),
                $cookieData['signature']
            );

            try {
                $authenticatedToken = $this->authenticationProvider->authenticate($token);
            } catch (AuthenticationException $e) {
                return false;
            }

            return $authenticatedToken;
        }

        return false;
    }

    /**
     * @param JsonCookie $authCookie
     */
    public function setAuthCookie($authCookie)
    {
        $this->authCookie = $authCookie;
    }
}
