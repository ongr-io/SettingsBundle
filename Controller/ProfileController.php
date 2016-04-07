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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class SettingsListController. Is used for managing settings in General env.
 */
class ProfileController extends Controller
{
    /**
     * Renders add setting form.
     *
     * @return Response
     */
    public function displayAction()
    {
        $params = [];
        $cache = $this->get('es.cache_engine');
        if ($cache->contains('settings_errors')) {
            $params['errors'] = $cache->fetch('settings_errors');
            $cache->delete('settings_errors');
        } elseif ($cache->contains('settings_success')) {
            $params['success'] = $cache->fetch('settings_success');
            $cache->delete('settings_success');
        }
        return $this->render(
            'ONGRSettingsBundle:Settings:addProfile.html.twig', $params
        );
    }

    /**
     * Action for creating a profile
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $profileManager = $this->get('ongr_settings.profiles_manager');
        $profiles = $profileManager->getAllProfiles();
        $data = $this->get('ongr_settings.form_validator')->validateProfileForm($request, $profiles);
        $cache = $this->get('es.cache_engine');

        if ($data['error'] != '') {
            $cache->save('settings_errors', $data['error']);
            return new RedirectResponse($this->generateUrl('ongr_settings_profile_add'));
        }
        try {
            $profileManager->createProfile($data['name'], $data['description']);
        }catch (\Exception $e) {
            $cache->save('settings_errors', $e->getMessage());
            return new RedirectResponse($this->generateUrl('ongr_settings_profile_add'));
        }
        $cache->save('settings_success', true);
        return new RedirectResponse($this->generateUrl('ongr_settings_profile_add'));
    }
}
