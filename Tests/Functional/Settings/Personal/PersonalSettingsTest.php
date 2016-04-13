<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Functional\Settings\Personal;

use ONGR\SettingsBundle\Document\Setting;
use ONGR\SettingsBundle\Document\Profile;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use ONGR\ElasticsearchBundle\Service\Manager;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Test integration with admin-user feature in admin bundle.
 */
class PersonalSettingsTest extends WebTestCase
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

        /** @var Client $client */
        $this->client = static::createClient();
        /** @var Manager $manager */
        $manager = static::$kernel->getContainer()->get('es.manager');

        // There is something wrong with ElasticsearchTestCase method getDataArray,
        // if we don't create in here all test data, it's not existing when test is run.
        $content = new Profile();
        $content->setId('default_profile');
        $content->setName('default');
        $manager->persist($content);

        $content = new Profile();
        $content->setName('profile_foo.com_profile');
        $content->setName('profile_foo.com');
        $manager->persist($content);

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
        $crawler = $this->client->request('GET', '/settings/settings');

        // Asserts.
        $settingsDescription = $crawler->filter('.category-ongr_settings_settings');
        $this->assertCount(1, $settingsDescription, 'Live settings setting must exist');

        $profile = $crawler->filter('.profile-ongr_settings_profiles');
        $this->assertCount(2, $profile, 'Profile default checkbox must exist');
    }

    /**
     * When user is logged in, and he selects profile, then settings container must receive that choice.
     */
    public function testSettingsSelected()
    {
        $this->client->request('GET',
            $this->client->getContainer()->get('router')->generate(
                'ongr_settings_personal_settings_change',
                [
                    'encodedName' => base64_encode('ongr_settings_profile_default'),
                ]
            )
        );
        $this->assertTrue($this->client->getResponse()->isOk());
        // Load any url and check that user selected domains are loaded.
        $this->client->request('GET', '/settings/setting/test/edit');
        $settingsContainer = $this->client->getContainer()->get('ongr_settings.settings_container');

        $selectedDomains = $settingsContainer->getProfiles();
        $this->assertTrue(in_array('default', $selectedDomains));
    }
}
