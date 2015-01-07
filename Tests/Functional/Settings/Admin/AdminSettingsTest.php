<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Functional\Settings\Admin;

use ONGR\AdminBundle\Document\Setting;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use ONGR\AdminBundle\Tests\Fixtures\Security\LoginTestHelper;
use ONGR\ElasticsearchBundle\ORM\Manager;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Test integration with admin-user feature in admin bundle.
 */
class AdminSettingsTest extends WebTestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        static::bootKernel(['environment' => 'test_container_creation']);

        // Log in.
        $client = self::createClient();
        $loginHelper = new LoginTestHelper($client);
        $this->client = $loginHelper->loginAction('test', 'test');

        /** @var Manager $manager */
        $manager = static::$kernel->getContainer()->get('es.manager');

        // There is something wrong with ElasticsearchTestCase method getDataArray,
        // if we don't create in here all test data, it's not existing when test is run.
        $content = new Setting();
        $content->setId('foo_default');
        $content->setName('test');
        $content->setProfile('default');
        $content->setDescription('Description');
        $content->setType(Setting::TYPE_ARRAY);
        $content->setData((object)['value' => 'testData']);
        $manager->persist($content);

        $content = new Setting();
        $content->setId('foo');
        $content->setName('test2');
        $content->setProfile('profile_foo.com');
        $content->setDescription('Description');
        $content->setType(Setting::TYPE_ARRAY);
        $content->setData((object)['value' => 'testData']);
        $manager->persist($content);

        $content = new Setting();
        $content->setId('bar');
        $content->setName('test2');
        $content->setProfile('profile_foo.com');
        $content->setDescription('Description');
        $content->setType(Setting::TYPE_ARRAY);
        $content->setData((object)['value' => 'testData']);
        $manager->persist($content);

        $manager->commit();
    }

    /**
     * When user is logged in, he should see admin settings and profile checkboxes.
     */
    public function testSettingsDisplayed()
    {
        // Retrieve content.
        $crawler = $this->client->request('GET', '/admin/settings');

        // Asserts.
        $settingsDescription = $crawler->filter('#settings_ongr_admin_live_settings');
        $this->assertCount(1, $settingsDescription, 'Live settings setting must exist');

        $profile = $crawler->filter('#settings_ongr_admin_profile_default');
        $this->assertCount(1, $profile, 'Profile default checkbox must exist');

        $profile = $crawler->filter('#settings_ongr_admin_profile_profile_foo-2e-com');
        $this->assertCount(1, $profile, 'Profile foo checkbox must exist');

        $categories = $crawler->filter('.category');
        $this->assertCount(4, $categories);
    }

    /**
     * When user is logged in, and he selects profile, then settings container must receive that choice.
     */
    public function testSettingsSelected()
    {
        // Retrieve content.
        $crawler = $this->client->request('GET', '/admin/settings');

        // Submit domain selection.
        $buttonNode = $crawler->selectButton('settings_submit');
        $form = $buttonNode->form();
        /** @noinspection PhpUndefinedMethodInspection */
        $form['settings[ongr_admin_profile_profile_foo-2e-com]']->tick();
        $this->client->submit($form);

        // Load any url and check that user selected domains are loaded.
        $this->client->request('GET', '/admin/setting/name0/edit');
        $settingsContainer = $this->client->getContainer()->get('ongr_admin.settings_container');

        $selectedDomains = $settingsContainer->getProfiles();
        $this->assertEquals(['default', 'test1', 'test2', 'meh', 'profile_foo.com'], $selectedDomains);
    }
}
