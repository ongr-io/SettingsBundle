<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Unit\Document;

use ONGR\AdminBundle\Document\Parameter;

class ParameterTest extends \PHPUnit_Framework_TestCase
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
        $model0 = new Parameter();
        $model0->setId('ongr.test.parameter1');
        $model0->value = json_encode(['a' => 'b', 'c' => 3]);

        $expected0 = '{"value":"{\"a\":\"b\",\"c\":3}"'
            . ',"id":"ongr.test.parameter1","score":null,"parent":null,"ttl":null,"highlight":null}';
        $out[] = [$model0, $expected0];

        return $out;
    }

    /**
     * Checks if jsonSerialize method is working properly.
     *
     * @param Parameter $model
     * @param string    $expected
     *
     * @dataProvider getJsonSerializableData
     */
    public function testJsonSerializable($model, $expected)
    {
        $this->assertEquals($expected, json_encode($model));
    }
}
