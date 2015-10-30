<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Functional\Settings\General;

use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use ONGR\SettingsBundle\Document\Setting;
use ONGR\SettingsBundle\Settings\General\Provider\ManagerAwareSettingProvider;
use ONGR\SettingsBundle\Settings\General\SettingsContainer;
use Stash\Pool;

class SettingsContainerTest extends AbstractElasticsearchTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getDataArray()
    {
        return [
            'default' => [
                'setting' => [
                    [
                        '_id' => 'default_test',
                        'name' => 'test',
                        'profile' => 'default',
                        'description' => 'test item #1',
                        'type' => Setting::TYPE_ARRAY,
                        'data' => (object)['value' => 'test1'],
                    ],
                    [
                        '_id' => 'default_test2',
                        'name' => 'test2',
                        'profile' => 'default',
                        'description' => 'test item #2',
                        'type' => Setting::TYPE_ARRAY,
                        'data' => (object)['value' => 'test2'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Test for SettingsContainer get.
     */
    public function testGet()
    {
        $pool = new Pool();
        $settingsContainer = new SettingsContainer($pool);
        $provider = new ManagerAwareSettingProvider();
        $provider->setManager($this->getManager());
        $settingsContainer->addProvider($provider);

        // First time, it should be loaded from database.
        $settings = $settingsContainer->get('test');
        $this->assertEquals('test1', $settings);

        // Second time it should be loaded from object itself.
        $this->getManager()->dropIndex();

        $settings = $settingsContainer->get('test');
        $this->assertEquals('test1', $settings);
        $settings = $settingsContainer->get('test2');
        $this->assertEquals('test2', $settings);
    }
}
