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

use ONGR\AdminBundle\Service\SettingsManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * SettingsManager controller responsible for CRUD actions from frontend for settings.
 *
 * @package ONGR\AdminBundle\Controller
 */
class SettingsManagerController extends Controller
{
    /**
     * Action for saving/seting setting values.
     *
     * @param Request $request
     * @param string  $name
     * @param string  $profile
     *
     * @return Response
     */
    public function setSettingAction(Request $request, $name, $profile = 'default')
    {
        $value = json_decode($request->request->get('value'), true);

        if (json_last_error() != JSON_ERROR_NONE) {
            // Not Acceptable.
            return new Response(Response::$statusTexts[406], 406);
        }

        $manager = $this->getSettingsManager();
        $manager->set($name, $value, $profile);

        return new Response();
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

        return $this->render(
            'ONGRAdminBundle:Settings:edit.html.twig',
            [
                'setting' => $setting,
            ]
        );
    }

    /**
     * Action for Angularjs to edit settings.
     *
     * @param Request $request
     * @param string  $name
     * @param string  $profile
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function ngEditAction(Request $request, $name, $profile)
    {
        $content = $request->getContent();
        if (empty($content)) {
            return new Response(Response::$statusTexts[400], 400);
        }

        $content = json_decode($content, true);
        if ($content === null || empty($content['setting'])) {
            return new Response(Response::$statusTexts[400], 400);
        }

        $type = isset($content['setting']['type']) ? $content['setting']['type'] : 'string';

        $manager = $this->getSettingsManager();
        $model = $manager->get($name, $profile, false, $type);

        $model->data = $content['setting']['data'];
        $manager->save($model);

        return new Response();
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

        return new Response();
    }

    /**
     * Copies a setting to a new profile.
     *
     * @param string $name
     * @param string $from
     * @param string $to
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function copyAction($name, $from, $to)
    {
        $settingsManager = $this->getSettingsManager();

        $setting = $settingsManager->get($name, $from);

        $this->getSettingsManager()->duplicate($setting, $to);

        return new Response();
    }

    /**
     * @return SettingsManager
     */
    protected function getSettingsManager()
    {
        return $this->get('ongr_admin.settings_manager');
    }
}
