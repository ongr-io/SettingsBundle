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

use ONGR\AdminBundle\Document\Pair;
use ONGR\AdminBundle\Service\PairStorage;
use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\ElasticsearchBundle\ORM\Repository;
use Elasticsearch\Common\Exceptions\Missing404Exception;

class PairStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $ormManagerMock Manager mock
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
            'value' => new Pair(),
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
        $par = new Pair();
        $par->setId($key);
        $par->setValue(serialize($value));

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

        $pairStorage = $this->getPairStorage($this->ormManagerMock);
        $this->assertEquals($par, $pairStorage->set($key, $value));
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
            $par = new Pair();
            $par->setId($key);
            $par->setValue(serialize($value));

            $this->repositoryMock->expects(
                $this->once()
            )->method('find')
                ->willReturn($par);
        }

        $this->ormManagerMock->expects(
            $this->once()
        )->method('getRepository')
            ->willReturn($this->repositoryMock);

        $pairStorage = $this->getPairStorage($this->ormManagerMock);

        $this->assertEquals(($exception ? null : $value), $pairStorage->get($key));
    }

    /**
     * Test for remove().
     */
    public function testRemove()
    {
        $par = new Pair();
        $par->setId('demo');
        $par->setValue(serialize('demo value'));

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

        $pairStorage = $this->getPairStorage($ormManagerMock);
        $pairStorage->remove('demo');
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
     * Returns mock of Pair.
     *
     * @return Pair
     */
    protected function getPairMock()
    {
        return $this->getMock(
            'ONGR\AdminBundle\Document\Pair'
        );
    }

    /**
     * Returns mock of PairStorage.
     *
     * @param object $ormManagerMock
     *
     * @return PairStorage
     */
    protected function getPairStorage($ormManagerMock)
    {
        return new PairStorage(
            $ormManagerMock
        );
    }
}
