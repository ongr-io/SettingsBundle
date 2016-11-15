<?php

namespace ONGR\SettingsBundle\Tests\Functional;

use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use ONGR\SettingsBundle\Service\SettingsManager;

class SettingsManagerTest extends AbstractElasticsearchTestCase
{
    /**
     * @var SettingsManager
     */
    private $settingsManager;

    protected function getDataArray()
    {
        return [
            'settings' => [
                'setting' => [
                    [
                        '_id' => '1',
                        'name' => 'foo',
                        'profile' => [
                            'profile1',
                        ],
                        'value' => '1',
                        'type' => 'bool',
                    ],
                    [
                        '_id' => '2',
                        'name' => 'bar',
                        'profile' => [
                            'profile1',
                            'profile2',
                        ],
                        'value' => 'test-string',
                        'type' => 'string',
                    ],
                    [
                        '_id' => '3',
                        'name' => 'acme',
                        'profile' => [
                            'profile2',
                        ],
                        'value' => '0',
                        'type' => 'bool',
                    ],
                    [
                        '_id' => '4',
                        'name' => 'active_profiles',
                        'value' => [
                            'profile2',
                        ],
                        'type' => 'hidden',
                    ],
                    [
                        '_id' => '5',
                        'name' => 'active_experiments',
                        'value' => [
                            'foo_experiment',
                        ],
                        'type' => 'hidden',
                    ],
                    [
                        '_id' => '6',
                        'name' => 'foo_experiment',
                        'profile' => [
                            'profile1',
                            'profile2',
                        ],
                        'value' => [
                            'test_experiment_value',
                        ],
                        'type' => 'experiment',
                    ],
                    [
                        '_id' => '7',
                        'name' => 'bar_experiment',
                        'profile' => [
                            'profile1',
                        ],
                        'value' => [
                            'test_experiment_value',
                        ],
                        'type' => 'experiment',
                    ],
                ]
            ]
        ];
    }

    public function testGetAllExperiments()
    {
        $expected = [
            [
                'id' => '6',
                'name' => 'foo_experiment',
                'value' => ['test_experiment_value'],
                'profile' => ['profile1', 'profile2'],
                'type' => 'experiment',
                'description' => null,
                'salt' => null,
            ],
            [
                'id' => '7',
                'name' => 'bar_experiment',
                'value' => ['test_experiment_value'],
                'profile' => ['profile1'],
                'type' => 'experiment',
                'description' => null,
                'salt' => null,
            ]
        ];

        $experiments = [];
        $results = $this->getContainer()->get('ongr_settings.settings_manager')->getAllExperiments();

        foreach ($results as $experiment) {
            $experiments[] = $experiment->getSerializableData();
        }

        $this->assertEquals($expected, $experiments);
    }

    public function testToggleExperiment()
    {
        $manager = $this->getSettingsManager();
        $this->assertEquals(['foo_experiment'], $manager->getActiveExperiments());

        $manager->toggleExperiment('foo_experiment');
        $this->assertEquals([], $manager->getActiveExperiments());

        $manager->toggleExperiment('bar_experiment');
        $this->assertEquals(['bar_experiment'], $manager->getActiveExperiments());
    }

    public function testGetCachedExperiment()
    {
        $expected = [
            'id' => '6',
            'name' => 'foo_experiment',
            'value' => ['test_experiment_value'],
            'profile' => ['profile1', 'profile2'],
            'type' => 'experiment',
            'description' => null,
            'salt' => null,
        ];
        $manager = $this->getSettingsManager();
        // Test it without cache:
        $this->assertEquals($expected, $manager->getCachedExperiment('foo_experiment'));
        // Test it with cache:
        $this->assertEquals($expected, $manager->getCachedExperiment('foo_experiment'));
        // test a non existing experiment:
        $this->assertNull($manager->getCachedExperiment('non-existing-experiment'));
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage The setting `foo` was found but it is not an experiment
     */
    public function testGetCachedExperimentException()
    {
        $this->getSettingsManager()->getCachedExperiment('foo');
    }

    /**
     * @return SettingsManager
     */
    private function getSettingsManager()
    {
        if (!empty($this->settingsManager)) {
            $this->settingsManager->getCache()->deleteAll();
            return $this->settingsManager;
        }

        /** @var SettingsManager $manager */
        $manager = $this->getContainer()->get('ongr_settings.settings_manager');
        $manager->setActiveExperimentsSettingName('active_experiments');

        $this->settingsManager = $manager;

        return $this->settingsManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        // Deletes the settings manager cache
        $this->getSettingsManager();
    }
}
