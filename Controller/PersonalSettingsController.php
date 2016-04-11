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

use ONGR\SettingsBundle\Form\Type\SettingsType;
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
        $settingsData = $this->get('stash')->getItem('ongr_settings')->get();
        $settingsMap = $manager->getSettingsMap();
        $options = [
            'settingsStructure' => $settingsMap,
        ];

        $form = $this->createForm(SettingsType::class, $settingsData, $options);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $manager->setSettingsFromForm($form->getData());
            $redirectResponse = $this->redirect($request->getUri());
            $settings = $manager->getSettings();
            $this->saveStash($settings);
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
                'form' => $form->createView(),
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
            $settings = $this->get('stash')->getItem('ongr_settings')->get();
            if (array_key_exists($name, $settings)) {
                $settings[$name] = !$settings[$name];
            } else {
                $settings[$name] = true;
            }

            $manager->setSettingsFromStash($settings);
            $this->saveStash($manager->getSettings());

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

    /**
     * Sets cookie values from settings based on settings map.
     *
     * @param array $settings
     */
    protected function saveStash(array $settings)
    {
        $pool = $this->get('stash');
        $stashSettings = $pool->getItem('ongr_settings');
        $stashSettings->set($settings);
        $pool->save($stashSettings);
    }
}
