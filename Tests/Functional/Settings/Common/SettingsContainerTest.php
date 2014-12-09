<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Functional\Settings\Common;

use ONGR\AdminBundle\Document\Setting;
use ONGR\AdminBundle\Settings\Common\Provider\ManagerAwareSettingProvider;
use ONGR\AdminBundle\Settings\Common\SettingsContainer;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use Stash\Pool;

class SettingsContainerTest extends ElasticsearchTestCase
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
        $this->getManager()->getConnection()->dropIndex();

        $settings = $settingsContainer->get('test');
        $this->assertEquals('test1', $settings);
        $settings = $settingsContainer->get('test2');
        $this->assertEquals('test2', $settings);
    }
}
