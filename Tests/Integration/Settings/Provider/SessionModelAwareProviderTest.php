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

namespace Fox\AdminBundle\Tests\Integration\Settings\Provider;

use Fox\AdminBundle\Model\SettingModel;
use Fox\AdminBundle\Settings\Provider\SessionModelAwareProvider;
use Fox\AdminBundle\Tests\Integration\BaseTest;

/**
 * Tests for SessionModelAwareProvider
 */
class SessionModelAwareProviderTest extends BaseTest
{
    /**
     * {@inheritDoc}
     */
    protected function getDocumentsData()
    {
        return [
            [
                '_id' => 'default_foo',
                'name' => 'foo',
                'description' => 'this should be the one we get',
                'domain' => 'default',
                'type' => SettingModel::TYPE_ARRAY,
                'data' => ['value' => 'test1']
            ],
            [
                '_id' => 'default_bar',
                'name' => 'bar',
                'description' => 'we use the same data value on this one to test limit',
                'domain' => 'default',
                'type' => SettingModel::TYPE_ARRAY,
                'data' => ['value' => 'test1']
            ],
            [
                '_id' => 'custom_baz',
                'name' => 'baz',
                'description' => 'this should be the one we get2',
                'domain' => 'custom',
                'type' => SettingModel::TYPE_ARRAY,
                'data' => ['value' => 'test3']
            ],
        ];
    }

    /**
     * Data provider for testGetSettings
     *
     * Return array[]
     */
    public function getSettingsData()
    {
        // #0 test custom limit
        $expected = ['test1'];
        $out[] = [$expected, 1, 'default'];

        // #1 test default values
        $expected = ['test1', 'test1'];
        $out[] = [$expected];

        // #2 test custom domain
        $expected = ['test3'];
        $out[] = [$expected, 1000, 'custom'];

        return $out;
    }

    /**
     * Tests the method getSettings
     *
     * @param array $expected
     * @param int|null $limit
     * @param string $domain
     *
     * @dataProvider getSettingsData
     */
    public function testGetSettings($expected, $limit = null, $domain = null)
    {
        if (isset($limit) || isset($domain)) {
            $provider = new SessionModelAwareProvider($domain, $limit);
        } else {
            $provider = new SessionModelAwareProvider();
        }
        $provider->setSessionModel($this->sessionModel);

        $settings = $provider->getSettings();
        $this->assertEquals($expected, array_values($settings));
    }

    /**
     * Test getSettings witout index
     */
    public function testGetSettingsWithoutIndex()
    {
        $this->tearDown();
        $provider = new SessionModelAwareProvider();
        $provider->setSessionModel($this->sessionModel);
        $settings = $provider->getSettings();

        $this->assertEquals([], $settings);
    }
}
