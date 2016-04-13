<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Functional\Settings;

use ONGR\SettingsBundle\Document\Setting;
use ONGR\SettingsBundle\Settings\General\SettingsManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use ONGR\ElasticsearchBundle\Service\Manager;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Tests for SettingsManager.
 */
class SettingsManagerTest extends WebTestCase
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        static::bootKernel(['environment' => 'test_container_creation']);

        /** @var ContainerInterface container */
        $this->container = static::$kernel->getContainer();
        /** @var Manager $manager */
        $this->manager = $this->container->get('es.manager');

        $this->manager->dropAndCreateIndex();

        // There is something wrong with ElasticsearchTestCase method getDataArray,
        // if we don't create in here all test data, it's not existing when test is run.
        $content = new Setting();
        $content->setId('default_name0');
        $content->setName('name0');
        $content->setProfile('default');
        $content->setDescription('this should be updated');
        $content->setType(Setting::TYPE_STRING);
        $content->setData((object)['value' => 'test1']);
        $this->manager->persist($content);

        $this->manager->commit();
    }

    /**
     * Tests the method set.
     */
    public function testSetSettings()
    {
        $manager = new SettingsManager(
            $this->container->get('translator'),
            $this->container->get('event_dispatcher'),
            $this->manager
        );

        $manager->set(
            'name0',
            'string',
            'description',
            'test1',
            ['default']
        );
        $manager->set(
            'name1',
            'string',
            'description',
            'test13',
            ['not-default']
        );
        $setting1 = $this->getSetting('name0', Setting::TYPE_STRING, 'test1', 'default');
        $setting2 = $this->getSetting('name1', Setting::TYPE_STRING, 'test13', 'not-default');

        $repo = $this->manager->getRepository('ONGRSettingsBundle:Setting');

        $search = $repo
            ->createSearch()
            ->addQuery(new MatchAllQuery());
        $documents = $repo->execute($search);

        $expected = [$setting1, $setting2];

        $documents = iterator_to_array($documents);

        sort($documents);
        sort($expected);

        $this->assertEquals($expected[0]->getName(), $documents[0]->getName());
        $this->assertEquals($expected[1]->getName(), $documents[1]->getName());
    }

    /**
     * Tests duplicate method.
     */
    public function testDuplicate()
    {
        $manager = new SettingsManager(
            $this->container->get('translator'),
            $this->container->get('event_dispatcher'),
            $this->manager
        );

        $settingToCopy = $manager->get('name0', 'default');
        $settingToCopy->setName('SettingModel');

        $manager->duplicate($settingToCopy, ['newDomain']);

        $repo = $this->manager->getRepository('ONGRSettingsBundle:Setting');

        $search = $repo
            ->createSearch()
            ->addQuery(new MatchAllQuery());
        $documents = $repo->execute($search);

        $expectedCreated = clone $settingToCopy;
        $expectedCreated->setId('newDomain_SettingModel');
        $expectedCreated->setProfile('newDomain');
        $settingToCopy->setName('name0');

        $actual = iterator_to_array($documents);
        $expected = [$settingToCopy, $expectedCreated];

        sort($actual);
        sort($expected);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Public function test removing a setting.
     *
     * @expectedException \UnexpectedValueException
     */
    public function testRemove()
    {
        $manager = new SettingsManager(
            $this->container->get('translator'),
            $this->container->get('event_dispatcher'),
            $this->manager
        );
        $setting = $manager->get('name0', 'default');

        $this->assertInstanceOf('ONGR\SettingsBundle\Document\Setting', $setting);

        $manager->remove($setting);

        $manager->get('name0', 'default');
    }

    /**
     * Creates setting model.
     *
     * @param string $name
     * @param string $type
     * @param mixed  $value
     * @param string $profile
     *
     * @return Setting
     */
    private function getSetting($name, $type, $value, $profile = 'default')
    {
        $setting = new Setting();
        $setting->setId($profile . '_' . $name);
        $setting->setName($name);
        $setting->setDescription('ongr_settings.' . $name);
        $setting->setProfile($profile);
        $setting->setType($type);
        $setting->setData(['value' => $value]);

        return $setting;
    }
}
