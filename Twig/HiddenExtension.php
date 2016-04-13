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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class for outputting hidden information.
 */
class HiddenExtension extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;
        if ($container->has('request')) {
            try {
                $this->request = $container->get('request');
            } catch (\Exception $e) {

            }
        }
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'ongr_hidden',
                [$this, 'generate'],
                [
                    'needs_environment' => true,
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    /**
     * Generate twig view.
     *
     * @param \Twig_Environment $environment
     * @param array             $data
     * @param bool              $checkRequest
     *
     * @return string
     */
    public function generate($environment, $data, $checkRequest = false)
    {
        if ($checkRequest && ($this->request !== null)) {
            foreach ($data as $name => $value) {
                $requestVal = $this->request->get($name);
                if (!isset($requestVal) || empty($requestVal) || ($requestVal !== null)) {
                    unset($data[$name]);
                }
            }
        }

        return $environment->render(
            'ONGRSettingsBundle:Utils:hidden.html.twig',
            ['data' => $this->modify($data)]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ongr_hidden';
    }

    /**
     * Modifies data array for easier view rendering.
     *
     * @param array $data Data to modify.
     *
     * @return array
     */
    protected function modify($data)
    {
        $new = [];

        foreach ($data as $name => $value) {
            if (is_array($value)) {
                foreach ($value as $value2) {
                    if (!is_array($value2)) {
                        $new[] = [
                            'name' => $name . '[]',
                            'value' => $value2,
                        ];
                    }
                }
            } else {
                $new[] = [
                    'name' => $name,
                    'value' => $value,
                ];
            }
        }

        return $new;
    }
}
