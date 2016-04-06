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
class SettingsAddController extends Controller
{
    /**
     * Renders add setting form.
     *
     * @return Response
     */
    public function displayAction()
    {
        return $this->render(
            'ONGRSettingsBundle:Settings:add.html.twig'
        );
    }
}