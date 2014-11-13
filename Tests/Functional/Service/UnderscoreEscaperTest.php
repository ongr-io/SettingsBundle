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

namespace Fox\AdminBundle\Tests\Functional\Service;

use Fox\AdminBundle\Service\UnderscoreEscaper;

class UnderscoreEscaperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test cases for testEscape
     *
     * @return array
     */
    public function getEscapeCases()
    {
        $cases = $this->getBaseCases();

        return $cases;
    }

    /**
     * @dataProvider getEscapeCases
     *
     * @param $input
     * @param $expectedOutput
     */
    public function testEscape($input, $expectedOutput)
    {
        $output = UnderscoreEscaper::escape($input);
        $this->assertSame($expectedOutput, $output);
    }

    /**
     * Test cases for testUnescape
     *
     * @return array
     */
    public function getUnescapeCases()
    {
        $cases = $this->getBaseCases();
        $cases[] = ['foo-bar', 'foo-bar']; // Invalid unescaped strings should be still converted to something

        return $cases;
    }

    /**
     * @dataProvider getUnescapeCases
     *
     * @param $expectedOutput
     * @param $input
     */
    public function testUnescape($expectedOutput, $input)
    {
        $output = UnderscoreEscaper::unescape($input);
        $this->assertSame($expectedOutput, $output);
    }

    /**
     * Cases for testing conversions back and forth
     *
     * @return array
     */
    protected function getBaseCases()
    {
        $cases = [];

        $cases[] = [null, null];
        $cases[] = ['', ''];
        $cases[] = ['foo', 'foo'];
        $cases[] = ['foo_bar', 'foo_bar'];
        $cases[] = ['foo.bar', 'foo-2e-bar'];
        $cases[] = ['foo.bar.baz', 'foo-2e-bar-2e-baz'];
        $cases[] = ['foo-bar', 'foo-2d-bar'];
        $cases[] = ['jäger', 'j-c3a4-ger'];

        return $cases;
    }
}
