<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Unit\PairStorage;

use ONGR\SettingsBundle\Document\Pair;
use ONGR\SettingsBundle\PairStorage\PairStorage;
use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\ElasticsearchBundle\ORM\Repository;
use Elasticsearch\Common\Exceptions\Missing404Exception;

class PairStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $ormManagerMock Manager mock.
     */
    private $ormManagerMock;

    /**
     * @var $ormRepositoryMock Repository mock.
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
        $pair = new Pair();
        $pair->setId($key);
        $pair->setValue($value);

        if ($exception) {
            $this->repositoryMock->expects(
                $this->once()
            )->method('find')
                ->will($this->throwException(new Missing404Exception));
        } else {
            $this->repositoryMock->expects(
                $this->once()
            )->method('find')
                ->willReturn($pair);
        }

        $this->ormManagerMock->expects(
            $this->once()
        )->method('getRepository')
            ->willReturn($this->repositoryMock);

        $pairStorage = $this->getPairStorage($this->ormManagerMock);
        $this->assertEquals($pair, $pairStorage->set($key, $value));
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
            $pair = new Pair();
            $pair->setId($key);
            $pair->setValue($value);

            $this->repositoryMock->expects(
                $this->once()
            )->method('find')
                ->willReturn($pair);
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
        $pair = new Pair();
        $pair->setId('demo');
        $pair->setValue('demo value');

        $ormManagerMock = $this->getOrmManagerMock();
        $repositoryMock = $this->getOrmRepositoryMock();

        $repositoryMock->expects(
            $this->once()
        )->method('find')
            ->willReturn($pair);

        $repositoryMock->expects(
            $this->once()
        )->method('remove')
            ->with($pair->getId());

        $ormManagerMock->expects(
            $this->once()
        )->method('getRepository')
            ->willReturn($repositoryMock);

        $ormManagerMock->expects(
            $this->once()
        )->method('flush');

        $ormManagerMock->expects(
            $this->once()
        )->method('refresh');

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
     * Returns mock of ORM repository.
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
            'ONGR\SettingsBundle\Document\Pair'
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
