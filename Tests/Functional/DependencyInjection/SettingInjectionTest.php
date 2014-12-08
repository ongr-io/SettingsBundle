<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Functional\DependencyInjection;

use ONGR\AdminBundle\Document\Setting;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;

class SettingInjectionTest extends ElasticsearchTestCase
{
    /**
     * @var Value to be returned.
     */
    private $expected = 'test1';

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $setting = new Setting();
        $setting->setId('default_setting_1');
        $setting->name = 'setting_1';
        $setting->description = 'test item #1';
        $setting->profile = 'default';
        $setting->type = Setting::TYPE_ARRAY;
        $setting->data = (object)['value' => $this->expected];

        $manager = $this->getManager();
        $manager->persist($setting);
        $manager->commit();
        $manager->flush();
    }

    /**
     * Test for settings injection into services.
     */
    public function testSettingInjection()
    {
        /** @var DummyService $dummyService */
        $dummyService = $this->getContainer()->get('ongr_admin.dummy_service');

        $this->assertEquals($this->expected, $dummyService->getSetting1());
    }
}
