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

namespace Fox\AdminBundle\Tests\Integration\Service;

use Fox\AdminBundle\Model\SettingModel;
use Fox\AdminBundle\Service\SettingsManager;
use Fox\AdminBundle\Tests\Integration\BaseTest;
use Fox\DDALBundle\Core\Query;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Tests for SessionModelAwareProvider
 */
class SettingsManagerTest extends BaseTest
{
    /**
     * Creates setting model
     *
     * @param string $name
     * @param string $type
     * @param mixed $value
     * @param string $domain
     *
     * @return SettingModel
     */
    private function getSettingModel($name, $type, $value, $domain = 'default')
    {
        $model = new SettingModel();
        $model->setDocumentId($domain . '_' . $name);
        $model->assign([
            'name' => $name,
            'description' => 'fox_admin.' . $name,
            'data' => ['value' => $value],
            'type' => $type,
            'domain' => $domain
        ]);
        return $model;
    }

    /**
     * {@inheritDoc}
     */
    protected function getDocumentsData()
    {
        return [
            [
                '_id' => 'default_name0',
                'name' => 'name0',
                'description' => 'this should be updated',
                'domain' => 'default',
                'type' => SettingModel::TYPE_STRING,
                'data' => (object)['value' => 'test1']
            ]
        ];
    }

    /**
     * data provider for testSet
     *
     * @return array
     */
    public function getSettingsData()
    {
        $out = [];

        //case #0: update
        $expected0 = $this->getSettingModel('name0', 'string', 'value0');
        $out[] = [$expected0, 'name0', 'value0'];

        //case #1: new setting
        $expected1 = $this->getSettingModel('name1', 'array', ['key' => 'value']);
        $out[] = [$expected1, 'name1', ['key' => 'value']];

        return $out;
    }

    /**
     * tests the method set
     *
     * @param SettingModel $expected
     * @param string $name
     * @param string|array $value
     * @param string $domain
     *
     * @dataProvider getSettingsData
     */
    public function testGetSettings($expected, $name, $value, $domain = 'default')
    {
        /** @var TranslatorInterface $translator */
        $translator = $this->container->get('translator');

        $manager = new SettingsManager($translator, $this->container->get('event_dispatcher'));
        $manager->setSessionModel($this->sessionModel);
        $manager->set($name, $value, $domain);

        /** @var SettingModel $doc */
        $doc = $this->sessionModel->getDocumentById($domain . '_' . $name);
        $this->assertEquals($expected, $doc);
    }

    /**
     * Tests duplicate method
     */
    public function testDuplicate()
    {
        /** @var TranslatorInterface $translator */
        $translator = $this->container->get('translator');

        $manager = new SettingsManager($translator, $this->container->get('event_dispatcher'));
        $manager->setSessionModel($this->sessionModel);

        $settingToCopy = $manager->get('name0', 'default');
        $settingToCopy->setDocumentScore(1.0);
        $settingToCopy->setModelName('SettingModel');

        $manager->duplicate($settingToCopy, 'newDomain');

        $documents = $this->sessionModel->findDocuments(new Query());

        $expectedCreated = clone $settingToCopy;
        $expectedCreated->setDocumentId('newDomain_name0');
        $expectedCreated->domain = 'newDomain';

        $actual = iterator_to_array($documents);
        $expected = [$settingToCopy, $expectedCreated];

        sort($actual);
        sort($expected);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Public function test removing a setting
     *
     * @expectedException \Fox\DDALBundle\Exception\DocumentNotFoundException
     */
    public function testRemove()
    {
        /** @var TranslatorInterface $translator */
        $translator = $this->container->get('translator');

        $manager = new SettingsManager($translator, $this->container->get('event_dispatcher'));
        $manager->setSessionModel($this->sessionModel);
        $setting = $manager->get('name0', 'default');

        $this->assertInstanceOf('Fox\AdminBundle\Model\SettingModel', $setting);

        $manager->remove($setting);

        $manager->get('name0', 'default');
    }
}
