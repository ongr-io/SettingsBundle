<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\UtilsBundle\Tests\Functional\Twig;

use ONGR\AdminBundle\Twig\EncryptionExtension;

/**
 * Provides tests for content extention.
 */
class EncryptionExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test getName function.
     */
    public function testGetName()
    {
        $extension = new EncryptionExtension();

        $this->assertEquals($extension->getName(), 'encryption_extension');
    }

    /**
     * Test getFunctions.
     */
    public function testGetFilters()
    {
        $extension = new EncryptionExtension();

        foreach ($extension->getFilters() as $function => $object) {
            $this->assertTrue(method_exists($extension, $function . 'Filter'));
        }
    }

    /**
     * Test base64EncodeFilter function.
     */
    public function testBase64EncodeFilter()
    {
        $string = 'teststring';
        $expected = base64_encode($string);
        $extension = new EncryptionExtension();
        $this->assertEquals($expected, $extension->base64EncodeFilter($string));
    }

    /**
     * Test base64DecodeFilter function.
     */
    public function testBase64DecodeFilter()
    {
        $string = 'teststring';
        $expected = base64_decode($string);
        $extension = new EncryptionExtension();
        $this->assertEquals($expected, $extension->base64DecodeFilter($string));
    }
}
