<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Tests\Functional\Security\Authentication\Cookie;

use ONGR\SettingsBundle\Security\Authentication\Cookie\SessionlessAuthenticationCookieService;

class SessionlessAuthenticationCookieServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SessionlessAuthenticationCookieService
     */
    private $service;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->service = new SessionlessAuthenticationCookieService(null, '', 0);
    }

    /**
     * Test cases for testValidateCookie.
     *
     * @return array.
     */
    public function getValidateCookieCases()
    {
        $cases = [];

        // Case 0: Parse fail.
        $cases[] = [false, false];
        // Case 1: Correct.
        $cases[] = [['username' => 'foo', 'expiration' => 13, 'signature' => 'a'], true];
        // Case 2: Missing username.
        $cases[] = [['expiration' => 13, 'signature' => 'a'], false];
        // Case 3: Invalid username.
        $cases[] = [['username' => '', 'expiration' => 13, 'signature' => 'a'], false];
        // Case 4: Expiration is a string.
        $cases[] = [['username' => 'foo', 'expiration' => '13', 'signature' => 'a'], false];
        // Case 5: Signature is not a string.
        $cases[] = [['username' => 'foo', 'expiration' => 13, 'signature' => true], false];

        return $cases;
    }

    /**
     * Validate that validation is correct.
     *
     * @param array $value
     * @param array $expectedResult
     *
     * @dataProvider getValidateCookieCases()
     */
    public function testValidateCookie($value, $expectedResult)
    {
        $this->assertSame($expectedResult, $this->service->validateCookie($value));
    }
}
