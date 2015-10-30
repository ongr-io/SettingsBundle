<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Functional\DependencyInjection;

use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use ONGR\SettingsBundle\Document\Setting;

class SettingInjectionTest extends AbstractElasticsearchTestCase
{
    /**
     * @var string Value to be returned.
     */
    private $expected = 'test1';

    /**
     * Test for settings injection into services.
     */
    public function testSettingInjection()
    {
        $this->getManager();

        /** @var DummyService $dummyService */
        $dummyService = $this->getContainer()->get('ongr_settings.dummy_service');

        $this->assertEquals($this->expected, $dummyService->getSetting1());
    }

    /**
     * {@inheritdoc}
     */
    protected function getDataArray()
    {
        return [
            'default' => [
                'setting' => [
                    [
                        '_id' => 'default_setting_1',
                        'name' => 'setting_1',
                        'profile' => 'default',
                        'description' => 'test item #1',
                        'type' => Setting::TYPE_ARRAY,
                        'data' => (object)['value' => $this->expected],
                    ],
                ],
            ],
        ];
    }
}
