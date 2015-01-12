<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Fixtures\Acme\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for tests.
 */
class TestController extends Controller
{
    /**
     * Test action.
     *
     * @param Request $request
     *
     * @param string  $settingName
     * @param string  $needAuth
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function testAction(Request $request, $settingName, $needAuth)
    {
        return $this->render(
            'AcmeTestBundle:Test:test.html.twig',
            ['setting_name' => $settingName, 'need_auth' => $needAuth]
        );
    }
}
