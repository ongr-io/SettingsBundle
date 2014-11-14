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

namespace ONGR\AdminBundle\Tests\Functional\Model;

use ONGR\AdminBundle\Model\SettingModel;

class SettingModelTest extends \PHPUnit_Framework_TestCase
{

    /**
     * data provider for testJsonSerializable
     *
     * @return array
     */
    public function getJsonSerializableData()
    {
        $out = [];

        //case #0
        $model0 = new SettingModel();
        $model0->setDocumentId('s1');
        $model0->assign([
            'type' => SettingModel::TYPE_STRING,
            'domain' => 'default'
        ]);
        $expected0 = '{"_id":"s1","domain":"default","type":"string"}';
        $out[] = [$model0, $expected0];

        return $out;
    }

    /**
     * checks if jsonSerialize method is working properly
     *
     * @dataProvider getJsonSerializableData
     *
     * @param SettingModel $model
     * @param string $expected
     */
    public function testJsonSerializable($model, $expected)
    {
        $this->assertEquals($expected, json_encode($model));
    }
}
