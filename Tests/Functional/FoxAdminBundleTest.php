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

namespace ONGR\AdminBundle\Tests\Functional;

use ONGR\AdminBundle\FoxAdminBundle;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * This class check if we register compiler passes correctly
 */
class FoxAdminBundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * List of passes, which should not be added to compiler
     *
     * @var array
     */
    protected $passesBlacklist = [];

    /**
     * Check whether all Passes in DependencyInjection/Compiler/ are added to container
     */
    public function testPassesRegistered()
    {
        $container = new ContainerBuilder();
        $bundle = new FoxAdminBundle();
        $bundle->build($container);

        /** @var array $loadedPasses  Array of class names of loaded passes*/
        $loadedPasses = [];
        /** @var PassConfig $passConfig */
        $passConfig = $container->getCompiler()->getPassConfig();
        foreach ($passConfig->getPasses() as $pass) {
            $class = explode("\\", get_class($pass));
            $loadedPasses[] = end($class);
        }

        $dir = __DIR__ . "/../../DependencyInjection/Compiler/";

        $fileSystem = new Filesystem();

        if (!$fileSystem->exists($dir)) {
            return;
        }

        $finder = new Finder();
        $finder->files()->in($dir);

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $passName = str_replace(".php", "", $file->getFilename());
            // check whether pass is not blacklisted and not added by bundle
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
