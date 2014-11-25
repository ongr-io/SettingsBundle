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

use Cocur\Slugify\Slugify;

/**
 * @deprecated Will be removed in 2.0 (use Twig filter provided by Cocur\Slugify instead)
 */
class SlugifyExtension extends \Twig_Extension
{
    /**
     * Extension name
     */
    const NAME = 'slugify_extension';

    /**
     * @var Slugify
     */
    protected $slugify;

    /**
     * @var array
     */
    protected $cache = [];

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        $functions = [];
        $functions[] = new \Twig_SimpleFunction('slugify', [$this, 'slugify']);

        return $functions;
    }

    /**
     * Generates slug
     * @param string $text
     * @param string $separator
     * @return string
     */
    public function slugify($text, $separator = '-')
    {
        $key = $text . $separator;
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $this->cache[$key] = $this->getSlugify()->slugify($text, $separator);

        return $this->cache[$key];
    }

    /**
     * @return Slugify
     */
    protected function getSlugify()
    {
        if ($this->slugify) {
            return $this->slugify;
        }

        $this->slugify = new Slugify(Slugify::MODEARRAY);

        return $this->slugify;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @param Slugify $slugify
     */
    public function setSlugify(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }
}
