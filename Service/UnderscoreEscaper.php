<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Service;

/**
 * Handles string escaping and unescaping.
 *
 * E.g.
 *
 * www.example_profile.com -> www-2e-example_profile-2e-com
 */
class UnderscoreEscaper
{
    /**
     * Escapes any string so that it only contains characters [a-zA-Z0-9_-] .
     *
     * @param string $string
     *
     * @return string
     */
    public static function escape($string)
    {
        if ($string === null) {
            return null;
        }

        $string = preg_replace_callback(
            '/[^a-zA-Z0-9_]/u',
            function ($matches) {
                return '-' . bin2hex($matches[0]) . '-';
            },
            $string
        );

        return $string;
    }

    /**
     * Unescapes string so that it contains full range unicode (UTF-8) text.
     *
     * @param string $string
     *
     * @return string|null
     */
    public static function unescape($string)
    {
        if ($string === null) {
            return null;
        }

        $string = preg_replace_callback(
            '/-([0-9a-f]+)-/u',
            function ($matches) {
                return hex2bin($matches[1]);
            },
            $string
        );

        return $string;
    }
}
