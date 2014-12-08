<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Functional\Security\Authentication\Provider;

use ONGR\AdminBundle\Security\Authentication\Provider\SessionlessAuthenticationProvider;
use ONGR\AdminBundle\Security\Authentication\Token\SessionlessToken;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

class SessionlessAuthenticationProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SessionlessAuthenticationProvider
     */
    private $provider;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->provider = new SessionlessAuthenticationProvider(null, [], []);
    }

    /**
     * Test supports method.
     */
    public function testSupports()
    {
        $this->assertFalse($this->provider->supports(new AnonymousToken('', '')));
        $this->assertTrue($this->provider->supports(new SessionlessToken('', 0, '', '')));
    }
}
