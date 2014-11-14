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

namespace ONGR\AdminBundle\Tests\Functional\Service;

use ONGR\AdminBundle\Model\SettingModel;
use ONGR\AdminBundle\Service\SettingsManager;
use Fox\DDALBundle\Exception\DocumentNotFoundException;
use Fox\DDALBundle\Session\SessionModelInterface;
use Symfony\Component\Translation\Translator;

class SettingsManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates setting model
     *
     * @param string $name
     * @param mixed $value
     * @param string $type
     * @param string $domain
     *
     * @return SettingModel
     */
    private function getSettingModel($name, $value, $type, $domain = 'default')
    {
        $model = new SettingModel();
        $model->setDocumentId($domain . '_' . $name);
        $model->assign([
            'name' => $name,
            'description' => 'ongr_admin.' . $name,
            'data' => (object)['value' => $value],
            'type' => $type,
            'domain' => $domain
        ]);
        return $model;
    }

    /**
     * Returns mock of event dispatcher
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEventDispatcherMock()
    {
        return $this->getMock('Symfony\\Component\\EventDispatcher\\EventDispatcherInterface');
    }

    /**
     * data provider for set test
     *
     * @return array
     */
    public function getTestSetData()
    {
        $out = [];

        //case #0, type string
        $expected = $this->getSettingModel('name0', 'value0', SettingModel::TYPE_STRING);
        $out[] = [$expected, 'name0', 'value0'];

        // #1 type array
        $expected = $this->getSettingModel('name1', ['value1'], SettingModel::TYPE_ARRAY);
        $out[] = [$expected, 'name1', ['value1']];

        // #2 type boolean
        $expected = $this->getSettingModel('name2', true, SettingModel::TYPE_BOOLEAN);
        $out[] = [$expected, 'name2', true];

        // #3 type object
        $obj = json_decode(json_encode(['foo' => 'test', 'baz' => ['text1', 'text2']]), false);
        $expected = $this->getSettingModel('name2', $obj, SettingModel::TYPE_OBJECT);
        $out[] = [$expected, 'name2', $obj];

        // #4 unknown type
        $expected = $this->getSettingModel('name3', 3, SettingModel::TYPE_STRING);
        $out[] = [$expected, 'name3', 3];

        // #5 custom domain
        $expected = $this->getSettingModel('name4', 'value4', SettingModel::TYPE_STRING, 'custom');
        $out[] = [$expected, 'name4', 'value4', 'custom'];

        return $out;
    }

    /**
     * tests if set method is working as expected
     *
     * @param SettingModel $expected
     * @param string $name
     * @param string|array $value
     * @param string $domain
     *
     * @dataProvider getTestSetData
     */
    public function testSet($expected, $name, $value, $domain = 'default')
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|SessionModelInterface $sessionModel */
        $sessionModel = $this->getMock('Fox\DDALBundle\Session\SessionModelInterface');

        $sessionModel->expects($this->once())->method('saveDocument')->with($expected);
        $sessionModel->expects($this->once())->method('flush');

        $manager = new SettingsManager(new Translator('en'), $this->getEventDispatcherMock());
        $manager->setSessionModel($sessionModel);
        $manager->set($name, $value, $domain);
    }

    /**
     * tests if exception is being thrown
     *
     * @expectedException \LogicException
     */
    public function testSessionModelException()
    {
        $manager = new SettingsManager(new Translator('en'), $this->getEventDispatcherMock());
        $manager->set('name', 'value');
    }

    /**
     * Test for get()
     */
    public function testGet()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|SessionModelInterface $sessionModel */
        $sessionModel = $this->getMock('Fox\DDALBundle\Session\SessionModelInterface');
        $sessionModel->expects($this->once())->method('getDocumentById')->with('foo_domain_bar_name');

        $manager = new SettingsManager(new Translator('en'), $this->getEventDispatcherMock());
        $manager->setSessionModel($sessionModel);

        $manager->get('bar_name', 'foo_domain');
    }

    /**
     * Data provider for testGetNew()
     *
     * @return array
     */
    public function getTestGetNewData()
    {
        return [
            ['string', null],
            ['array', []],
        ];
    }

    /**
     * Test for get() in case document does not exist
     *
     * @param $type
     * @param $defaultValue
     *
     * @dataProvider getTestGetNewData()
     */
    public function testGetNew($type, $defaultValue)
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|SessionModelInterface $sessionModel */
        $sessionModel = $this->getMock('Fox\DDALBundle\Session\SessionModelInterface');
        $sessionModel->expects($this->once())->method('getDocumentById')->willThrowException(
            new DocumentNotFoundException()
        );

        $manager = new SettingsManager(new Translator('en'), $this->getEventDispatcherMock());
        $manager->setSessionModel($sessionModel);

        $model = $manager->get('bar_name', 'foo_domain', false, $type);
        $this->assertInstanceOf('\ONGR\AdminBundle\Model\SettingModel', $model);
        $this->assertEquals($type, $model->type);
        $this->assertEquals($defaultValue, $model->data['value']);
    }

    /**
     * Test for save()
     */
    public function testSave()
    {
        $model = $this->getSettingModel('name0', 'value0', SettingModel::TYPE_STRING);

        /** @var \PHPUnit_Framework_MockObject_MockObject|SessionModelInterface $sessionModel */
        $sessionModel = $this->getMock('Fox\DDALBundle\Session\SessionModelInterface');

        $sessionModel->expects($this->once())->method('saveDocument')->with($model);
        $sessionModel->expects($this->once())->method('flush');

        $eventDispatcher = $this->getEventDispatcherMock();
        $eventDispatcher->expects($this->once())->method('dispatch');

        $manager = new SettingsManager(new Translator('en'), $eventDispatcher);
        $manager->setSessionModel($sessionModel);
        $manager->save($model);
    }
}
