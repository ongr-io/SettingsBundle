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

namespace ONGR\AdminBundle\Service;

/**
 * Handles string escaping and unescaping
 *
 * E.g.
 *
 * www.example_domain.com -> www-2e-example_domain-2e-com
 */
class UnderscoreEscaper
{
    /**
     * Escapes any string so that it only contains characters [a-zA-Z0-9_-]
     *
     * @param string $string
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
     * Unescapes string so that it contains full range unicode (UTF-8) text
     *
     * @param $string
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
