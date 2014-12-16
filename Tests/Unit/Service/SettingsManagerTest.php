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

use ONGR\AdminBundle\Document\Setting;
use ONGR\AdminBundle\Service\SettingsManager;
use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\ElasticsearchBundle\ORM\Repository;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\Translator;

class SettingsManagerTest extends ElasticsearchTestCase
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
     * Tests if exception is being thrown.
     *
     * @expectedException \LogicException
     */
    public function testLogicException()
    {
        $this->ormManagerMock->expects(
            $this->once()
        )->method('getRepository')
        ->willReturn($this->repositoryMock);

        $settingsManager = $this->getSettingsManager($this->ormManagerMock);
        $testData = $this->getDataSetForSet();
        $settingsManager->get($testData[0]['name'], $testData[0]['profile']);
    }

    /**
     * Test for set().
     */
    public function testSet()
    {
        $dataSet = $this->getDataSetForSet();
        foreach ($dataSet as $item) {
            $this->ormManagerMock->expects(
                $this->any()
            )->method('getRepository')
                ->willReturn($this->repositoryMock);
            $settingsManager = $this->getSettingsManager($this->ormManagerMock);
            $settingsManager->set($item['name'], $item['profile']);
        }
    }

    /**
     * Test for get().
     */
    public function testGet()
    {
        $this->ormManagerMock->expects(
            $this->once()
        )->method('getRepository')
            ->willReturn($this->repositoryMock);

        $settingsManager = $this->getSettingsManager($this->ormManagerMock);
        $testData = $this->getDataSetForSet();

        // String type.
        $settingsManager->get($testData[0]['name'], $testData[0]['profile'], false);
        // Array type.
        $settingsManager->get($testData[0]['name'], $testData[0]['profile'], false, 'array');
    }

    /**
     * Test for save() .
     */
    public function testSave()
    {
        $ormManagerMock = $this->getOrmManagerMock();
        $repositoryMock = $this->getOrmRepositoryMock();

        $ormManagerMock->expects(
            $this->once()
        )->method('getRepository')
            ->willReturn($repositoryMock);

        $settingsManager = $this->getSettingsManager($ormManagerMock);
        $settingsManager->save($this->getSettingMock());
    }

    /**
     * Test for remove() .
     */
    public function testDuplicate()
    {
        $ormManagerMock = $this->getOrmManagerMock();
        $repositoryMock = $this->getOrmRepositoryMock();

        $ormManagerMock->expects(
            $this->once()
        )->method('getRepository')
            ->willReturn($repositoryMock);

        $settingsManager = $this->getSettingsManager($ormManagerMock);
        $settingsManager->duplicate($this->getSettingMock(), 'new_profile');
    }

    /**
     *  Returns mock of ORM Manager.
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
            ['getBundlesMapping'],
            [$this->getOrmManagerMock(), null ]
        );

        return $mock;
    }

    /**
     *  Returns mock of Setting.
     *
     * @return Setting
     */
    protected function getSettingMock()
    {
        return $this->getMock(
            'ONGR\AdminBundle\Document\Setting'
        );
    }

    /**
     *  Returns mock of SettingsManager.
     *
     * @param object $ormManagerMock
     *
     * @return SettingsManager
     */
    protected function getSettingsManager($ormManagerMock)
    {
        return new SettingsManager(
            new Translator('en'),
            $this->getEventDispatcherMock(),
            $ormManagerMock
        );
    }

    /**
     * Returns mock of event dispatcher.
     *
     * @return EventDispatcherInterface|MockObject
     */
    protected function getEventDispatcherMock()
    {
        return $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
    }

    /**
     * Test cases for validation of set method.
     *
     * @return array.
     */
    protected function getDataSetForSet()
    {
        $cases = [];

        // Case No 1. String.
        $cases[0] = ['name' => 'bar_name', 'profile' => 'foo_value'];

        // Case No 2. Boolean.
        $cases[1] = ['name' => 'bar_name', 'profile' => true];

        // Case No 3. Array.
        $cases[2] = ['name' => 'bar_name', 'profile' => ['value1', 'value2'] ];

        // Case No 3. Object.
        $cases[3] = ['name' => 'bar_name', 'profile' => $this->getOrmRepositoryMock() ];

        return $cases;
    }
}
