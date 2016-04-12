<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Unit\Settings\Personal;

use ONGR\SettingsBundle\Settings\Personal\SettingsStructure;

class SettingsStructureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test setStructure method.
     */
    public function testSetStructure()
    {
        $service = new SettingsStructure([], []);
        $service->setStructure(['foo' => []]);
        $this->assertEquals(
            ['foo' => ['stash' => 'ongr_settings']],
            $service->getStructure()
        );
    }

    /**
     * Test setCategoriesStructure method.
     */
    public function testSetCategoriesStructure()
    {
        $service = new SettingsStructure([], []);
        $service->setCategoriesStructure(['foo']);
        $this->assertEquals(['foo'], $service->getCategoriesStructure());
    }
}
