<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Utils;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * ContainerAware trait.
 *
 * Use this, instead of \Symfony\Component\DependencyInjection\ContainerAwareTrait, because latter one is available
 * only in Symfony v2.4.
 */
trait ContainerAwareTrait
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Sets the Container associated with this Controller.
     *
     * @param ContainerInterface $container A ContainerInterface instance.
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
