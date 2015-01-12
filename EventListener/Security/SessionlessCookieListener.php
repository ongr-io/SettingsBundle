<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\EventListener\Security;

use ONGR\CookiesBundle\Cookie\Model\JsonCookie;
use ONGR\SettingsBundle\Security\Authentication\Cookie\SessionlessAuthenticationCookieService;
use ONGR\SettingsBundle\Security\Authentication\Provider\SessionlessAuthenticationProvider;
use ONGR\SettingsBundle\Security\Authentication\Token\SessionlessToken;
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
     * @param SessionlessSecurityContext             $securityContext
     * @param AuthenticationProviderInterface        $authenticationProvider
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
     * Performs authentication if cookie is present.
     *
     * @param GetResponseEvent $event
     *
     * @return bool|\ONGR\SettingsBundle\Security\Authentication\Token\SessionlessToken
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
