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

use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\FilterManagerBundle\Filters\ViewData;
use ONGR\FilterManagerBundle\Filters\Widget\Choice\SingleTermChoice;
use ONGR\FilterManagerBundle\Filters\Widget\Pager\Pager;
use ONGR\FilterManagerBundle\Filters\Widget\Search\MatchSearch;
use ONGR\FilterManagerBundle\Filters\Widget\Sort\Sort;
use ONGR\FilterManagerBundle\Search\FiltersContainer;
use ONGR\FilterManagerBundle\Search\FiltersManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SettingsListController. Is used for managing settings in Admin env.
 *
 * @package ONGR\AdminBundle\Controller
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
            'ONGRAdminBundle:Settings:list.html.twig',
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

        /** @var Manager $manager */
        $manager = $this->container->get('es.manager');

        /** @var FiltersContainer $container */
        $container = new FiltersContainer();

        /** @var Pager $pager */
        $pager = new Pager();
        $pager->setRequestField('page');
        $pager->setCountPerPage(15);
        $container->set('pager', $pager);

        /** @var Sort $sort */
        $sort = new Sort();
        $sort->setRequestField('sort');
        $choices = [
            'nameAsc' => ['label' => 'Name Asc', 'field' => 'name', 'order' => 'asc', 'default' => true],
            'nameDesc' => ['label' => 'Name Desc', 'field' => 'name', 'order' => 'desc', 'default' => false],
        ];
        $sort->setChoices($choices);
        $container->set('sort', $sort);

        /** @var MatchSearch $search */
        $search = new MatchSearch();
        $search->setRequestField('q');
        $search->setField('name');
        $container->set('search', $search);

        /** @var SingleTermChoice $profile */
        $profile = new SingleTermChoice();
        $profile->setRequestField('profile');
        $profile->setField('profile');
        $container->set('profile', $profile);

        /** @var FiltersManager $fm */
        $fm = new FiltersManager($container, $manager->getRepository('ONGRAdminBundle:Setting'));
        //var_dump($fm); exit;
        $fmr = $fm->execute($request);

        return [
            'data' => iterator_to_array($fmr->getResult()),
            'filters' => $fmr->getFilters(),
            'routeParams' => $fmr->getUrlParameters(),
        ];
    }


}
