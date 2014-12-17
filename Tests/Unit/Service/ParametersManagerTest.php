<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Unit\Service;

use ONGR\AdminBundle\Document\Parameter;
use ONGR\AdminBundle\Service\ParametersManager;
use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\ElasticsearchBundle\ORM\Repository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Elasticsearch\Common\Exceptions\Missing404Exception;

class ParametersManagerTest extends WebTestCase
{
    /**
     * @var $ormManagerMock  Manager mock
     */
    private $ormManagerMock;

    /**
     * @var $ormRepositoryMock Repository mock
     */
    private $repositoryMock;

    /**
     * Set Up for test.
     */
    public function setUp()
    {
        $this->ormManagerMock = $this->getOrmManagerMock();
        $this->repositoryMock = $this->getOrmRepositoryMock();
    }

    /**
     * Test cases for validation of set method.
     *
     * @return array.
     */
    public function getDataSetForSet()
    {
        $cases = [];

        // Case No 1. String.
        $cases[] = [
            'key' => 'bar_name',
            'value' => 'test 15',
            'exception' => false,
        ];

        // Case No 2. Boolean.
        $cases[] = [
            'key' => 'bar_name',
            'value' => true,
            'exception' => false,
        ];

        // Case No 3. Array.
        $cases[] = [
            'key' => 'bar_name',
            'value' => ['value1', 'value2'],
            'exception' => false,
        ];

        // Case No 4. Object.
        $cases[] = [
            'key' => 'bar_name',
            'value' => new Parameter(),
            'exception' => false,
        ];

        // Case No 5. String with exception.
        $cases[] = [
            'key' => 'bar_name',
            'value' => 'test 15',
            'exception' => true,
        ];

        // Case No 6. Boolean with exception.
        $cases[] = [
            'key' => 'bar_name',
            'value' => true,
            'exception' => true,
        ];

        // Case No 7. Array with exception.
        $cases[] = [
            'key' => 'bar_name',
            'value' => ['value1', 'value2'],
            'exception' => true,
        ];

        // Case No 8. Object with exception.
        $cases[] = [
            'key' => 'bar_name',
            'value' => $this->getOrmRepositoryMock(),
            'exception' => true,
        ];

        return $cases;
    }

    /**
     * Test for set().
     *
     * @param string $key
     * @param string $value
     * @param bool   $exception
     *
     * @dataProvider getDataSetForSet
     */
    public function testSet($key, $value, $exception)
    {
        $par = new Parameter();
        $par->setId($key);
        $par->value = serialize($value);

        if ($exception) {
            $this->repositoryMock->expects(
                $this->once()
            )->method('find')
                ->will($this->throwException(new Missing404Exception));
        } else {
            $this->repositoryMock->expects(
                $this->once()
            )->method('find')
                ->willReturn($par);
        }

        $this->ormManagerMock->expects(
            $this->once()
        )->method('getRepository')
            ->willReturn($this->repositoryMock);

        $parameterManager = $this->getParametersManager($this->ormManagerMock);
        $this->assertEquals($par, $parameterManager->set($key, $value));
    }

    /**
     * Test for get().
     *
     * @param string $key
     * @param string $value
     * @param bool   $exception
     *
     * @dataProvider getDataSetForSet
     */
    public function testGet($key, $value, $exception)
    {
        if ($exception) {
            $this->repositoryMock->expects(
                $this->once()
            )->method('find')
                ->will($this->throwException(new Missing404Exception));
        } else {
            $par = new Parameter();
            $par->setId($key);
            $par->value = serialize($value);

            $this->repositoryMock->expects(
                $this->once()
            )->method('find')
                ->willReturn($par);
        }

        $this->ormManagerMock->expects(
            $this->once()
        )->method('getRepository')
            ->willReturn($this->repositoryMock);

        $parameterManager = $this->getParametersManager($this->ormManagerMock);

        $this->assertEquals(($exception ? null : $value), $parameterManager->get($key));
    }

    /**
     * Test for remove() .
     */
    public function testRemove()
    {
        $par = new Parameter();
        $par->setId('demo');
        $par->value = serialize('demo value');

        $ormManagerMock = $this->getOrmManagerMock();
        $repositoryMock = $this->getOrmRepositoryMock();

        $repositoryMock->expects(
            $this->once()
        )->method('find')
            ->willReturn($par);

        $ormManagerMock->expects(
            $this->once()
        )->method('getRepository')
            ->willReturn($repositoryMock);

        $parameterManager = $this->getParametersManager($ormManagerMock);
        $parameterManager->remove('demo');
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

    /**
     *  Returns mock of ORM repository.
     *
     * @return Repository
     */
    protected function getOrmRepositoryMock()
    {
        $mock = $this->getMock(
            'ONGR\ElasticsearchBundle\ORM\Repository',
            ['getBundlesMapping', 'find', 'remove'],
            [$this->getOrmManagerMock(), null ]
        );

        return $mock;
    }

    /**
     *  Returns mock of Parameter.
     *
     * @return Parameter
     */
    protected function getParameterMock()
    {
        return $this->getMock(
            'ONGR\AdminBundle\Document\Parameter'
        );
    }

    /**
     *  Returns mock of ParametersManager.
     *
     * @param object $ormManagerMock
     *
     * @return ParametersManager
     */
    protected function getParametersManager($ormManagerMock)
    {
        return new ParametersManager(
            $ormManagerMock
        );
    }
}
