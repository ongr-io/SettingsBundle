<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Integration\Controller;

use ONGR\AdminBundle\Tests\Functional\CookieTestHelper;
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
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->client = self::createClient();
    }

    /**
     * Cases for testLoginAction.
     *
     * @return array.
     */
    public function getLoginActionCases()
    {
        $cases = [];

        // Case 0: given correct credentials, login.
        $cases[] = ['foo_user', 'foo_password', true];

        // Case 1: given incorrect password, do not redirect.
        $cases[] = ['foo_user', 'foo_Password', false];

        // Case 2: given incorrect username, do not redirect.
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
        // Visit login page.
        $crawler = $this->client->request('GET', '/admin/login');

        // Submit login form.
        $buttonNode = $crawler->selectButton('login_submit');
        $form = $buttonNode->form();
        $form['login[username]'] = $username;
        $form['login[password]'] = $password;
        $this->client->submit($form);
        $response = $this->client->getResponse();

        if ($shouldSucceed) {
            // Assert successful redirect.
            $this->assertSame('/admin/login', $response->headers->get('location'));

            // Assert correct cookie has been set.
            /** @var Cookie $cookie */
            $cookie = $response->headers->getCookies()[0];
            $this->assertSame('ongr_admin_user_auth', $cookie->getName());
            $this->assertSame('foo_user', json_decode($cookie->getValue(), true)['username']);
        } else {
            // Assert not a redirect.
            $this->assertTrue($response->isOk(), 'Response must not be a redirect');
        }
    }

    /**
     * If user is already logged-in, check that form is not displayed.
     */
    public function testLoginActionWhenLoggedIn()
    {
        // Set authentication cookie.
        CookieTestHelper::setAuthenticationCookie($this->client);

        // Visit login page.
        $crawler = $this->client->request('GET', '/admin/login');

        // Assert content contains message.
        $response = $this->client->getResponse();
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

        $value['expiration'] = 'invalid_value';
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
     * Test cases for testLogoutAction.
     *
     * @return array
     */
    public function getLogoutTestCases()
    {
        $cases = [];

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
        if ($shouldLogin) {
            // Set authentication cookie.
            CookieTestHelper::setAuthenticationCookie($this->client);
            $this->assertSame(1, count($this->client->getCookieJar()->all()));
        } else {
            // Ensure no cookie set.
            $this->assertSame(0, count($this->client->getCookieJar()->all()));
        }

        // Visit logout page.
        $this->client->request('GET', '/admin/logout');

        // Assert successful redirect.
        $response = $this->client->getResponse();
        $this->assertSame('/admin/login', $response->headers->get('location'));

        // Assert cookie has been cleared.
        $this->assertSame(0, count($this->client->getCookieJar()->all()));
    }

    /**
     * @return array
     */
    private function setAuthCookieAndReturn()
    {
        // Set authentication cookie.
        CookieTestHelper::setAuthenticationCookie($this->client);

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
