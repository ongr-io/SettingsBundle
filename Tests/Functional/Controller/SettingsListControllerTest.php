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

namespace ONGR\AdminBundle\Tests\Functional\Controller;

use ONGR\AdminBundle\Controller\SettingsListController;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;

class SettingsListControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|EngineInterface
     */
    public function getTemplateEngine()
    {
        $template = $this->getMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $template->expects($this->any())->method('renderResponse')->will($this->returnArgument(1));
        return $template;
    }

    /**
     * test for list action
     */
    public function testListAction()
    {
        $container = new ContainerBuilder();

        $list = $this
            ->getMockBuilder('Fox\ProductBundle\Service\FilteredList')
            ->disableOriginalConstructor()
            ->getMock();
        $list
            ->expects($this->once())
            ->method('setRequest')
            ->with(new Request(['a' => 1]));
        $list
            ->expects($this->once())
            ->method('getProducts')
            ->will($this->returnValue(new \ArrayIterator([1, 2, 3])));
        $list
            ->expects($this->once())
            ->method('getFiltersViewData')
            ->will($this->returnValue(['filters', 'view', 'data']));
        $list
            ->expects($this->once())
            ->method('getStateLink')
            ->will($this->returnValue(['link']));
        $list
            ->expects($this->once())
            ->method('getRouteParamsValues')
            ->will($this->returnValue(['route', 'params']));
        $container->set('ongr_admin.browser.filteredList', $list);

        $container->set('templating', $this->getTemplateEngine());
        $container->set('router', $this->getMock('Symfony\\Component\\Routing\\RouterInterface'));

        $controller = new SettingsListController();
        $controller->setContainer($container);

        $this->assertEquals(
            [
                'state' => ['link'],
                'data' => [1, 2, 3],
                'filters' => ['filters', 'view', 'data'],
                'routeParams' => ['route', 'params'],
                'domain' => 'default',
            ],
            $controller->listAction(new Request(['a' => 1]))
        );
    }
}
