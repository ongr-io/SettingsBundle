<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Functional\Security\Authentication\Token;

use ONGR\AdminBundle\Security\Authentication\Token\SessionlessToken;

class SessionlessTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test getCredentials method.
     */
    public function testGetCredentials()
    {
        $token = new SessionlessToken('', 0, '', '');
        $this->assertSame('', $token->getCredentials());
    }
}
