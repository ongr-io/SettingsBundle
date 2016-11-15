<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Unit\Service;

use Doctrine\Common\Cache\PhpFileCache;
use ONGR\CookiesBundle\Cookie\Model\GenericCookie;
use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\ElasticsearchBundle\Service\Manager;
use ONGR\ElasticsearchBundle\Service\Repository;
use ONGR\ElasticsearchDSL\Search;
use ONGR\SettingsBundle\Document\Setting;
use ONGR\SettingsBundle\Service\SettingsManager;

class SettingsManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $manager;

    /**
     * @var Repository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;

    /**
     * @var PhpFileCache|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cache;

    /**
     * @var GenericCookie|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cookie;

    public function setUp()
    {
        $this->manager = $this->getMockBuilder('ONGR\ElasticsearchBundle\Service\Manager')
            ->disableOriginalConstructor()
            ->setMethods(['persist', 'commit'])
            ->getMock();
        $this->cache = $this->getMockBuilder('Doctrine\Common\Cache\PhpFileCache')
            ->disableOriginalConstructor()
            ->setMethods(['contains', 'fetch', 'save', 'delete'])
            ->getMock();
        $this->cookie = $this->getMockBuilder('ONGR\CookiesBundle\Cookie\Model\GenericCookie')
            ->disableOriginalConstructor()
            ->setMethods(['getValue'])
            ->getMock();
        $this->repository = $this->getMockBuilder('ONGR\ElasticsearchBundle\Service\Repository')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'findOneBy',
                    'remove',
                    'createSearch',
                    'findDocuments',
                    'getClassName',
                    'getManager',
                    'getAggregation',
                ]
            )
            ->getMock();

        $this->repository->expects($this->any())->method('getClassName')->willReturn(Setting::class);
        $this->repository->expects($this->any())->method('getManager')->willReturn($this->manager);
        $this->repository->expects($this->any())->method('createSearch')->willReturn(new Search());
    }

    /**
     * Test get cache getter.
     */
    public function testGetCache()
    {
        $manager = new SettingsManager(
            $this->repository,
            $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );
        $manager->setCache($this->cache);

        $this->assertInstanceOf('Doctrine\Common\Cache\PhpFileCache', $manager->getCache());
    }

    /**
     * Test get cookie getter.
     */
    public function testGetActiveProfilesCookie()
    {
        $manager = new SettingsManager(
            $this->repository,
            $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );
        $manager->setActiveProfilesCookie($this->cookie);

        $this->assertInstanceOf('ONGR\CookiesBundle\Cookie\Model\GenericCookie', $manager->getActiveProfilesCookie());
    }

    /**
     * Test get cookie getter.
     */
    public function testGetActiveProfilesSettingName()
    {
        $manager = new SettingsManager(
            $this->repository,
            $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );
        $manager->setActiveProfilesSettingName('ongr');

        $this->assertEquals('ongr', $manager->getActiveProfilesSettingName());
    }

    /**
     * Test setting create function.
     */
    public function testCreate()
    {
        $data = [
            'name' => 'acme',
            'type' => 'string',
            'value' => 'foo',
        ];

        $this->manager->expects($this->once())->method('persist')->with($this->callback(function ($obj) {
            return $obj instanceof Setting;
        }))->willReturn(null);
        $this->manager->expects($this->once())->method('persist')->willReturn(null);

        $manager = new SettingsManager(
            $this->repository,
            $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );
        $document = $manager->create($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $document->{'get' . ucfirst($key)}());
        }
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Missing one of the mandatory field!
     */
    public function testCreateMandatoryParameters()
    {
        $data = ['bar' => 'foo'];

        $manager = new SettingsManager(
            $this->repository,
            $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );
        $manager->create($data);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Setting acme already exists.
     */
    public function testCreateWhenSettingExists()
    {
        $data = [
            'name' => 'acme',
            'type' => 'string',
            'value' => 'foo',
        ];

        $this->repository->expects($this->once())->method('findOneBy')->willReturn(new Setting());

        $manager = new SettingsManager(
            $this->repository,
            $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );
        $manager->create($data);
    }

    /**
     * Tests setting create without value set. Should be set to 0 by default.
     */
    public function testWithoutDataValueSet()
    {
        $data = [
            'name' => 'acme',
            'type' => 'string',
        ];

        $manager = new SettingsManager(
            $this->repository,
            $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );
        $document = $manager->create($data);

        $this->assertEquals(0, $document->getValue());
    }

    /**
     * Tests setting update.
     */
    public function testUpdate()
    {
        $setting = new Setting();
        $setting->setName('acme');
        $setting->setValue('foo');

        $this->repository->expects($this->once())->method('findOneBy')->willReturn($setting);
        $manager = new SettingsManager(
            $this->repository,
            $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );
        $manager->setCache($this->cache);

        $document = $manager->update('acme', ['value' => 'bar']);
        $this->assertEquals('acme', $document->getName());
        $this->assertEquals('bar', $document->getValue());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Setting acme not exist.
     */
    public function testUpdateWhenSettingNotExists()
    {
        $this->repository->expects($this->once())->method('findOneBy')->willReturn(null);

        $manager = new SettingsManager(
            $this->repository,
            $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );
        $manager->setCache($this->cache);
        $manager->update('acme', ['value' => 'foo']);
    }

    /**
     * Tests setting delete.
     */
    public function testDelete()
    {
        $setting = new Setting();
        $setting->setId('acme');
        $setting->setName('acme');

        $this->repository->expects($this->any())
            ->method('findOneBy')->with($this->equalTo(['name.name' => 'acme']))->willReturn($setting);
        $manager = new SettingsManager(
            $this->repository,
            $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );
        $manager->setCache($this->cache);

        $manager->delete('acme');
    }

    public function testHas()
    {
        $setting = new Setting();
        $setting->setName('acme');
        $setting->setValue('foo');

        $this->repository->expects($this->once())
            ->method('findOneBy')->with($this->equalTo(['name.name' => 'acme']))->willReturn($setting);
        $manager = new SettingsManager(
            $this->repository,
            $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );

        $result = $manager->has('acme');

        $this->assertTrue($result);
    }

    /**
     * Test has method when there is no setting.
     */
    public function testHasWhenThereIsNoSetting()
    {
        $this->repository->expects($this->once())
            ->method('findOneBy')->with($this->equalTo(['name.name' => 'acme']))->willReturn(null);
        $manager = new SettingsManager(
            $this->repository,
            $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );

        $result = $manager->has('acme');

        $this->assertFalse($result);
    }

    /**
     * Tests setting update.
     */
    public function testGetValue()
    {
        $setting = new Setting();
        $setting->setName('acme');
        $setting->setValue('foo');

        $this->repository->expects($this->once())->method('findOneBy')->willReturn($setting);
        $manager = new SettingsManager(
            $this->repository,
            $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );

        $result = $manager->getValue('acme');
        $this->assertEquals('foo', $result);
    }

    /**
     * Tests setting update.
     */
    public function testGetValueWhenThereIsNoSetting()
    {
        $this->repository->expects($this->once())->method('findOneBy')->willReturn(null);
        $manager = new SettingsManager(
            $this->repository,
            $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );

        $result = $manager->getValue('acme', 'bar');
        $this->assertEquals('bar', $result);
    }

    /**
     * Returns document iterator with pre-loaded aggregations.
     *
     * @return DocumentIterator
     */
    private function getDocumentIterator()
    {
        $rawData = [
            'aggregations' => [
                'filter' => [
                    'profiles' => [
                        'buckets' => [
                            [
                                'key' => 'default',
                                'doc_count' => 2,
                                'documents' => [
                                    'hits' => [
                                        'total' => 2,
                                        'max_score' => 1,
                                        'hits' => [
                                            [
                                                '_index' => 'settings',
                                                '_type' => 'setting',
                                                '_id' => 'kk',
                                                '_score' => 1,
                                                '_source' => [
                                                    'name' => 'foo',
                                                    'profile' => [
                                                        'bar'
                                                    ],
                                                    'type' => 'bool',
                                                    'value' => 1
                                                ]
                                            ],
                                            [
                                                '_index' => 'settings',
                                                '_type' => 'setting',
                                                '_id' => 'xx',
                                                '_score' => 1,
                                                '_source' => [
                                                    'name' => 'kk',
                                                    'profile' => [
                                                        'kk'
                                                    ],
                                                    'type' => 'bool',
                                                    'value' => 1
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'key' => 'foo',
                                'doc_count' => 1,
                                'documents' => [
                                    'hits' => [
                                        'total' => 1,
                                        'max_score' => 1,
                                        'hits' => [
                                            [
                                                '_index' => 'settings',
                                                '_type' => 'setting',
                                                '_id' => 'kk',
                                                '_score' => 1,
                                                '_source' => [
                                                    'name' => 'foo',
                                                    'profile' => [
                                                        'bar'
                                                    ],
                                                    'type' => 'bool',
                                                    'value' => 1
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'key' => 'kk',
                                'doc_count' => 1,
                                'documents' => [
                                    'hits' => [
                                        'total' => 1,
                                        'max_score' => 1,
                                        'hits' => [
                                            [
                                                '_index' => 'settings',
                                                '_type' => 'setting',
                                                '_id' => 'kk',
                                                '_score' => 1,
                                                '_source' => [
                                                    'name' => 'foo',
                                                    'profile' => [
                                                        'bar'
                                                    ],
                                                    'type' => 'bool',
                                                    'value' => 1
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return new DocumentIterator($rawData, $this->manager);
    }

    /**
     * Tests setting update.
     */
    public function testGetCachedValue()
    {

        $activeProfilesSetting = 'active_profiles';

        $this->repository->expects($this->any())->method('findOneBy')->willReturnCallback(
            function ($arg) use ($activeProfilesSetting) {
                $settingName = $arg['name.name'];
                $setting = new Setting();
                switch ($settingName) {
                    case 'active_profiles':
                        $setting->setName($activeProfilesSetting);
                        $setting->setValue(['bar', 'foo']);
                        return $setting;
                        break;
                    case 'acme':
                        $setting->setName('acme');
                        $setting->setValue('foo');
                        $setting->setProfile(['foo', 'default']);
                        break;
                }
                return $setting;
            }
        );

        $this->repository->expects($this->any())->method('findDocuments')->willReturn($this->getDocumentIterator());
        $this->cookie->expects($this->any())->method('getValue')->willReturn(['foo']);

        $manager = new SettingsManager(
            $this->repository,
            $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );
        $manager->setCache($this->cache);
        $manager->setActiveProfilesSettingName($activeProfilesSetting);
        $manager->setActiveProfilesCookie($this->cookie);
        $manager->setActiveProfilesList(['default']);

        $result = $manager->getCachedValue('acme');
        $this->assertEquals('foo', $result);
    }

    /**
     * Tests setting update.
     */
    public function testGetCachedValueFromCache()
    {

        $activeProfilesSetting = 'active_profiles';

        $this->repository->expects($this->any())->method('execute')->willReturn($this->getDocumentIterator());
        $this->cookie->expects($this->any())->method('getValue')->willReturn(['foo']);
        $this->cache->expects($this->any())->method('contains')->willReturn(true);
        $this->cache->expects($this->any())->method('fetch')->willReturnCallback(
            function ($arg) use ($activeProfilesSetting) {
                if ($arg == $activeProfilesSetting) {
                    return ['foo'];
                }
                return ['value' => 'foo', 'profiles' => ['foo']];
            }
        );

        $manager = new SettingsManager(
            $this->repository,
            $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );
        $manager->setCache($this->cache);
        $manager->setActiveProfilesSettingName($activeProfilesSetting);
        $manager->setActiveProfilesCookie($this->cookie);

        $result = $manager->getCachedValue('acme');
        $this->assertEquals('foo', $result);
    }

    /**
     * Tests setting update.
     */
    public function testGetCachedValueWithoutActiveProfiles()
    {
        $activeProfilesSetting = 'active_profiles';
        $this->repository->expects($this->any())->method('execute')->willReturn($this->getDocumentIterator());
        $this->cookie->expects($this->any())->method('getValue')->willReturn(['foo']);
        $this->cache->expects($this->any())->method('contains')->willReturn(true);
        $this->cache->expects($this->any())->method('fetch')->willReturn(['value' => 'foo', 'profiles' => ['foo']]);

        $manager = new SettingsManager(
            $this->repository,
            $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );
        $manager->setCache($this->cache);
        $manager->setActiveProfilesSettingName($activeProfilesSetting);
        $manager->setActiveProfilesCookie($this->cookie);

        $result = $manager->getCachedValue('acme', false);
        $this->assertEquals('foo', $result);
    }

    /**
     * Tests if there is no setting.
     */
    public function testGetCachedValueWhenItsNotExist()
    {
        $activeProfilesSetting = 'active_profiles';
        $this->repository->expects($this->any())->method('execute')->willReturn($this->getDocumentIterator());
        $manager = new SettingsManager(
            $this->repository,
            $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );
        $manager->setCache($this->cache);
        $manager->setActiveProfilesSettingName($activeProfilesSetting);

        $value = $manager->getCachedValue('acme');
        $this->assertNull($value);
    }

    public function testGetAllActiveProfilesNameList()
    {
        $document = new Setting();
        $document->setName('active_profiles');
        $document->setValue(['kk']);
        $this->repository->expects($this->any())->method('findOneBy')->willReturn($document);
        $this->repository->expects($this->any())->method('findDocuments')->willReturn($this->getDocumentIterator());
        $manager = new SettingsManager(
            $this->repository,
            $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );
        $manager->setCache($this->cache);

        $value = $manager->getActiveProfiles();
        $this->assertEquals(['kk'], $value);
    }

    public function testGetActiveExperimentsFromRepository()
    {
        $activeExperimentsSettingName = 'foo';
        $experimentName = 'bar';
        $cache = $this->cache;
        $cache->expects($this->any())->method('contains')->willReturn(false);
        $activeExperiments = new Setting;
        $activeExperiments->setName($activeExperimentsSettingName);
        $activeExperiments->setValue([$experimentName]);
        $repository = $this->repository;
        $repository->expects($this->any())->method('findOneBy')
            ->with(['name.name' => $activeExperimentsSettingName])->willReturn($activeExperiments);
        $manager = new SettingsManager(
            $repository,
            $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );
        $manager->setActiveExperimentsSettingName($activeExperimentsSettingName);
        $manager->setCache($cache);

        $activeExperiments = $manager->getActiveExperiments();

        $this->assertEquals([$experimentName], $activeExperiments);
    }

    public function testGetActiveExperimentsFromCache()
    {
        $activeExperimentsSettingName = 'foo';
        $experimentName = 'bar';
        $cache = $this->cache;
        $cache->expects($this->any())->method('contains')
            ->with($activeExperimentsSettingName)->willReturn(true);
        $cache->expects($this->any())->method('fetch')
            ->with($activeExperimentsSettingName)->willReturn(['value' => [$experimentName]]);
        $manager = new SettingsManager(
            $this->repository,
            $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );
        $manager->setActiveExperimentsSettingName($activeExperimentsSettingName);
        $manager->setCache($cache);

        $activeExperiments = $manager->getActiveExperiments();

        $this->assertEquals([$experimentName], $activeExperiments);
    }

    public function testGetActiveExperimentsCreateNew()
    {
        $activeExperimentsSettingName = 'foo';
        $cache = $this->cache;
        $cache->expects($this->any())->method('contains')
            ->with($activeExperimentsSettingName)->willReturn(false);
        $repository = $this->repository;
        $repository->expects($this->any())->method('findOneBy')
            ->with(['name.name' => $activeExperimentsSettingName])->willReturn(null);
        $manager = new SettingsManager(
            $repository,
            $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );
        $manager->setActiveExperimentsSettingName($activeExperimentsSettingName);
        $manager->setCache($cache);

        $activeExperiments = $manager->getActiveExperiments();

        $this->assertEquals([], $activeExperiments);
    }

    public function testToggleExperiment()
    {
        $activeExperimentsSettingName = 'active_profiles';
        $activeExperimentsSetting = new Setting();
        $activeExperimentsSetting->setName($activeExperimentsSettingName);
        $repository = $this->repository;
        $repository->expects($this->any())->method('findOneBy')
            ->with(['name.name' => $activeExperimentsSettingName])->willReturn($activeExperimentsSetting);
        $manager = new SettingsManager(
            $repository,
            $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );
        $manager->setCache($this->cache);
        $manager->setActiveExperimentsSettingName($activeExperimentsSettingName);
        $manager->toggleExperiment('foo');
        $this->assertEquals(['foo'], $activeExperimentsSetting->getValue());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage The setting `active_profiles` is not set
     */
    public function testGetCachedExperimentException()
    {
        $activeExperimentsSettingName = 'active_profiles';
        $repository = $this->repository;
        $repository->expects($this->any())->method('findOneBy')
            ->with(['name.name' => $activeExperimentsSettingName])->willReturn(null);
        $manager = new SettingsManager(
            $repository,
            $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );
        $manager->setActiveExperimentsSettingName($activeExperimentsSettingName);
        $manager->toggleExperiment('foo');
    }
}
