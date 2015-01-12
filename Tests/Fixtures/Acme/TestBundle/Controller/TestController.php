<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Fixtures\Acme\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for tests.
 */
class TestController extends Controller
{
    /**
     * Test personal settings action.
     *
     * @param Request $request
     *
     * @param string  $settingName
     * @param string  $needAuth
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function testPersonalSettingsAction(Request $request, $settingName, $needAuth)
    {
        return $this->render(
            'AcmeTestBundle:Test:personal.html.twig',
            ['setting_name' => $settingName, 'need_auth' => $needAuth]
        );

    }


    /**
     * Test general settings action.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function testgeneralsettingsAction(Request $request)
    {
        return $this->render(
            'AcmeTestBundle:Test:general.html.twig'
        );

    }
}
