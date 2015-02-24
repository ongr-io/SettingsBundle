<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Unit\Controller;

use ONGR\SettingsBundle\Controller\SettingsListController;
use ONGR\SettingsBundle\Tests\Fixtures\ElasticSearch\RepositoryTrait;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;

class SettingsListControllerTest extends \PHPUnit_Framework_TestCase
{
    use RepositoryTrait;

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

        $container->set('es.manager', $this->getRepositoryMock());
        $container->set('templating', $this->getTemplateEngine());

        $container->setParameter('ongr_settings.connection.repository', 'es.manager');

        $container->set('router', $this->getMock('Symfony\\Component\\Routing\\RouterInterface'));

        $controller = new SettingsListController();

        $controller->setContainer($container);
        $requestQuery = ['q' => 'testName'];
        $result = $controller->listAction(new Request($requestQuery));
        $this->assertArrayHasKey('data', $result);
        $this->assertEquals('testName', $result['filters']['search']->getState()->getValue());
    }

    /**
     * Returns manager instance with injected connection if does not exist creates new one.
     *
     * @return Manager
     */
    protected function getManagerWithRepositoryMock()
    {
        $managerMock = $this->getOrmManagerMock();

        $repositoryMock = $this->getOrmRepositoryMock();
        $repositoryMock->expects(
            $this->once()
        )->method('execute')
            ->willReturn($this->getDocumentIteratorMock());

        $managerMock->expects(
            $this->once()
        )->method('getRepository')
            ->willReturn($repositoryMock);

        return $managerMock;
    }

    /**
     * Returns repository mock.
     *
     * @return Manager
     */
    protected function getRepositoryMock()
    {
        $repositoryMock = $this->getOrmRepositoryMock();
        $repositoryMock->expects(
            $this->once()
        )->method('execute')
            ->willReturn($this->getDocumentIteratorMock());

        return $repositoryMock;
    }

    /**
     * Returns mock of Document Iterator.
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
}
