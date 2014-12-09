<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Functional\Service;

use ONGR\AdminBundle\Document\Setting;
use ONGR\AdminBundle\Service\SettingsManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\ElasticsearchBundle\DSL\Query\MatchAllQuery;
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

        $this->manager->getConnection()->dropAndCreateIndex();

        // There is something wrong with ElasticsearchTestCase method getDataArray,
        // if we don't create in here all test data, it's not existing when test is run.
        $content = new Setting();
        $content->setId('default_name0');
        $content->name = 'name0';
        $content->profile = 'default';
        $content->description = 'this should be updated';
        $content->type = Setting::TYPE_STRING;
        $content->data = (object)['value' => 'test1'];
        $this->manager->persist($content);

        $this->manager->commit();
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
        $setting->name = $name;
        $setting->description = 'ongr_admin.' . $name;
        $setting->profile = $profile;
        $setting->type = $type;
        $setting->data = ['value' => $value];
        $setting->setScore(1.0);

        return $setting;
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

        $manager->set('name0', 'test1', 'default');
        $manager->set('name1', 'test13', 'not-default');
        $setting1 = $this->getSetting('name0', Setting::TYPE_STRING, 'test1', 'default');
        $setting2 = $this->getSetting('name1', Setting::TYPE_STRING, 'test13', 'not-default');

        $repo = $this->manager->getRepository('ONGRAdminBundle:Setting');

        $search = $repo
            ->createSearch()
            ->addQuery(new MatchAllQuery());
        $documents = $repo->execute($search);

        $expected = [$setting1, $setting2];

        sort(iterator_to_array($documents));
        rsort($expected);

        $this->assertEquals($expected, iterator_to_array($documents));
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
        $settingToCopy->setScore(1.0);
        $settingToCopy->name = 'SettingModel';

        $manager->duplicate($settingToCopy, 'newDomain');

        $repo = $this->manager->getRepository('ONGRAdminBundle:Setting');

        $search = $repo
            ->createSearch()
            ->addQuery(new MatchAllQuery());
        $documents = $repo->execute($search);

        $expectedCreated = clone $settingToCopy;
        $expectedCreated->setId('newDomain_SettingModel');
        $expectedCreated->profile = 'newDomain';
        $settingToCopy->name = 'name0';

        $actual = iterator_to_array($documents);
        $expected = [$settingToCopy, $expectedCreated];

        sort($actual);
        sort($expected);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Public function test removing a setting.
     *
     * @expectedException \Elasticsearch\Common\Exceptions\Missing404Exception
     */
    public function testRemove()
    {
        $manager = new SettingsManager(
            $this->container->get('translator'),
            $this->container->get('event_dispatcher'),
            $this->manager
        );
        $setting = $manager->get('name0', 'default');

        $this->assertInstanceOf('ONGR\AdminBundle\Document\Setting', $setting);

        $manager->remove($setting);

        $manager->get('name0', 'default');
    }
}
