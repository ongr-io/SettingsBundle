<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Twig;

/**
 * Class for encrypting string with base64.
 */
class EncryptionExtension extends \Twig_Extension
{
    const NAME = 'encryption_extension';

    /**
     * Gets filter settings.
     *
     * @return array
     */
    public function getFilters()
    {
        return [
            'base64Encode' => new \Twig_Filter_Method($this, 'base64EncodeFilter'),
        ];
    }

    /**
     * Encodes string to base64.
     *
     * @param string $str
     *
     * @return string
     */
    public function base64EncodeFilter($str)
    {
        return base64_encode($str);
    }

    /**
     * Decodes string from base64.
     *
     * @param string $str
     *
     * @return string
     */
    public function base64DecodeFilter($str)
    {
        return base64_decode($str);
    }

    /**
     * Get name of the twig extension.
     *
     * @return string
     */
    public function getName()
    {
        $name = self::NAME;

        return $name;
    }
}
