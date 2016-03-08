<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Unit\Document;

use ONGR\SettingsBundle\Document\Setting;

class SettingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Data provider for testJsonSerializable.
     *
     * @return array
     */
    public function getJsonSerializableData()
    {
        $out = [];

        // Case #0.
        $model0 = new Setting();
        $model0->setId('s1');
        $model0->setType(Setting::TYPE_STRING);
        $model0->setProfile('default');

        $expected0 = '{"name":null,"description":null,"profile":"default","type":"string",'
            . '"data":null,"id":"s1"}';
        $out[] = [$model0, $expected0];

        return $out;
    }

    /**
     * Checks if jsonSerialize method is working properly.
     *
     * @param Setting $model
     * @param string  $expected
     *
     * @dataProvider getJsonSerializableData
     */
    public function testJsonSerializable($model, $expected)
    {
        $this->assertEquals($expected, json_encode($model));
    }
}
