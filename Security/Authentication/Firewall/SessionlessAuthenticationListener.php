<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Security\Authentication\Firewall;

use ONGR\AdminBundle\Security\Authentication\Cookie\SessionlessAuthenticationCookieService;
use ONGR\AdminBundle\Security\Authentication\Token\SessionlessToken;
use ONGR\CookiesBundle\Cookie\Model\CookieInterface;
use ONGR\AdminBundle\Security\Authentication\Cookie\SessionlessSignatureGenerator;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContext;
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
     * @var SecurityContext
     */
    protected $securityContext;

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
        SecurityContext $securityContext,
        CookieInterface $cookie
    ) {
        $this->authenticationManager = $authenticationManager;
        $this->authCookieService = $authCookieService;
        $this->securityContext = $securityContext;
        $this->cookie = $cookie;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetResponseEvent $event)
    {
        $cookieData = $this->cookie->getValue();

        if (!$this->authCookieService->validateCookie($cookieData)) {
            $this->securityContext->setToken(null);

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
            $this->securityContext->setToken($regeneratedToken);
        } catch (AuthenticationException $e) {
            $this->cookie->setClear(true);
            $this->securityContext->setToken(null);

            return false;
        }

        return $regeneratedToken;
    }
}
