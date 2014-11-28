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

/**
 * Class WrapperExtension.
 *
 * @package ONGR\AdminBundle\Twig
 */
class WrapperExtension extends \Twig_Extension
{
    /**
     * Returns list of new Twig functions.
     *
     * @return array
     */
    public function getFilters()
    {
        $functions[] = new \Twig_SimpleFilter('wrapKeywords', [$this, 'wrap']);

        return $functions;
    }

    /**
     * Wraps keywords with prefix and suffix in given text.
     *
     * @param string $text
     * @param array  $keywords
     * @param string $prefix
     * @param string $suffix
     *
     * @return string
     */
    public function wrap($text, $keywords, $prefix = '<strong>', $suffix = '</strong>')
    {
        $result = '';
        $lowerCaseText = strtolower($text);

        $explodedText = explode(' ', $text);
        $explodedLowerCaseText = explode(' ', $lowerCaseText);

        end($explodedLowerCaseText);
        $lastElementKey = key($explodedLowerCaseText);

        foreach ($explodedLowerCaseText as $key => $word) {
            if (in_array($word, $keywords)) {
                // Keyword matches entire word.
                $result .= $prefix . $explodedText[$key] . $suffix;
            } else {
                foreach ($keywords as $keyword) {
                    $startPosition = strpos($word, $keyword);
                    if ($startPosition !== false) {
                        // Keyword matches only a part of word.
                        $wordStart = substr($explodedText[$key], 0, $startPosition);
                        $wordMiddle = substr($explodedText[$key], $startPosition, strlen($keyword));
                        $wordEnd = substr($explodedText[$key], $startPosition + strlen($keyword));

                        $result .= $wordStart . $prefix . $wordMiddle . $suffix . $wordEnd;

                        if ($lastElementKey !== $key) {
                            $result .= ' ';
                        }

                        continue 2;
                    }
                }

                // If not continued before, add not changed word.
                $result .= $explodedText[$key];
            }

            if ($lastElementKey !== $key) {
                $result .= ' ';
            }
        }

        return $result;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'wrapper';
    }
}
