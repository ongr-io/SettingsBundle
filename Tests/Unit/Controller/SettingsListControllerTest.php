<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Unit\Controller;

use ONGR\AdminBundle\Controller\SettingsListController;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;

class SettingsListControllerTest extends ElasticsearchTestCase
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
     * Test for list action.
     */
    public function testListAction()
    {
        $container = new ContainerBuilder();
        $container->set('es.manager', $this->getManager());

        $container->set('templating', $this->getTemplateEngine());
        $container->set('router', $this->getMock('Symfony\\Component\\Routing\\RouterInterface'));

        $controller = new SettingsListController();
        $controller->setContainer($container);
        $this->assertArrayHasKey('data', $controller->listAction(new Request()));
    }
}
