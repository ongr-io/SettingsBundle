<?php

/*
 *************************************************************************
 * NFQ eXtremes CONFIDENTIAL
 * [2013] - [2014] NFQ eXtremes UAB
 * All Rights Reserved.
 *************************************************************************
 * NOTICE: 
 * All information contained herein is, and remains the property of NFQ eXtremes UAB.
 * Dissemination of this information or reproduction of this material is strictly forbidden
 * unless prior written permission is obtained from NFQ eXtremes UAB.
 *************************************************************************
 */

namespace ONGR\AdminBundle\Tests\Functional\Controller;

use ONGR\AdminBundle\Controller\SettingsManagerController;
use ONGR\AdminBundle\Model\SettingModel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;

class SettingsManagerControllerTest extends \PHPUnit_Framework_TestCase
{
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_OK = 200;

    /**
     * data provider for testSetSettingAction
     *
     * @return array
     */
    public function getSetSettingActionData()
    {
        $out = [];

        //case #0: string
        $out[] = ['name1', json_encode("value1")];

        //case #1: json
        $out[] = ['name2', json_encode(["value2", "value3"])];

        //case #2: invalid json
        $out[] = ['name3', serialize(["value4", "value5"]), self::HTTP_NOT_ACCEPTABLE];

        return $out;
    }

    /**
     * test for set setting action
     *
     * @param string $name
     * @param string $value
     * @param int $status
     *
     * @dataProvider getSetSettingActionData
     */
    public function testSetSettingAction($name, $value, $status = self::HTTP_OK)
    {
        $manager = $this
            ->getMockBuilder('ONGR\AdminBundle\Service\SettingsManager')
            ->disableOriginalConstructor()
            ->getMock();

        if ($status == self::HTTP_OK) {
            $manager
                ->expects($this->once())
                ->method('set')
                ->with($name, json_decode($value));
        }

        $container = new ContainerBuilder();
        $container->set('ongr_admin.settings_manager', $manager);

        $controller = new SettingsManagerController();
        $controller->setContainer($container);

        $request = new Request([], [
            'value' => $value,
        ]);

        $response = $controller->setSettingAction($request, $name);
        $this->assertEquals($status, $response->getStatusCode());
    }

    /**
     * test if ng edit action works as expected
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
            ->with('fooname', 'domain')
            ->will($this->returnValue(new SettingModel()));

        $model = new SettingModel();
        $model->data['value'] = 'foobaz';

        $manager
            ->expects($this->once())
            ->method('save')
            ->with($model);

        $container = new ContainerBuilder();
        $container->set('ongr_admin.settings_manager', $manager);

        $controller = new SettingsManagerController();
        $controller->setContainer($container);

        $this->assertEquals(
            200,
            $controller->ngEditAction($request, 'fooname', 'domain')->getStatusCode()
        );
    }

    /**
     * test if getting 400 status code with wrong content data
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
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * data provider for testNgEditActionFail
     *
     * @return array
     */
    public function getNgEditActionFailData()
    {
        $out = [];

        //case #0: empty
        $out[] = [''];

        //case #1: non json
        $out[] = ["data" => "data"];

        //case #2: invalid json
        $out[] = [json_encode(['data' => 'wrong_data'])];

        return $out;
    }
}
