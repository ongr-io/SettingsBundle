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
use ONGR\ElasticsearchBundle\ORM\Manager;
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

    /**
     * Returns manager instance with injected connection if does not exist creates new one.
     *
     * @return Manager
     */
    protected function getManager()
    {
        $managerMock = $this->getMockBuilder('ONGR\ElasticsearchBundle\ORM\Manager')
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock = $this->getOrmRepositoryMock();
        $managerMock->expects(
            $this->once()
        )->method('getRepository')
            ->willReturn($repositoryMock);

        return $managerMock;
    }

    /**
     * Returns mock of ORM repository.
     *
     * @return Repository
     */
    protected function getOrmRepositoryMock()
    {
        $mock = $this->getMock(
            'ONGR\ElasticsearchBundle\ORM\Repository',
            ['getBundlesMapping', 'find', 'remove', 'execute'],
            [$this->getOrmManagerMock(), ['ONGRAdminBundle:Setting'] ]
        );

        $mock->expects(
            $this->once()
        )->method('execute')
            ->willReturn($this->getDocumentIteratorMock());

        return $mock;
    }

    /**
     * Returns mock of ORM Manager.
     *
     * @return Manager
     */
    protected function getDocumentIteratorMock()
    {
        return $this->getMock(
            'ONGR\ElasticsearchBundle\Result\DocumentIterator',
            null,
            [ null, null, [], [] ]
        );
    }

    /**
     * Returns mock of ORM Manager.
     *
     * @return Manager
     */
    protected function getOrmManagerMock()
    {
        return $this->getMock(
            'ONGR\ElasticsearchBundle\ORM\Manager',
            ['getRepository', 'persist', 'commit', 'flush', 'refresh'],
            [ null, null, [], [] ]
        );
    }
}
