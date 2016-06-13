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
class SettingsController extends Controller
{
    /**
     * Renders list page.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function homeAction(Request $request)
    {
        return $this->render(
            'ONGRSettingsBundle:Settings:home.html.twig',
            []
        );
    }

    /**
     * Renders list page.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function allSettingsListAction(Request $request)
    {
//        $fm = $this->get('ongr')
        return $this->render(
            'ONGRSettingsBundle:Settings:list.html.twig',
            []
        );
    }
}
