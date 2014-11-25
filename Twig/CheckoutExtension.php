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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class CheckoutExtension extends \Twig_Extension
{

    /**
     * Extension name
     */
    const NAME = 'checkout_extension';

    /**
     * @var ContainerInterface
     */
    protected $container = null;

    /**
     * Checkout path
     * @var string
     */
    protected $path = null;

    /**
     * Force checkout path perform only on https mode
     * @var bool
     */
    protected $forceSecure = false;

    /**
     * Returns the name of the extension.
     * @return string The extension name
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @param ContainerInterface $container
     * @param string $path
     * @param bool $forceSecure
     */
    public function __construct($container, $path, $forceSecure = false)
    {
        $this->container = $container;
        $this->path = $path;
        $this->forceSecure = $forceSecure;
    }

    /**
     * @return array|void
     */
    public function getFunctions()
    {
        $out = [];

        $out['checkout_url'] = new \Twig_SimpleFunction('checkout_url', [$this, 'getCheckoutUrl']);

        return $out;
    }

    /**
     * Get url to checkout
     * @param string $path
     * @param array $query
     * @return string
     */
    public function getCheckoutUrl($path, $query = [])
    {
        $request = $this->getRequest();
        $path = join(
            '/',
            [
                $request->getHost(),
                $this->path,
                $path
            ]
        );

        $scheme = $request->getScheme();

        if ($this->forceSecure) {
            $scheme = 'https';
        }

        $url = $scheme.'://'.str_replace('//', '/', $path);

        if (count($query) > 0) {
            $url.= '?'.http_build_query($query);
        }

        return $url;
    }

    /**
     * @return Request
     */
    protected function getRequest()
    {
        return $this->container->get('request');
    }
}
