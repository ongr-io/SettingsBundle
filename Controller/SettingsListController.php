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

use ONGR\ProductBundle\Service\FilteredList;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SettingsListController.
 *
 * @package ONGR\AdminBundle\Controller
 */
class SettingsListController extends Controller
{
    /**
     * Gets list data.
     *
     * @param Request $request
     *
     * @return array
     */
    protected function getListData(Request $request)
    {
        /** @var FilteredList $list */
        $list = $this->get('ongr_admin.browser.filteredList');
        $list->setRequest($request);

        return [
            'state' => $list->getStateLink(),
            'data' => iterator_to_array($list->getProducts()),
            'filters' => $list->getFiltersViewData(),
            'routeParams' => $list->getRouteParamsValues()
        ];
    }

    /**
     * Renders list page.
     *
     * @param Request   $request
     * @param string    $domain
     *
     * @return Response
     */
    public function listAction(Request $request, $domain = 'default')
    {
        return $this->render(
            "ONGRAdminBundle:Settings:list.html.twig",
            array_merge(
                $this->getListData($request),
                ['domain' => $domain]
            )
        );
    }
}
