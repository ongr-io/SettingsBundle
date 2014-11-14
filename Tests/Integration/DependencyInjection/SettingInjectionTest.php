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

namespace ONGR\AdminBundle\Tests\Integration\DependencyInjection;

use ONGR\AdminBundle\Model\SettingModel;
use ONGR\AdminBundle\Tests\Integration\BaseTest;

class SettingInjectionTest extends BaseTest
{
    /**
     * {@inheritDoc}
     */
    protected function getDocumentsData()
    {
        return [
            [
                '_id' => 'default_setting_1',
                'name' => 'setting_1',
                'description' => 'test item #1',
                'domain' => 'default',
                'type' => SettingModel::TYPE_ARRAY,
                'data' => ['value' => 'test1']
            ],
        ];
    }

    /**
     * Test for settings injection into services
     */
    public function testSettingInjection()
    {
        /** @var DummyService $dummyService */
        $dummyService = $this->container->get('ongr_admin.dummy_service');

        $expectedValue = $this->getDocumentsData()[0]['data']['value'];
        $this->assertEquals($expectedValue, $dummyService->getSetting1());
    }
}
