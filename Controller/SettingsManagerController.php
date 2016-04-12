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

use ONGR\SettingsBundle\Settings\General\SettingsManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Yaml\Dumper;

/**
 * SettingsManager controller responsible for CRUD actions from frontend for settings.
 *
 * @package ONGR\SettingsBundle\Controller
 */
class SettingsManagerController extends Controller
{
    /**
     * Action for saving/seting setting values.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function setSettingAction(Request $request)
    {
        $data = $this->get('ongr_settings.form_validator')->validateSettingForm($request);
        $cache = $this->get('es.cache_engine');

        if ($data['error'] != '') {
            $cache->save('settings_errors', $data['error']);
            return new RedirectResponse($this->generateUrl('ongr_settings_settings_add'));
        }
        $manager = $this->getSettingsManager();

        try {
            $manager->set(
                $data['name'],
                $data['type'],
                $data['description'],
                $data['value'],
                $data['profiles']
            );
        } catch (\Exception $e) {
            $cache->save('settings_errors', $e->getMessage());
            return new RedirectResponse($this->generateUrl('ongr_settings_settings_add'));
        }

        $cache->save('settings_success', true);
        return new RedirectResponse($this->generateUrl('ongr_settings_settings_add'));
    }

    /**
     * Action for rendering single setting edit page.
     *
     * @param Request $request
     * @param string  $name
     * @param string  $profile
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function editAction(Request $request, $name, $profile)
    {
        $setting = $this->getSettingsManager()->get($name, $profile, false, $request->query->get('type', 'string'));
        $params = [];
        $params['setting'] = $setting;
        $cache = $this->get('es.cache_engine');
        if ($setting->getType() == 'object') {
            $dumper = new Dumper();
            $setting->setData(
                [
                    'value' => $dumper->dump(json_decode($setting->getData()['value'], true), 2)
                ]
            );
        }
        if ($cache->contains('settings_errors')) {
            $params['errors'] = $cache->fetch('settings_errors');
            $cache->delete('settings_errors');
        } elseif ($cache->contains('settings_success')) {
            $params['success'] = $cache->fetch('settings_success');
            $cache->delete('settings_success');
        }
        return $this->render(
            'ONGRSettingsBundle:Settings:edit.html.twig',
            $params
        );
    }

    /**
     * Action updating a setting.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function updateAction(Request $request)
    {
        $data = $this->get('ongr_settings.form_validator')->validateSettingForm($request);
        $manager = $this->getSettingsManager();
        $cache = $this->get('es.cache_engine');

        if ($data['error'] != '') {
            $cache->save('settings_errors', $data['error']);
            return new RedirectResponse(
                $this->generateUrl(
                    'ongr_settings_setting_edit',
                    ['name' => $data['name'], 'profile' => $data['profiles'][0]]
                )
            );
        }

        $model = $manager->get(
            $data['name'],
            $data['profiles'][0],
            false,
            $data['type']
        );

        $model->setData((object)['value' => $data['value']]);
        $model->setType($data['type']);
        $model->setDescription($data['description']);

        try {
            $manager->save([$model]);
        } catch (\Exception $e) {
            $cache->save('settings_errors', $e->getMessage());
            return new RedirectResponse(
                $this->generateUrl(
                    'ongr_settings_setting_edit',
                    ['name' => $data['name'], 'profile' => $data['profiles'][0]]
                )
            );
        }

        $cache->save('settings_success', true);
        return new RedirectResponse(
            $this->generateUrl(
                'ongr_settings_setting_edit',
                ['name' => $data['name'], 'profile' => $data['profiles'][0]]
            )
        );
    }

    /**
     * Action for deleting a setting.
     *
     * @param string $name
     * @param string $profile
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function removeAction($name, $profile)
    {
        $setting = $this->getSettingsManager()->get($name, $profile);

        $this->getSettingsManager()->remove($setting);

        return new RedirectResponse(
            $this->generateUrl(
                'ongr_settings_settings_list',
                ['profile' => $profile]
            )
        );
    }

    /**
     * Copies a setting to a new profile.
     *
     * @param Request $request
     * @param string $name
     * @param string $from
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function copyAction(Request $request, $name, $from)
    {
        $cache = $this->get('es.cache_engine');
        $profiles = $request->request->get('settingProfiles');

        if (!is_array($profiles)) {
            $cache->save('settings_errors', 'You must select at least one profile');
            return new RedirectResponse(
                $this->generateUrl(
                    'ongr_settings_settings_duplicate',
                    ['profile' => $from, 'name' => $name]
                )
            );
        }
        $existingProfiles = $this->get('ongr_settings.profiles_manager')
            ->getAllProfiles();
        foreach ($profiles as $profile) {
            if (!in_array($profile, array_column($existingProfiles, 'name'))) {
                $cache->save('settings_errors', 'Profile `'.$profile.'` does not match any existing profiles.`');
                return new RedirectResponse(
                    $this->generateUrl(
                        'ongr_settings_settings_duplicate',
                        ['profile' => $from, 'name' => $name]
                    )
                );
            }
        }

        $settingsManager = $this->getSettingsManager();

        $setting = $settingsManager->get($name, $from);

        $this->getSettingsManager()->duplicate($setting, $profiles);

        $cache->save('settings_success', true);
        return new RedirectResponse(
            $this->generateUrl(
                'ongr_settings_settings_duplicate',
                ['profile' => $from, 'name' => $name]
            )
        );
    }

    /**
     * @return SettingsManager
     */
    protected function getSettingsManager()
    {
        return $this->get('ongr_settings.settings_manager');
    }
}
