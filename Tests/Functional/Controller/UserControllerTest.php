<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Functional\Controller;

use ONGR\AdminBundle\Tests\Functional\CookieTestHelper;
use ONGR\AdminBundle\Tests\Fixtures\Security\LoginTestHelper;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Tests for UserController.
 */
class UserControllerTest extends WebTestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var LoginTestHelper
     */
    private $loginHelper;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->client = self::createClient();
        $this->loginHelper = new LoginTestHelper($this->client);
    }

    /**
     * Cases for testLoginAction.
     *
     * @return array.
     */
    public function getLoginActionCases()
    {
        // Case 0: given correct credentials, login.
        $cases[] = ['test', 'test', true];

        // Case 1: given incorrect password, no login.
        $cases[] = ['foo_user', 'foo_Password', false];

        // Case 2: given incorrect username, no login.
        $cases[] = ['foo_bad', 'foo_password', false];

        return $cases;
    }

    /**
     * Test if user can login.
     *
     * @param string $username
     * @param string $password
     * @param bool   $shouldSucceed
     *
     * @dataProvider getLoginActionCases()
     */
    public function testLoginAction($username, $password, $shouldSucceed)
    {
        $client = $this->loginHelper->loginAction($username, $password);
        $response = $client->getResponse();

        if ($shouldSucceed) {
            $this->assertContains(' already logged in', $response->getContent());

             // Assert correct cookie has been set.
            /** @var Cookie $cookie */
            $cookie = $client->getCookieJar()->get('ongr_admin_user_auth');
            $this->assertSame('ongr_admin_user_auth', $cookie->getName());
            $this->assertSame('test', json_decode($cookie->getValue(), true)['username']);
        } else {
            // Assert not a redirect.
            $this->assertTrue($response->isOk(), 'Response must not be a redirect');
        }
    }

    /**
     * Test cases for testLogoutAction.
     *
     * @return array
     */
    public function getLogoutTestCases()
    {
        // Case #0: when logged in, should be logged out.
        $cases[] = [true];

        // Case #1: when logged out, should not complain and keep same behaviour.
        $cases[] = [false];

        return $cases;
    }

    /**
     * Test logging out action.
     *
     * @param bool $shouldLogin
     *
     * @dataProvider getLogoutTestCases()
     */
    public function testLogoutAction($shouldLogin)
    {
        $client = $this->loginHelper->loginAction('test', 'test');
        if ($shouldLogin) {
            // Set authentication cookie.
            $this->assertSame('ongr_admin_user_auth', $client->getCookieJar()->get('ongr_admin_user_auth')->getName());
        } else {
            // Ensure no cookie set.
            $client = $this->loginHelper->logoutAction($client);
            $this->assertSame('MOCKSESSID', $client->getCookieJar()->all()[0]->getName());
        }

        // Visit logout page.
        $client->request('GET', '/admin/logout');

        // Assert successful redirect.
        $this->assertSame('/', $client->getRequest()->getRequestUri());
    }

    /**
     * If user is already logged-in, check that form is not displayed.
     */
    public function testLoginActionWhenLoggedIn()
    {
        $client = $this->loginHelper->loginAction('test', 'test');

        // Visit login page.
        $crawler = $client->request('GET', '/admin/login');

        // Assert content contains message.
        $response = $client->getResponse();
        $this->assertContains('already logged in', $response->getContent());

        // Assert there is no form.
        $buttonNode = $crawler->selectButton('login_submit');
        $this->assertSame(0, $buttonNode->count(), 'There should be no form');
    }

    /**
     * Cookie should no be accepted if it has been tampered with.
     */
    public function testCookieTamper()
    {
        list($cookie, $value) = $this->setAuthCookieAndReturn();
        $value['expiration'] = $value['expiration'] + 1;
        $this->setCookieAndAssertFail($value, $cookie);
    }

    /**
     * Cookie should no be accepted if it is invalid.
     */
    public function testCookieInvalid()
    {
        list($cookie, $value) = $this->setAuthCookieAndReturn();
        $value['expiration'] = 'invalid';
        $this->setCookieAndAssertFail($value, $cookie);
    }

    /**
     * Cookie should not be accepted if it has expired.
     */
    public function testCookieExpiration()
    {
        // Set old authentication cookie.
        CookieTestHelper::setAuthenticationCookie($this->client, time() - 360 * 24 * 3600);

        // Visit login page.
        $crawler = $this->client->request('GET', '/admin/login');

        // Assert there is a form.
        $buttonNode = $crawler->selectButton('login_submit');
        $this->assertSame(1, $buttonNode->count(), 'There should be a form');
    }

    /**
     * @return array
     */
    private function setAuthCookieAndReturn()
    {
        // Set authentication cookie.
        $this->client = $this->loginHelper->loginAction('test', 'test');

        // Get cookie value.
        $cookie = $this->client->getCookieJar()->get('ongr_admin_user_auth');
        $valueJson = $cookie->getValue();
        $value = json_decode($valueJson, true);

        return [$cookie, $value];
    }

    /**
     * @param mixed  $cookieValue
     * @param Cookie $oldCookie
     */
    private function setCookieAndAssertFail($cookieValue, $oldCookie)
    {
        // Set cookie.
        $newCookie = new \Symfony\Component\BrowserKit\Cookie(
            'ongr_admin_user_auth',
            json_encode(
                $cookieValue
            ),
            $oldCookie->getExpiresTime(),
            $oldCookie->getPath(),
            $oldCookie->getDomain()
        );
        $this->client->getCookieJar()->set($newCookie);

        // Visit login page.
        $crawler = $this->client->request('GET', '/admin/login');

        // Assert there is a form.
        $buttonNode = $crawler->selectButton('login_submit');
        $this->assertSame(1, $buttonNode->count(), 'There should be a form');
    }
}
