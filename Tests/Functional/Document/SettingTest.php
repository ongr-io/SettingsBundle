<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Functional\Document;

use ONGR\AdminBundle\Document\Setting;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;

/**
 * Tests for setting model.
 */
class SettingTest extends ElasticsearchTestCase
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
                        '_id' => 'testProfile_name0',
                        'name' => 'name0',
                        'profile' => 'testProfile',
                        'description' => 'test Description with the analyzer',
                        'type' => Setting::TYPE_ARRAY,
                        'data' => (object)['value' => 'testData'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Check if documents are saved correctly and if we can find them by indexed properties.
     */
    public function testDocumentsSave()
    {
        $repo = $this->getManager()->getRepository('ONGRAdminBundle:Setting');

        /** @var Setting $setting */
        $setting = $repo->find('testProfile_name0');

        $this->assertEquals('test Description with the analyzer', $setting->description);
        $this->assertEquals('testProfile', $setting->profile);
        $this->assertEquals(Setting::TYPE_ARRAY, $setting->type);
        $this->assertEquals(['value' => 'testData'], $setting->data);
    }
}
