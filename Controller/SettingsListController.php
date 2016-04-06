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

/**
 * Class SettingsListController. Is used for managing settings in General env.
 */
class SettingsListController extends Controller
{
    /**
     * Renders list page.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        return $this->render(
            'ONGRSettingsBundle:Settings:list.html.twig',
            array_merge(
                $this->getListData($request)
            )
        );
    }

    /**
     * Gets list data.
     *
     * @param Request $request
     *
     * @return array
     */
    protected function getListData(Request $request)
    {
        $filterManager = $this->get('ongr_settings.filter_manager')->handleRequest($request);

        return [
            'data' => iterator_to_array($filterManager->getResult()),
            'filters' => $filterManager->getFilters(),
            'routeParams' => $filterManager->getUrlParameters(),
        ];
    }
}
