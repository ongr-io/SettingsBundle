<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Security\Authentication\Firewall;

use ONGR\SettingsBundle\Security\Authentication\Cookie\SessionlessAuthenticationCookieService;
use ONGR\SettingsBundle\Security\Authentication\Token\SessionlessToken;
use ONGR\CookiesBundle\Cookie\Model\CookieInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * Firewalls listener.
 */
class SessionlessAuthenticationListener implements ListenerInterface
{
    /**
     * @var AuthenticationManagerInterface
     */
    protected $authenticationManager;

    /**
     * @var SessionlessAuthenticationCookieService
     */
    protected $authCookieService;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var CookieInterface
     */
    protected $cookie;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        AuthenticationManagerInterface $authenticationManager,
        SessionlessAuthenticationCookieService $authCookieService,
        TokenStorageInterface $tokenStorage,
        CookieInterface $cookie
    ) {
        $this->authenticationManager = $authenticationManager;
        $this->authCookieService = $authCookieService;
        $this->tokenStorage = $tokenStorage;
        $this->cookie = $cookie;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetResponseEvent $event)
    {
        $cookieData = $this->cookie->getValue();

        if (!$this->authCookieService->validateCookie($cookieData)) {
            $this->tokenStorage->setToken(null);

            return false;
        }

        $token = new SessionlessToken(
            $cookieData['username'],
            $cookieData['expiration'],
            $event->getRequest()->getClientIp(),
            $cookieData['signature']
        );

        try {
            $regeneratedToken = $this->authenticationManager->authenticate($token);
            $this->tokenStorage->setToken($regeneratedToken);
        } catch (AuthenticationException $e) {
            $this->cookie->setClear(true);
            $this->tokenStorage->setToken(null);

            return false;
        }

        return $regeneratedToken;
    }
}
