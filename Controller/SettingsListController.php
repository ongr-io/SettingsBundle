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

        $manager = $this->get('es.manager');
        $repository = $manager->getRepository('ONGRAdminBundle:Settings');

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

        return [
            'state' => [],
            'data' => [],
            'filters' => [],//$fm->getFilters(),
            'routeParams' => [],//=> $fm->getUrlParameters() ,
        ];
    }

    /**
     * Returns item list.
     *
     * @param Request $request
     *
     * @return array
     */
    private function getProductsData($request)
    {
        //here we get our filter manager
       return $this->get('ongr_filter_manager.product_list')->execute($request);

//        $container = new FiltersContainer();
//        $manager = $this->get('es.manager');
//        $filterManager = new FiltersManager($container, $manager->getRepository('ONGRAdminBundle:Settings'));
//        return $filterManager->execute($request);
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
            'ONGRAdminBundle:Settings:list.html.twig',
            array_merge(
                $this->getListData($request),
                ['domain' => $domain]
            )
        );
    }


}
