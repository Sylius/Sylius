<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Service;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class ApiSecurityService implements SecurityServiceInterface
{
    private SharedStorageInterface $sharedStorage;

    private JWTTokenManagerInterface $jwtTokenManager;

    public function __construct(SharedStorageInterface $sharedStorage, JWTTokenManagerInterface $jwtTokenManager)
    {
        $this->sharedStorage = $sharedStorage;
        $this->jwtTokenManager = $jwtTokenManager;
    }

    public function logIn(UserInterface $user): void
    {
        $this->sharedStorage->set('token', $this->jwtTokenManager->create($user));
        $this->sharedStorage->set('user', $user);
    }

    public function logOut(): void
    {
        $this->sharedStorage->set('token', null);
        $this->sharedStorage->set('user', null);
    }

    public function getCurrentToken(): TokenInterface
    {
        $token = new JWTUserToken();
        $token->setRawToken($this->sharedStorage->get('token'));

        return $token;
    }

    public function restoreToken(TokenInterface $token): void
    {
        $this->sharedStorage->set('token', (string) $token);
    }
}
