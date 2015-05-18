<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Functional\Twig;

use ONGR\SettingsBundle\Twig\ImagePathExtension;

class ImagePathExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string Cdn url.
     */
    protected $cdnUrl = 'http://ongr.dev';

    /**
     * @var string Prefixes.
     */
    protected $prefix = '/cdn';

    /**
     * @var array Presets config.
     */
    protected $config = [
        'presets' => [
            'product' => [
                'type' => 'product',
                'path' => '373x250',
                'parts' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 'twelve'],
            ],
            'product_preview' => [
                'type' => 'product',
                'path' => '798x534',
                'parts' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 'twelve'],
            ],
            'category' => [
                'type' => 'category',
                'path' => '200x100',
            ],
        ],
        'mapping' => [
            'product' => [
                1 => 'pic1',
                2 => 'pic2',
                3 => 'pic3',
                4 => 'pic4',
                5 => 'pic5',
                6 => 'pic6',
                7 => 'pic7',
                8 => 'pic8',
                9 => 'pic9',
                10 => 'pic10',
                11 => 'pic11',
                'twelve' => ['path' => '12', 'field' => 'pic12'],
            ],
            'category' => [],
        ],
    ];

    /**
     * Test get image path.
     *
     * @param array  $expected
     * @param string $image
     * @param string $preset
     * @param string $part
     *
     * @covers \ONGR\SettingsBundle\Twig\ImagePathExtension::getImagePath()
     *
     * @dataProvider getTestData
     */
    public function testGetImagePath(array $expected, $image, $preset, $part = null)
    {
        $extension = new ImagePathExtension($this->cdnUrl, $this->config, $this->prefix);

        $this->assertEquals(
            $expected['path'],
            $extension->getImagePath($image, $preset, $part)
        );
    }

    /**
     * Tests get image path exception preset.
     *
     * @covers \ONGR\SettingsBundle\Twig\ImagePathExtension::getImagePath()
     *
     * @expectedException \Exception
     * @expectedExceptionMessage preset '__fake_preset__' config not found
     */
    public function testGetImagePathExceptionPreset()
    {
        $extension = new ImagePathExtension($this->cdnUrl, $this->config, $this->prefix);

        $extension->getImagePath('ART005_spoon.jpg', '__fake_preset__');
    }

    /**
     * Tests get image path part.
     *
     * @covers \ONGR\SettingsBundle\Twig\ImagePathExtension::getImagePath()
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Part '__fake_part__' was not found
     */
    public function testGetImagePathExceptionPart()
    {
        $extension = new ImagePathExtension($this->cdnUrl, $this->config, $this->prefix);

        $extension->getImagePath('ART005_spoon.jpg', 'product', '__fake_part__');
    }

    /**
     * Test get image url.
     *
     * @param array  $expected
     * @param string $image
     * @param string $preset
     * @param string $part
     *
     * @covers \ONGR\SettingsBundle\Twig\ImagePathExtension::getImageUrl()
     * @covers \ONGR\SettingsBundle\Twig\ImagePathExtension::getImagePath()
     *
     * @dataProvider getTestData
     */
    public function testGetImageUrl(array $expected, $image, $preset, $part = null)
    {
        $extension = new ImagePathExtension($this->cdnUrl, $this->config, $this->prefix);

        $this->assertEquals(
            $expected['url'],
            $extension->getImageUrl($image, $preset, $part)
        );
    }

    /**
     * Test data provider.
     *
     * @return array
     */
    public function getTestData()
    {
        return [
            [
                [
                    'path' => $this->prefix . '/product/1/AA000_knife_set.jpg',
                    'url' => $this->cdnUrl . $this->prefix . '/product/1/AA000_knife_set.jpg',
                ],
                'AA000_knife_set.jpg',
                'product',
                '1',
            ],
            [
                [
                    'path' => $this->prefix . '/product/5/AA002_knife_set.jpg',
                    'url' => $this->cdnUrl . $this->prefix . '/product/5/AA002_knife_set.jpg',
                ],
                'AA002_knife_set.jpg',
                'product',
                '5',
            ],
            [
                [
                    'path' => $this->prefix . '/product_preview/5/AA002_knife_set.jpg',
                    'url' => $this->cdnUrl . $this->prefix . '/product_preview/5/AA002_knife_set.jpg',
                ],
                'AA002_knife_set.jpg',
                'product_preview',
                '5',
            ],
            [
                [
                    'path' => $this->prefix . '/category/CAT001_kitchen.jpg',
                    'url' => $this->cdnUrl . $this->prefix . '/category/CAT001_kitchen.jpg',
                ],
                'CAT001_kitchen.jpg',
                'category',
            ],
            [
                [
                    'path' => $this->prefix . '/product/12/BB000_knife_set.jpg',
                    'url' => $this->cdnUrl . $this->prefix . '/product/12/BB000_knife_set.jpg',
                ],
                'BB000_knife_set.jpg',
                'product',
                'twelve',
            ],
        ];
    }

    /**
     * Expected filters getter.
     *
     * @return array
     */
    public function getExpectedFunctions()
    {
        return [
            ['imagePath'],
            ['imageUrl'],
        ];
    }

    /**
     * Tet get functions.
     *
     * @param string $function
     *
     * @dataProvider getExpectedFunctions()
     */
    public function testGetFunctions($function)
    {
        $extension = new ImagePathExtension($this->cdnUrl, $this->config, $this->prefix);

        $exists = false;
        foreach ($extension->getFunctions() as $filterObject) {
            if ($filterObject->getName() == $function) {
                $exists = true;
                $this->assertTrue(is_callable($filterObject->getCallable()));
                break;
            }
        }

        $this->assertTrue($exists);
    }

    /**
     * Function test.
     */
    public function testGetName()
    {
        $extension = new ImagePathExtension($this->cdnUrl, $this->config, $this->prefix);

        $this->assertEquals('image_path_extension', $extension->getName());
    }

    /**
     * Test image path without image name.
     */
    public function testImagePathWithoutImage()
    {
        $extension = new ImagePathExtension($this->cdnUrl, $this->config, $this->prefix);

        $path = $extension->getImagePath('', 'id');
        $this->assertEquals('/etc/nopic.jpg', $path);
    }
}
