<?php

namespace ONGR\SettingsBundle\Tests\Functional;

use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ExperimentListenerTest extends AbstractElasticsearchTestCase
{
    protected function getDataArray()
    {
        return [
            'settings' => [
                'setting' => [
                    [
                        '_id' => '5',
                        'name' => 'active_experiments',
                        'value' => ['bar_experiment'],
                        'type' => 'hidden',
                    ],
                    [
                        '_id' => '6',
                        'name' => 'foo_experiment',
                        'profile' => ['profile1', 'profile2'],
                        'value' => ['test_experiment_value'],
                        'type' => 'experiment',
                    ],
                    [
                        '_id' => '7',
                        'name' => 'bar_experiment',
                        'profile' => ['profile1'],
                        'value' => '{"Clients":{"types":["Browser"],"clients":["Safari"]}}',
                        'type' => 'experiment',
                    ],
                ]
            ]
        ];
    }

    public function testOnKernelRequest()
    {
        $this->getContainer()->get('ongr_settings.settings_manager')
            ->setActiveExperimentsSettingName('active_experiments');

        $cookie = $this->getContainer()->get('ongr_settings.cookie.active_experiments');
        $cookie->load(null);
        $listener = $this->getContainer()->get('ongr_settings.experiment_listener');
        $request = new Request();
        $request->headers->set(
            'User-Agent',
            'AppleWebKit/602.1.50 (KHTML, like Gecko) Version/10.0 Safari/602.1.50'
        );
        $event = new GetResponseEvent(static::$kernel, $request, HttpKernelInterface::MASTER_REQUEST);
        $listener->onKernelRequest($event);
        $this->assertEquals(['profile1'], $cookie->getValue());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->getContainer()->get('ongr_settings.settings_manager')->getCache()->deleteAll();
    }
}
