<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Unit\Service;

use ONGR\AdminBundle\Service\UnderscoreEscaper;

class UnderscoreEscaperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test cases for testEscape.
     *
     * @return array
     */
    public function getEscapeCases()
    {
        $cases = $this->getBaseCases();

        return $cases;
    }

    /**
     * Tests escape.
     *
     * @param string $input
     * @param string $expectedOutput
     *
     * @dataProvider getEscapeCases
     */
    public function testEscape($input, $expectedOutput)
    {
        $output = UnderscoreEscaper::escape($input);
        $this->assertSame($expectedOutput, $output);
    }

    /**
     * Test cases for testUnescape.
     *
     * @return array
     */
    public function getUnescapeCases()
    {
        $cases = $this->getBaseCases();
        // Invalid unescaped strings should be still converted to something.
        $cases[] = ['foo-bar', 'foo-bar'];

        return $cases;
    }

    /**
     * Tests unsescape method.
     *
     * @param string $expectedOutput
     * @param string $input
     *
     * @dataProvider getUnescapeCases
     */
    public function testUnescape($expectedOutput, $input)
    {
        $output = UnderscoreEscaper::unescape($input);
        $this->assertSame($expectedOutput, $output);
    }

    /**
     * Cases for testing conversions back and forth.
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
