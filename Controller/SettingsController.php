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

use ONGR\ElasticsearchBundle\Service\Repository;
use ONGR\SettingsBundle\Document\Setting;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SettingsListController. Is used for managing settings in General env.
 */
class SettingsController extends Controller
{
    /**
     * Renders settings list page.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        return $this->render(
            'ONGRSettingsBundle:Settings:list.html.twig',
            []
        );
    }

    /**
     * Setting update action.
     *
     * @param Request $request
     * @param $id
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request, $id)
    {
        try {
            /** @var Repository $repo */
            $repo = $this->get($this->getParameter('ongr_settings.repo'));

            /** @var Setting $setting */
            $setting = $repo->find($id);
            $setting->setValue($request->get('value'));

            $em = $repo->getManager();
            $em->persist($setting);
            $em->commit();

            return new JsonResponse(['error' => false]);

        } catch (\Exception $e) {
            return new JsonResponse(
                [
                    'error' => true,
                    'message' => 'Error occurred please try to update setting again.'
                ]
            );
        }
    }
}
