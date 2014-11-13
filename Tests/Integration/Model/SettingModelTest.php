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

namespace Fox\AdminBundle\Tests\Integration\Model;

use Fox\AdminBundle\Model\SettingModel;
use Fox\AdminBundle\Tests\Integration\BaseTest;
use Fox\DDALBundle\Core\Query;

/**
 * Tests for setting model
 */
class SettingModelTest extends BaseTest
{
    /**
     * {@inheritDoc}
     */
    protected function getDocumentsData()
    {
        return [
            [
                'description' => 'test Description with the analyzer',
                'domain' => 'testDomain',
                'type' => SettingModel::TYPE_ARRAY,
                'data' => ['customData' => 'testData']
            ],
        ];
    }

    /**
     * Check if documents are saved correctly and if we can find them by indexed properties
     */
    public function testDocumentsSave()
    {
        $query = new Query();
        $query->filter->setShould('type', SettingModel::TYPE_ARRAY);

        /** @var SettingModel $setting */
        $setting = $this->sessionModel->getDocument($query);
        $this->assertEquals('test Description with the analyzer', $setting->description);
        $this->assertEquals('testDomain', $setting->domain);
        $this->assertEquals(SettingModel::TYPE_ARRAY, $setting->type);
        $this->assertEquals(['customData' => 'testData'], $setting->data);
    }
}
