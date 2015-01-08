<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Security\Authentication\Provider;

use ONGR\SettingsBundle\Security\Authentication\Cookie\SessionlessSignatureGenerator;
use ONGR\SettingsBundle\Security\Authentication\Token\SessionlessToken;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class responsible for authenticating or rejecting session-less security token.
 */
class SessionlessAuthenticationProvider implements AuthenticationProviderInterface
{
    /**
     * @var array
     */
    private $usersParameters;

    /**
     * @var array
     */
    private $settingsParameters;

    /**
     * @var SessionlessSignatureGenerator
     */
    private $generator;

    /**
     * @param SessionlessSignatureGenerator $generator
     * @param array                         $usersParameters
     * @param array                         $settingsParameters
     */
    public function __construct($generator, $usersParameters, $settingsParameters)
    {
        $this->usersParameters = $usersParameters;
        $this->settingsParameters = $settingsParameters;
        $this->generator = $generator;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(TokenInterface $token)
    {
        /** @var SessionlessToken $token */
        $signature = $this->generateSignature($token);

        if ($token->getExpirationTime() >= time() && $signature === $token->getSignature()) {
            $token->setAuthenticated(true);

            return $token;
        }

        throw new AuthenticationException('The Sessionless authentication failed.');
    }

    /**
     * {@inheritdoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof SessionlessToken;
    }

    /**
     * Check that username matches given password form settings array.
     *
     * @param string $username
     * @param string $password
     *
     * @return bool
     */
    public function matchUsernameAndPassword($username, $password)
    {
        $user = $this->getUserByName($username);

        if (!$user) {
            return false;
        }

        if (!isset($user['password']) || $user['password'] !== $password) {
            return false;
        }

        return true;
    }

    /**
     * User by name getter.
     *
     * @param string $username
     *
     * @return array|bool User settings array or false
     */
    private function getUserByName($username)
    {
        if (!isset($this->usersParameters[$username])) {
            return false;
        }

        $user = $this->usersParameters[$username];
        $user['username'] = $username;

        return $user;
    }

    /**
     * Generates user signature.
     *
     * @param SessionlessToken $token
     *
     * @return string
     */
    private function generateSignature(SessionlessToken $token)
    {
        $user = $this->getUserByName($token->getUsername());

        return $this->generator->generate(
            $token->getUsername(),
            $user['password'],
            (string)$token->getExpirationTime(),
            $token->getIpAddress()
        );
    }
}
