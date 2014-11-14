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

namespace ONGR\AdminBundle\Tests\Integration\Settings;

use ONGR\AdminBundle\Model\SettingModel;
use ONGR\AdminBundle\Settings\Provider\SessionModelAwareProvider;
use ONGR\AdminBundle\Settings\SettingsContainer;
use ONGR\AdminBundle\Tests\Integration\BaseTest;
use Stash\Pool;

class SettingsContainerTest extends BaseTest
{
    /**
     * {@inheritDoc}
     */
    protected function getDocumentsData()
    {
        return [
            [
                '_id' => 'default_test',
                'name' => 'test',
                'description' => 'test item #1',
                'domain' => 'default',
                'type' => SettingModel::TYPE_ARRAY,
                'data' => ['value' => 'test1']
            ],
            [
                '_id' => 'default_test2',
                'name' => 'test2',
                'description' => 'test item #2',
                'domain' => 'default',
                'type' => SettingModel::TYPE_ARRAY,
                'data' => ['value' => 'test2']
            ],
        ];
    }

    /**
     * Test for SettingsContainer get
     */
    public function testGet()
    {
        $pool = new Pool();
        $settingsContainer = new SettingsContainer($pool);
        $provider = new SessionModelAwareProvider();
        $provider->setSessionModel($this->sessionModel);
        $settingsContainer->addProvider($provider);

        // first time, it should be loaded from database
        $settings = $settingsContainer->get('test');
        $this->assertEquals('test1', $settings);

        // second time it should be loaded from object itself
        $this->session && $this->session->dropRepository();
        $settings = $settingsContainer->get('test');
        $this->assertEquals('test1', $settings);
        $settings = $settingsContainer->get('test2');
        $this->assertEquals('test2', $settings);
    }
}
