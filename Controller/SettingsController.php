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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SettingsListController. Is used for managing settings in General env.
 */
class SettingsController extends Controller
{

    public function enableProfileAction($key)
    {
        $profileName = $key;

        $cookie = $this->get('ongr_settings.cookie.active_profiles');
        $currentActiveProfiles = $cookie->getValue();

        if (is_array($currentActiveProfiles) && !array_intersect($currentActiveProfiles, [$profileName])) {
            $currentActiveProfiles[] = $profileName;
            $cookie->setValue($currentActiveProfiles);
        } else {
            $cookie->setValue([$profileName]);
        }

        $settingsManager = $this->get('ongr_settings.settings_manager');
        $settings = $settingsManager->getProfileSettings($profileName);

        return $this->render('ONGRSettingsBundle:Settings:enableProfile.html.twig', [
            'profile' => $profileName,
            'profiles' => $settings,
        ]);
    }

    /**
     * Renders settings list page.
     *
     * @return Response
     */
    public function listAction()
    {
        return $this->render('ONGRSettingsBundle:Settings:list.html.twig');
    }

    /**
     * Setting update action.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateValueAction(Request $request)
    {
        $name = $request->get('name');
        $value = $request->get('value');

        $manager = $this->get('ongr_settings.settings_manager');
        $manager->update($name, ['value' => $value]);

        return new JsonResponse(['error' => false]);
    }

    /**
     * Setting delete action
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request)
    {
        try {
            $manager = $this->get('ongr_settings.settings_manager');
            $manager->delete($request->get('name'));

            return new JsonResponse(['error' => false]);
        } catch (\Exception $e) {
            return new JsonResponse(
                [
                    'error' => true,
                    'message' => 'Error occurred please try to delete setting again.'
                ]
            );
        }
    }

    /**
     * Submit action to create or edit setting if not exists.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function submitAction(Request $request)
    {
        try {
            $manager = $this->get('ongr_settings.settings_manager');
            $data = $request->get('setting');

            if (!empty($data['value']) && !is_string($data['value'])) {
                $data['value'] = json_encode($data['value']);
            }

            if ($request->get('force')) {
                $name = $request->get('name');
                $manager->update($name, $data);
            } else {
                $manager->create($data);
            }

            return new JsonResponse(['error' => false]);
        } catch (\Exception $e) {
            return new JsonResponse(
                [
                    'error' => true,
                    'message' => 'Error occurred! Something is wrong with provided data. '.
                        'Please try to submit form again.'
                ]
            );
        }
    }
}
