<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Functional\FlashBag;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests flash bag functionality.
 */
class FlashBagTest extends WebTestCase
{
    /**
     * If flash bag has been set.
     */
    public function testCookie()
    {
        $client = static::createClient();
        $client->request('POST', '/flash_bag');
        $client->request('GET', '/flash_bag');
        $this->assertJsonStringEqualsJsonString('{"main": ["posted"]}', $client->getResponse()->getContent());
    }

    /**
     * If flash bag has been set.
     */
    public function testNoCookie()
    {
        $client = static::createClient();
        $client->request('GET', '/flash_bag');
        $this->assertEquals('[]', $client->getResponse()->getContent());
    }
}
