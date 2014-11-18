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
use ONGR\DDALBundle\Exception\DocumentNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class SettingsManagerController.
 *
 * @package ONGR\AdminBundle\Controller
 */
class SettingsManagerController extends Controller
{
    /**
     * @var SettingsManager
     */
    protected $settingsManager;

    /**
     * @return SettingsManager
     */
    protected function getSettingsManager()
    {
        if (is_null($this->settingsManager)) {
            /** @var SettingsManager settingsManager */
            $this->settingsManager = $this->get('ongr_admin.settings_manager');
        }

        return $this->settingsManager;
    }

    /**
     * Action for saving/seting setting values.
     *
     * @param Request   $request
     * @param string    $name
     * @param string    $domain
     *
     * @return Response
     */
    public function setSettingAction(Request $request, $name, $domain = 'default')
    {
        $value = json_decode($request->request->get("value"), true);

        if (json_last_error() != JSON_ERROR_NONE) {
            return new Response(Response::$statusTexts[406], 406); //Not Acceptable
        }

        $manager = $this->getSettingsManager();
        $manager->set($name, $value, $domain);

        return new Response();
    }

    /**
     * Action for rendering single setting edit page.
     *
     * @param Request $request
     * @param string  $name
     * @param string  $domain
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function editAction(Request $request, $name, $domain)
    {
        $setting = $this->getSettingsManager()->get($name, $domain, false, $request->query->get('type', 'string'));

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
     * @param Request   $request
     * @param string    $name
     * @param string    $domain
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function ngEditAction(Request $request, $name, $domain)
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
        $model = $manager->get($name, $domain, false, $type);

        $model->assign($content['setting']);
        $manager->save($model);

        return new Response();
    }

    /**
     * Action for deleting a setting.
     *
     * @param string  $name
     * @param string  $domain
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function removeAction($name, $domain)
    {
        try {
            $setting = $this->getSettingsManager()->get($name, $domain);
        } catch (DocumentNotFoundException $exception) {
            throw $this->createNotFoundException('Setting was not found.');
        }

        $this->getSettingsManager()->remove($setting);

        return new Response();
    }

    /**
     * Copies a setting to a new domain.
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
        try {
            $setting = $settingsManager->get($name, $from);
        } catch (DocumentNotFoundException $exception) {
            throw $this->createNotFoundException('Setting was not found.');
        }

        $this->getSettingsManager()->duplicate($setting, $to);

        return new Response();
    }
}
