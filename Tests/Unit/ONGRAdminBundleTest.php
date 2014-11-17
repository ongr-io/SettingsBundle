<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Functional;

use ONGR\AdminBundle\ONGRAdminBundle;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ONGRAdminBundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * List of passes, which should not be added to compiler.
     *
     * @var array
     */
    protected $passesBlacklist = [];

    /**
     * Check whether all Passes in DependencyInjection/Compiler/ are added to container.
     */
    public function testPassesRegistered()
    {
        $container = new ContainerBuilder();
        $bundle = new ONGRAdminBundle();
        $bundle->build($container);

        /** @var array $loadedPasses  Array of class names of loaded passes*/
        $loadedPasses = [];
        /** @var PassConfig $passConfig */
        $passConfig = $container->getCompiler()->getPassConfig();
        foreach ($passConfig->getPasses() as $pass) {
            $class = get_class($pass);
            $exploded = explode('\\', $class);
            $tmp = end($exploded);
            $loadedPasses[] = $tmp;
        }

        $files = new Filesystem();
        if (!$files->exists(__DIR__ . '/../../DependencyInjection/Compiler/')) {
            return;
        }

        $finder = new Finder();
        $finder->files()->in(__DIR__ . '/../../DependencyInjection/Compiler/');

        /** @var    $file   SplFileInfo */
        foreach ($finder as $file) {
            $passName = str_replace('.php', '', $file->getFilename());
            // Check whether pass is not blacklisted and not added by bundle.
            if (!in_array($passName, $this->passesBlacklist)) {
                $this->assertContains(
                    $passName,
                    $loadedPasses,
                    sprintf(
                        "Compiler pass '%s' is not added to container or not blacklisted in test.",
                        $passName
                    )
                );
            }
        }
    }
}
