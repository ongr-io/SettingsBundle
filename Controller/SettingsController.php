<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace ONGR\AdminBundle\Controller;

use ONGR\CookiesBundle\Cookie\Model\CookieInterface;
use ONGR\AdminBundle\Form\Type\SettingsType;
use ONGR\AdminBundle\Settings\UserSettingsManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for managing user settings.
 */
class SettingsController extends Controller
{
    /**
     * @return UserSettingsManager
     */
    protected function getUserSettingsManager()
    {
        return $this->get('ongr_admin.settings.user_settings_manager');
    }

    /**
     * Main action for changing settings.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function settingsAction(Request $request)
    {
        $manager = $this->getUserSettingsManager();
        if (!$manager->isAuthenticated()) {
            return $this->redirect($this->generateUrl('ongr_admin_sessionless_login'));
        }

        // Handle form.
        $settingsData = $manager->getSettings();
        $settingsMap = $manager->getSettingsMap();

        $form = $this->createForm(new SettingsType($settingsMap), $settingsData);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $manager->setSettingsFromForm($form->getData());
            $redirectResponse = $this->redirect($request->getUri());
            $settings = $manager->getSettings();
            $this->attachCookies($settings, $settingsMap);

            return $redirectResponse;
        }

        // Build settings layout within categories.
        $categoryMap = $manager->getCategoryMap();
        foreach ($settingsMap as $settingId => $setting) {
            $categoryMap[$setting['category']]['settings'][$settingId] = array_merge(
                $setting,
                [
                    'link' => $request->getUriForPath(
                        $this->generateUrl(
                            'ongr_admin_settings_change',
                            [
                                'hash' => base64_encode($settingId),
                            ]
                        )
                    ),
                ]
            );
        }

        // Render.
        return $this->render(
            'ONGRAdminBundle:Settings:settings.html.twig',
            [
                'form' => $form->createView(),
                'categories' => $categoryMap,
            ]
        );
    }

    /**
     * Creates new Setting.
     *
     * @param Request $request
     * @param string  $hash
     *
     * @return JsonResponse
     */
    public function changeSettingAction(Request $request, $hash)
    {
        $manager = $this->getUserSettingsManager();

        if (!$manager->isAuthenticated()) {
            return new JsonResponse(Response::$statusTexts[403], 403);
        }

        $name = base64_decode($hash);
        $settings = $manager->getSettings();

        if (array_key_exists($name, $settings)) {
            $settings[$name] = !$settings[$name];
            $manager->setSettingsFromCookie($settings);
            $this->attachCookies($manager->getSettings(), $manager->getSettingsMap());
        }

        return new JsonResponse();
    }

    /**
     * Set cookies.
     *
     * @param array $settings
     * @param array $settingsMap
     */
    protected function attachCookies(array $settings, array $settingsMap)
    {
        $cookies = [];

        foreach ($settings as $settingId => $setting) {
            $cookieServiceName = $settingsMap[$settingId]['cookie'];
            $cookies[$cookieServiceName][$settingId] = $setting;
        }

        foreach ($cookies as $cookieServiceName => $cookieSettings) {
            /** @var CookieInterface $cookie */
            $cookie = $this->get($cookieServiceName);
            $cookie->setValue($cookieSettings);
        }
    }
}
