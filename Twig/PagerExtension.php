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

use ONGR\AdminBundle\Pager\Pager;
use Symfony\Component\Routing\RouterInterface;

/**
 * PagerExtension extends Twig with pagination capabilities.
 */
class PagerExtension extends \Twig_Extension
{
    /**
     * @var RouterInterface
     */
    protected $router;
    /**
     *
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheirtdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * {@inheirtdoc}
     */
    public function getFunctions()
    {
        return [
            'paginate' => new \Twig_Function_Method($this, 'paginate', ['is_safe' => ['html']]),
            'paginate_path' => new \Twig_Function_Method($this, 'path', ['is_safe' => ['html']]),
        ];
    }

    /**
     * Renders pagination element
     *
     * @param Pager $pager
     * @param string $route
     * @param array $parameters
     * @param string $template
     *
     * @return string
     */
    public function paginate(
        Pager $pager,
        $route,
        array $parameters = [],
        $template = 'ONGRAdminBundle:Pager:paginate.html.twig'
    ) {
        return $this->environment->render(
            $template,
            ['pager' => $pager, 'route' => $route, 'parameters' => $parameters]
        );
    }

    /**
     * Generates url to certain page
     *
     * @param string $route
     * @param string $page
     * @param array $parameters
     *
     * @return string
     */
    public function path($route, $page, array $parameters = [])
    {
        if (isset($parameters['_page'])) {

            // Do not include default values into parameters
            if ($page > 1) {
                $parameters[$parameters['_page']] = $page;
            }

            unset($parameters['_page']);
        } else {
            $parameters['page'] = $page;
        }

        // Do not include default values into parameters
        if ($page <= 1) {
            unset($parameters['page']);
        }

        return $this->router->generate($route, $parameters);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pager';
    }
}
