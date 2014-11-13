<?php

/*
 *************************************************************************
 * NFQ eXtremes CONFIDENTIAL
 * [2013] - [2014] NFQ eXtremes UAB
 * All Rights Reserved.
 *************************************************************************
 * NOTICE: 
 * All information contained herein is, and remains the property of NFQ eXtremes UAB.
 * Dissemination of this information or reproduction of this material is strictly forbidden
 * unless prior written permission is obtained from NFQ eXtremes UAB.
 *************************************************************************
 */

namespace Fox\AdminBundle\Controller;

use Fox\ProductBundle\Service\FilteredList;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SettingsListController extends Controller
{
    /**
     * @param Request $request
     * @return array
     */
    protected function getListData(Request $request)
    {
        /** @var FilteredList $list */
        $list = $this->get('fox_admin.browser.filteredList');
        $list->setRequest($request);

        return [
            'state' => $list->getStateLink(),
            'data' => iterator_to_array($list->getProducts()),
            'filters' => $list->getFiltersViewData(),
            'routeParams' => $list->getRouteParamsValues()
        ];
    }

    /**
     * renders list page
     *
     * @param Request $request
     * @param string $domain
     *
     * @return Response
     */
    public function listAction(Request $request, $domain = 'default')
    {
        return $this->render(
            "FoxAdminBundle:Settings:list.html.twig",
            array_merge(
                $this->getListData($request),
                ['domain' => $domain]
            )
        );
    }
}
