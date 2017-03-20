<?php

namespace ONGR\SettingsBundle\Tests\Functional;

use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RequestListenerTest extends AbstractElasticsearchTestCase
{
    protected function getDataArray()
    {
        return [
            'settings' => [
                'setting' => [
                    [
                        '_id' => '1',
                        'name' => 'foo',
                        'profile' => ['not-profile'],
                        'value' => true,
                        'type' => 'bool',
                    ],
                    [
                        '_id' => '2',
                        'name' => 'bar',
                        'profile' => ['profile'],
                        'value' => true,
                        'type' => 'bool',
                    ],
                ]
            ]
        ];
    }

    public function testOnKernelRequest()
    {
        $cookie = $this->getContainer()->get('ongr_settings.cookie.active_experiments');
        $cookie->load(null);
        $cookie = $this->getContainer()->get('ongr_settings.cookie.active_profiles');
        $cookie->load(json_encode(['profile']));
        $listener = $this->getContainer()->get('ongr_settings.request_listener');
        $request = new Request();
        $request->cookies->set(
            $this->getContainer()->getParameter('ongr_settings.cookie.active_profiles.name'),
            serialize(['profile'])
        );
        $event = new GetResponseEvent(static::$kernel, $request, HttpKernelInterface::MASTER_REQUEST);
        $listener->onKernelRequest($event);
        $extension = $this->getContainer()->get('ongr_settings.setting_extension');

        $this->assertTrue($extension->getSettingValue('bar'));
        $this->assertFalse($extension->getSettingValue('foo'));
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
