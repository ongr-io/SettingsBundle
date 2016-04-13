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

use ONGR\SettingsBundle\Settings\Personal\PersonalSettingsManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for managing General (private) settings.
 */
class PersonalSettingsController extends Controller
{
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
        $manager = $this->getPersonalSettingsManager();
        // Handle form.
        $settingsData = $manager->getSettings();
        $settingsMap = $manager->getSettingsMap();

        // Build settings layout within categories.
        $categoryMap = $manager->getCategoryMap();
        foreach ($settingsMap as $settingId => $setting) {
            $categoryMap[$setting['category']]['settings'][$settingId] = array_merge(
                $setting,
                [
                    'link' => $request->getUriForPath(
                        $this->generateUrl(
                            'ongr_settings_personal_settings_change',
                            [
                                'encodedName' => base64_encode($settingId),
                            ]
                        )
                    ),
                ]
            );
        }

        // Render.
        return $this->render(
            'ONGRSettingsBundle:Settings:settings.html.twig',
            [
                'settings' => $settingsData,
                'categories' => $categoryMap,
            ]
        );
    }

    /**
     * Creates new Setting.
     *
     * @param Request $request     Request to process, not used here.
     * @param string  $encodedName Base64 encoded setting name.
     *
     * @return JsonResponse
     */
    public function changeSettingAction(Request $request, $encodedName)
    {
        $manager = $this->getPersonalSettingsManager();

        $name = base64_decode($encodedName);

        $settingsStructure = $manager->getSettingsMap();
        if (array_key_exists($name, $settingsStructure)) {
            $settings = $manager->getSettings();
            if (array_key_exists($name, $settings)) {
                $settings[$name] = !$settings[$name];
            } else {
                $settings[$name] = true;
            }

            $manager->setSettingsFromStash($settings);
            $manager->save();

            return new JsonResponse();
        } else {
            return new JsonResponse(Response::$statusTexts[403], 403);
        }
    }

    /**
     * @return PersonalSettingsManager
     */
    protected function getPersonalSettingsManager()
    {
        return $this->get('ongr_settings.settings.personal_settings_manager');
    }
}
