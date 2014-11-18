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

namespace ONGR\AdminBundle\Security\Authentication\Provider;

use ONGR\AdminBundle\Security\Authentication\Cookie\SessionlessSignatureGenerator;
use ONGR\AdminBundle\Security\Authentication\Token\SessionlessToken;
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
     * @param array $usersParameters
     * @param array $settingsParameters
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
     * @param string $username
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
     * @param SessionlessToken $token
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
