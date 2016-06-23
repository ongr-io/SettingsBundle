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

use ONGR\ElasticsearchBundle\Result\Aggregation\AggregationValue;
use ONGR\ElasticsearchBundle\Service\Repository;
use ONGR\ElasticsearchDSL\Aggregation\TermsAggregation;
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
        /** @var Repository $repo */
        $repo = $this->get($this->getParameter('ongr_settings.repo'));
        $className = $repo->getClassName();
        $form = $this->createForm($this->getParameter('ongr_settings.type.setting.class'), new $className);

        return $this->render(
            'ONGRSettingsBundle:Settings:list.html.twig',
            [
                'form' => $form->createView(),
            ]
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

            $form = $this->createForm($this->getParameter('ongr_settings.type.setting.class'), $setting);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $repo->getManager();
                $em->persist($setting);
                $em->commit();
            } else {
                return new JsonResponse(
                    [
                        'error' => true,
                        'message' => 'Not valid posted data.'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return new JsonResponse(
                [
                    'error' => false
                ]
            );

        } catch (\Exception $e) {
            return new JsonResponse(
                [
                    'error' => true,
                    'message' => 'Error occurred please try to update setting again.'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Setting update action.
     *
     * @param Request $request
     * @param $id
     *
     * @return JsonResponse
     */
    public function updateValueAction(Request $request, $id)
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

            return new JsonResponse(
                [
                    'error' => false
                ]
            );

        } catch (\Exception $e) {
            return new JsonResponse(
                [
                    'error' => true,
                    'message' => 'Error occurred please try to update setting again.'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Setting delete action
     *
     * @param Request $request
     * @param $id
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, $id)
    {
        try {
            /** @var Repository $repo */
            $repo = $this->get($this->getParameter('ongr_settings.repo'));

            $repo->remove($id);

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
     * Setting delete action
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createAction(Request $request)
    {
        try {
            /** @var Repository $repo */
            $repo = $this->get($this->getParameter('ongr_settings.repo'));
            $manager = $repo->getManager();

            $setting = new Setting();
            $setting->setName($request->get('setting_name'));

            $manager->persist($setting);
            $manager->commit();

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
}
