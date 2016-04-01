<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Controller;

use ONGR\CookiesBundle\Cookie\Model\JsonCookie;
use ONGR\SettingsBundle\Form\Type\LoginType;
use ONGR\SettingsBundle\Security\Authentication\Cookie\SessionlessAuthenticationCookieService;
use ONGR\SettingsBundle\Security\Authentication\Token\SessionlessToken;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for managing Admin login and logout.
 */
class UserController extends Controller
{
    /**
     * Login action.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        // Check if already logged in.
        $alreadyLoggedIn = $this->getSecurityContext()->getToken() instanceof SessionlessToken;

        // Handle form.
        $loginData = [];
        $form = $this->createForm(LoginType::class, $loginData);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $redirectResponse = $this->redirect($this->generateUrl('ongr_settings_sessionless_login'));
            $loginData = $form->getData();

            $username = $loginData['username'];
            $password = $loginData['password'];

            $ipAddress = $request->getClientIp();
            $cookieValue = $this->getAuthCookieService()->create($username, $password, $ipAddress);

            $cookie = $this->getAuthenticationCookie();
            $cookie->setValue($cookieValue);

            return $redirectResponse;
        }

        // Render.
        return $this->render(
            'ONGRSettingsBundle:User:login.html.twig',
            ['form' => $form->createView(), 'is_logged_in' => $alreadyLoggedIn]
        );
    }

    /**
     * Logout action.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function logoutAction(
        /** @noinspection PhpUnusedParameterInspection */
        Request $request
    ) {
        $cookie = $this->getAuthenticationCookie();
        $cookie->setClear(true);

        $response = $this->redirect($this->generateUrl('ongr_settings_sessionless_login'));

        $this->get('security.token_storage')->setToken(null);
        $request->getSession()->invalidate();

        return $response;
    }

    /**
     * @return SessionlessAuthenticationCookieService
     */
    protected function getAuthCookieService()
    {
        $this->authCookieService = $this->get(
            'ongr_settings.authentication.authentication_cookie_service'
        );

        return $this->authCookieService;
    }

    /**
     * @return SessionlessSecurityContext
     */
    protected function getSecurityContext()
    {
        return $this->get('security.token_storage');
    }

    /**
     * @return JsonCookie
     */
    protected function getAuthenticationCookie()
    {
        return $this->get('ongr_settings.authentication.authentication_cookie');
    }
}
