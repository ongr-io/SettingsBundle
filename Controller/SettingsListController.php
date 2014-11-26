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

//use ONGR\ProductBundle\Service\FilteredList;
use ONGR\FilterManagerBundle\ONGRFilterManagerBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use ONGR\AdminBundle\Document\Settings;

use ONGR\FilterManagerBundle\Filters\ViewData;
use ONGR\FilterManagerBundle\Search\FiltersContainer;
use ONGR\FilterManagerBundle\Search\FiltersManager;

/**
 * Class SettingsListController. Is used for managing settings in Admin env.
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

//        $content = new Settings();
//        $content->key = "first";
//        $content->value = "FIRST test VAL";
//        $manager->persist($content); //adds to bulk container
//        $manager->commit(); //bulk data to index and fs
//        var_dump($manager);



//        try {
//            //$document = $repository->find('MseS7rmiQq6t6-6pOxpHcQ');
//            $document = $repository->findBy(['key'=>'first']);
//            var_dump($document);
//        } catch(\Exception $e) {
//            var_dump($e->getMessage());
//        }
//        exit;

        ///** @var FilteredList $list */
        //TODO: rewrite this according to https://github.com/ongr-io/FilterManagerBundle/blob/master/Resources/doc/usage.md
        //        $list = $this->get('ongr_admin.browser.filteredList');
        //        $list->setRequest($request);
        //
        //        return [
        //            'state' => $list->getStateLink(),
        //            'data' => iterator_to_array($list->getProducts()),
        //            'filters' => $list->getFiltersViewData(),
        //            'routeParams' => $list->getRouteParamsValues()
        //        ];

//        $fm = $this->getProductsData($request);
//        var_dump( $fm, get_class_methods($fm) );
//        var_dump( $fm->getUrlParameters() );
//        exit;



        /** @var ONGR\FilterManagerBundle\FilterManager $fm */
//        $fm = $this->get('ongr_filter_manager.product_list')->execute($request);
//        var_dump($fm);
//        var_dump($this->get('ongr_admin.settings_container')->getDomains());
        var_dump($this->get('ongr_admin.settings_container')->getDomains());
        var_dump($this->get('ongr_admin.domains_manager')->getDomains());

//            $manager = $this->get('es.manager');
//        var_dump(get_class($manager));


        exit;
//        var_dump($fm->getFilters()["search"]->getState()->getUrlParameters() ); echo '<hr/>';
//        var_dump($fm->getFilters()); echo '<hr/>';
//        var_dump($this->getFilterManagerResponse($request, $managerName)); echo '<hr/>';
//        exit;        return $this->render(
//        $template,
//        $this->getFilterManagerResponse($request, $managerName);

        return [
            'state' => [],
            'data' => [],
            'filters' => $fm->getFilters(),
            'routeParams' => [],//=> $fm->getUrlParameters() ,
        ];
    }

    /**
     * Renders list page.
     *
     * @param Request   $request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        return $this->render(
            'ONGRAdminBundle:Settings:list.html.twig',
            array_merge(
                $this->getListData($request)
            )
        );
    }


}
