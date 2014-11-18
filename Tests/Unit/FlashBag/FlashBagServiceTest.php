<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Unit\FlashBag;

use ONGR\AdminBundle\FlashBag\DirtyFlashBag;

class FlashBagServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Check if service can be marked as dirty.
     */
    public function testDirty()
    {
        $service = new DirtyFlashBag();
        $service->setDirty();

        $this->assertTrue($service->isDirty());
    }

    /**
     * Check DirtyFlashBag::initialize marks service as dirty.
     */
    public function testInitialize()
    {
        $service = new DirtyFlashBag();
        $messages = [];
        $service->initialize($messages);

        $this->assertTrue($service->isDirty());
    }

    /**
     * Check DirtyFlashBag::add marks service as dirty.
     */
    public function testAdd()
    {
        $service = new DirtyFlashBag();
        $service->add('test', 'test');

        $this->assertTrue($service->isDirty());
    }

    /**
     * Check DirtyFlashBag::get doesn't mark service as dirty for non existent message.
     */
    public function testGet()
    {
        $service = new DirtyFlashBag();
        $service->get('test');

        $this->assertFalse($service->isDirty());
    }

    /**
     * Check DirtyFlashGag::get sets dirty for existent message.
     */
    public function testGetDirty()
    {
        $service = new DirtyFlashBag();
        $service->add('foo', 'baz');

        $service->get('foo');
        $this->assertTrue($service->isDirty());
    }

    /**
     * Check DirtyFlashBag::all marks service as dirty.
     */
    public function testAll()
    {
        $service = new DirtyFlashBag();
        $service->all();

        $this->assertTrue($service->isDirty());
    }

    /**
     * Check DirtyFlashBag::set marks service as dirty.
     */
    public function testSet()
    {
        $service = new DirtyFlashBag();
        $service->set('test', 'test');

        $this->assertTrue($service->isDirty());
    }

    /**
     * Check DirtyFlashBag::setAll marks service as dirty.
     */
    public function testSetAll()
    {
        $service = new DirtyFlashBag();
        $service->setAll(['test']);

        $this->assertTrue($service->isDirty());
    }
}
