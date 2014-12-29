<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Security\Authentication\Provider;

use ONGR\AdminBundle\Security\Authentication\Cookie\SessionlessSignatureGenerator;
use ONGR\AdminBundle\Security\Authentication\Token\SessionlessToken;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;


/**
 * Class responsible for authenticating or rejecting session-less security token.
 */
class SessionlessAuthenticationProvider implements AuthenticationProviderInterface
{
    /**
     * @var array
     */
    private $usersProvider;

    /**
     * @var SessionlessSignatureGenerator
     */
    private $generator;

	/**
	 * @param SessionlessSignatureGenerator $generator
	 * @param UserProviderInterface         $usersProvider
	 */
    public function __construct($generator, $usersProvider)
    {
        $this->generator = $generator;
        $this->usersProvider = $usersProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(TokenInterface $token)
    {
        /** @var SessionlessToken $token */
        $signature = $this->generateSignature($token);

        if ($token->getExpirationTime() >= time() && $signature === $token->getSignature()) {

			$user = $this->usersProvider->loadUserByUsername($token->getUsername());

			//Prepares new token, that represents authenticated user
			$authenticatedToken = new SessionlessToken(
					$token->getUsername(),
					$token->getExpirationTime(),
					$token->getIpAddress(),
					$token->getSignature(),
					$user->getRoles()
			);

			$authenticatedToken->setAuthenticated(true);
			$authenticatedToken->setUser($user);

            return $authenticatedToken;
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

		$user =  $this->getUser($username);

        if (!$user) {
            return false;
        }

        $dbPassword = $user->getPassword();
        if (!isset( $dbPassword ) || $dbPassword !== $password) {
            return false;
        }

        return true;
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
		$user = $this->getUser($token->getUsername());

		return $this->generator->generate(
			$token->getUsername(),
			$user->getPassword(),
			(string)$token->getExpirationTime(),
			$token->getIpAddress()
		);
	}

	/**
	 * User object getter with exception catcher.
	 *
	 * @param $username
	 *
	 * @return object
	 */
	private function getUser($username)
	{
		try {
			$user = $this->usersProvider->loadUserByUsername($username);
		} catch (Exception $e) {

		}

		return $user;
	}
}
