<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Unit\Controller;

use ONGR\AdminBundle\Controller\SettingsManagerController;
use ONGR\AdminBundle\Document\Setting;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SettingsManagerControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Data provider for testSetSettingAction.
     *
     * @return array
     */
    public function getSetSettingActionData()
    {
        $out = [];

        // Case #0: string.
        $out[] = ['name1', json_encode('value1')];

        // Case #1: json.
        $out[] = ['name2', json_encode(['value2', 'value3'])];

        // Case #2: invalid json.
        $out[] = ['name3', serialize(['value4', 'value5']), Response::HTTP_NOT_ACCEPTABLE];

        return $out;
    }

    /**
     * Test for set setting action.
     *
     * @param string $name
     * @param string $value
     * @param int    $status
     *
     * @dataProvider getSetSettingActionData
     */
    public function testSetSettingAction($name, $value, $status = Response::HTTP_OK)
    {
        $manager = $this
            ->getMockBuilder('ONGR\AdminBundle\Service\SettingsManager')
            ->disableOriginalConstructor()
            ->getMock();

        if ($status == Response::HTTP_OK) {
            $manager
                ->expects($this->once())
                ->method('set')
                ->with($name, json_decode($value));
        }

        $container = new ContainerBuilder();
        $container->set('ongr_admin.settings_manager', $manager);

        $controller = new SettingsManagerController();
        $controller->setContainer($container);

        $request = new Request(
            [],
            ['value' => $value]
        );

        $response = $controller->setSettingAction($request, $name);
        $this->assertEquals($status, $response->getStatusCode());
    }

    /**
     * Test if ng edit action works as expected.
     */
    public function testNgEditAction()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $request
            ->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue(json_encode(['setting' => ['data' => ['value' => 'foobaz']]])));

        $manager = $this
            ->getMockBuilder('ONGR\AdminBundle\Service\SettingsManager')
            ->disableOriginalConstructor()
            ->getMock();

        $manager
            ->expects($this->once())
            ->method('get')
            ->with('fooname', 'profile')
            ->will($this->returnValue(new Setting()));

        $setting = new Setting();
        $setting->data['value'] = 'foobaz';

        $manager
            ->expects($this->once())
            ->method('save')
            ->with($setting);

        $container = new ContainerBuilder();
        $container->set('ongr_admin.settings_manager', $manager);

        $controller = new SettingsManagerController();
        $controller->setContainer($container);

        $this->assertEquals(
            Response::HTTP_OK,
            $controller->ngEditAction($request, 'fooname', 'profile')->getStatusCode()
        );
    }

    /**
     * Test if getting 400 status code with wrong content data.
     *
     * @param string $content
     *
     * @dataProvider getNgEditActionFailData
     */
    public function testNgEditActionFailValue($content)
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $request
            ->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue($content));

        $controller = new SettingsManagerController();
        $response = $controller->ngEditAction($request, 'foobaz', 'default');
        $this->assertEquals(
            Response::HTTP_BAD_REQUEST,
            $response->getStatusCode()
        );
    }

    /**
     * Data provider for testNgEditActionFail.
     *
     * @return array
     */
    public function getNgEditActionFailData()
    {
        $out = [];

        // Case #0: empty.
        $out[] = [''];

        // Case #1: non json.
        $out[] = ['data' => 'data'];

        // Case #2: invalid json.
        $out[] = [json_encode(['data' => 'wrong_data'])];

        return $out;
    }
}
