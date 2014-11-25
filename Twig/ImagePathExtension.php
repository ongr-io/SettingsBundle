<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Twig;

class ImagePathExtension extends \Twig_Extension
{
    /**
     * Extension name
     */
    const NAME = 'image_path_extension';

    /**
     * Image to show if picture name is empty
     */
    const NO_PIC_FILE = '/etc/nopic.jpg';

    /**
     * @var string CDN URL
     */
    protected $cdnUrl;

    /**
     * @var array Image presets config
     */
    protected $config;

    /**
     * @var string Image cache prefix
     */
    protected $prefix;

    /**
     * Constructor
     *
     * @param string $cdnUrl CDN URL
     * @param array  $config Image presets config
     * @param string $prefix URL prefix
     */
    public function __construct($cdnUrl, $config, $prefix)
    {
        $this->cdnUrl = $cdnUrl;
        $this->config = $config;
        $this->prefix = $prefix;
    }

    /**
     * Provide a list of helper functions to be used
     *
     * @return array
     */
    public function getFunctions()
    {
        $functions = [];
        $functions[] = new \Twig_SimpleFunction('imagePath', [$this, 'getImagePath']);
        $functions[] = new \Twig_SimpleFunction('imageUrl', [$this, 'getImageUrl']);

        return $functions;
    }

    /**
     * Returns path to given image
     *
     * @param string $image  Image name
     * @param string $preset Preset ID
     * @param string $part   [Optional] Subdirectory
     *
     * @return string
     * @throws \Exception
     */
    public function getImagePath($image, $preset, $part = null)
    {
        if (empty($image)) {
            return self::NO_PIC_FILE;
        }

        if (!isset($this->config['presets'][$preset])) {
            throw new \Exception("Image preset '{$preset}' config not found!");
        }

        $options = $this->config['presets'][$preset];
        $suffix = '';

        if ($part !== null) {
            if (!in_array($part, $options['parts'])) {
                throw new \Exception("Part '{$part}' was not found in preset '{$preset}'!");
            } elseif (is_array($this->config['mapping'][$options['type']][$part])) {
                $part = $this->config['mapping'][$options['type']][$part]['path'];
            }

            $suffix = "/{$part}";
        }

        return "{$this->prefix}/{$preset}{$suffix}/{$image}";
    }

    /**
     * Returns full URL to given image
     *
     * @param string $image  Image name
     * @param string $preset Preset ID
     * @param string $part   [Optional] Subdirectory
     *
     * @return string
     */
    public function getImageUrl($image, $preset, $part = null)
    {
        return $this->cdnUrl . $this->getImagePath($image, $preset, $part);
    }

    /**
     * Returns name of the extension
     *
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }
}
