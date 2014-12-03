<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Unit\Twig;

use ONGR\AdminBundle\Twig\WrapperExtension;

class WrapperExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test if extension has filters set
     */
    public function testHasFilters()
    {
        $extension = new WrapperExtension();

        $this->assertNotEmpty($extension->getFilters());
    }

    /**
     * name getter test
     */
    public function testGetName()
    {
        $extension = new WrapperExtension();

        $this->assertEquals('wrapper', $extension->getName());
    }

    /**
     * Test case when nothing need to changed
     */
    public function testNoChanges()
    {
        $extension = new WrapperExtension();

        $text = "This is test string";

        $keywords = [];

        $returned = $extension->wrap($text, $keywords);

        $expected = "This is test string";

        $this->assertEquals($expected, $returned);
    }

    /**
     * Testing case, when wrapping single occurence of string.
     */
    public function testSingleChange()
    {
        $extension = new WrapperExtension();

        $text = "This is test string";

        $keywords = ["test"];

        $returned = $extension->wrap($text, $keywords);

        $expected = "This is <strong>test</strong> string";

        $this->assertEquals($expected, $returned);
    }

    /**
     * Test if substring is wrapped correctly
     */
    public function testSubstringChange()
    {
        $extension = new WrapperExtension();

        $text = "This is test string";

        $keywords = ["is"];

        $returned = $extension->wrap($text, $keywords);

        $expected = "Th<strong>is</strong> <strong>is</strong> test string";

        $this->assertEquals($expected, $returned);
    }
}
