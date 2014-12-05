<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace ONGR\UtilsBundle\Tests\Functional\DependencyInjection;

use ONGR\AdminBundle\DependencyInjection\ONGRAdminExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * This class holds tests for extension loader.
 */
class ONGRAdminExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test if admin user parameters are being loaded.
     */
    public function testAdminUser()
    {
        $config = [
            'admin_user' => [
                'categories' => [
                    'test_category1' => [
                        'name' => 'test_name1',
                        'description' => 'test_desc1',
                    ]
                ],
                'settings' => [
                    'test_category1' => [
                        'name' => 'test_name2',
                        'description' => 'test_desc2',
                        'category' => 'test_category1',
                    ]
                ]
            ]
        ];

        $extension = new ONGRAdminExtension();
        $container = new ContainerBuilder();
        $extension->load(['ongr-admin' => $config], $container);

        $this->assertTrue($container->hasParameter('ongr_admin.settings.categories'));
        $this->assertTrue($container->hasParameter('ongr_admin.settings.settings'));
    }

}
